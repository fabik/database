<?php

namespace Database;



/**
 * Representation of filtered table grouped by some column.
 *
 * @author     Jan-Sebastian Fabík
 */
class GroupedSelection extends \Nette\Database\Table\GroupedSelection implements IModelManagerAccessor
{
	/** @var IModelManager */
	protected $manager;



	/**
	 * @param  Nette\Database\Table\Selection
	 * @param  string
	 * @param  string
	 */
	public function __construct(\Nette\Database\Table\Selection $refTable, $name, $column)
	{
		parent::__construct($refTable, $name, $column);
		if (!$refTable instanceof IModelManagerAccessor) {
			throw new \Nette\InvalidArgumentException('Argument $refTable must be a IModelManagerAccessor descendant.');
		}
		$this->manager = $this->refTable->getModelManager();
	}



	/********** Database\Selection behaviour **********/



	/** @return IModelManager */
	public function getModelManager()
	{
		return $this->manager;
	}



	/**
	 * Creates a new row.
	 * @param  mixed[]
	 * @return Nette\Database\Table\ActiveRow
	 */
	protected function createRow(array $data)
	{
		return $this->manager->getRowFactory()->createRow($data, $this);
	}



	/**
	 * Creates a selection.
	 * @param  string
	 * @return Nette\Database\Table\Selection
	 */
	protected function createSelectionInstance($table = NULL)
	{
		return new Selection($table ?: $this->name, $this->manager);
	}



	/**
	 * Creates a new grouped selection.
	 * @param  string
	 * @param  string
	 * @return Nette\Database\Table\GroupedSelection
	 */
	protected function createGroupedSelectionInstance($table, $column)
	{
		return new GroupedSelection($this, $table, $column);
	}



	/********** Nette\Database\Table\GroupedSelection behaviour **********/



	protected function execute()
	{
		if ($this->rows !== NULL) {
			return;
		}

		$hash = md5($this->sqlBuilder->getSql() . json_encode($this->sqlBuilder->getParameters()));

		$referencing = & $this->getRefTable($refPath)->referencing[$refPath . $hash];
		$this->rows = & $referencing['rows'];
		$this->referenced = & $referencing['refs'];
		$this->accessed = & $referencing['accessed'];
		$refData = & $referencing['data'];

		if ($refData === NULL) {
			$limit = $this->sqlBuilder->getLimit();
			$this->refTable->execute(); // HACK
			$rows = count($this->refTable->data); // HACK
			if ($limit && $rows > 1) {
				$this->sqlBuilder->limit(NULL, NULL);
			}
			$this->_execute(); // HACK
			$this->sqlBuilder->limit($limit, NULL);
			$refData = array();
			$offset = array();
			foreach ($this->rows as $key => $row) {
				$ref = & $refData[$row[$this->column]];
				$skip = & $offset[$row[$this->column]];
				if ($limit === NULL || $rows <= 1 || (count($ref) < $limit && $skip >= $this->sqlBuilder->getOffset())) {
					$ref[$key] = $row;
				} else {
					unset($this->rows[$key]);
				}
				$skip++;
				unset($ref, $skip);
			}
		}

		$this->data = & $refData[$this->active];
		if ($this->data === NULL) {
			$this->data = array();
		} else {
			foreach ($this->data as $row) {
				$row->setTable($this); // injects correct parent GroupedSelection
			}
			reset($this->data);
		}
	}



	/********** Nette\Database\Table\Selection behaviour **********/



	/**
	 * Returns referencing rows.
	 * @param  string
	 * @param  string
	 * @param  int primary key
	 * @return Nette\Database\Table\GroupedSelection
	 */
	public function getReferencingTable($table, $column, $active = NULL)
	{
		$prototype = & $this->getRefTable($refPath)->referencingPrototype[$refPath . "$table.$column"];
		if (!$prototype) {
			$prototype = $this->createGroupedSelectionInstance($table, $column);
			$this->execute(); // HACK
			$prototype->where("$table.$column", array_keys((array) $this->data)); // HACK
		}

		$clone = clone $prototype;
		$clone->setActive($active);
		return $clone;
	}



	/**
	 * Executes built query.
	 * @return void
	 */
	protected function _execute()
	{
		if ($this->rows !== NULL) {
			return;
		}

		$this->observeCache = TRUE;

		try {
			$result = $this->query($this->sqlBuilder->getSql());

		} catch (\PDOException $exception) {
			if (!$this->sqlBuilder->getSelect() && $this->prevAccessed) {
				$this->prevAccessed = '';
				$this->accessed = array();
				$result = $this->query($this->sqlBuilder->getSql());
			} else {
				throw $exception;
			}
		}

		$this->rows = array();
		$result->setFetchMode(\PDO::FETCH_ASSOC);
		foreach ($result as $key => $row) {
			$row = $result->normalizeRow($row);
			$this->rows[isset($row[$this->primary]) ? $row[$this->primary] : $key] = $this->createRow($row);
		}
		$this->data = $this->rows;

		if (isset($row[$this->primary]) && !is_string($this->accessed)) {
			$this->accessed[$this->primary] = TRUE;
		}
	}
}
