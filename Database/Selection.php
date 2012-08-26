<?php

namespace Database;



/**
 * Filtered table representation.
 *
 * @author     Jan-Sebastian Fabík
 */
class Selection extends \Nette\Database\Table\Selection implements IModelManagerAccessor
{
	/** @var IModelManager */
	protected $manager;



	/**
	 * @param  string
	 * @param  IModelManager
	 */
	public function __construct($table, IModelManager $manager)
	{
		parent::__construct($manager->getConnection(), $table);
		$this->manager = $manager;
	}



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
	protected function createSelection($table)
	{
		return new Selection($table, $this->manager);
	}



	/**
	 * Creates a new grouped selection.
	 * @param  string
	 * @param  string
	 * @return Nette\Database\Table\GroupedSelection
	 */
	protected function createGroupedSelection($table, $column)
	{
		return new GroupedSelection($table, $this, $column);
	}



	/********** Nette\Database\Table\Selection behaviour **********/



	/**
	 * Returns referencing rows.
	 * @param  string
	 * @param  string
	 * @param  int  primary key
	 * @param  bool force new instance
	 * @return Nette\Database\Table\GroupedSelection
	 */
	public function getReferencingTable($table, $column, $active = NULL, $forceNewInstance = FALSE)
	{
		$referencing = & $this->referencing["$table:$column"];
		if (!$referencing || $forceNewInstance) {
			$referencing = $this->createGroupedSelection($table, $column); // HACK
		}

		$this->execute(); // HACK
		return $referencing->setActive($active)->where("$table.$column", array_keys((array) $this->data)); // HACK
	}



	/**
	 * Inserts row in a table.
	 * @param  mixed array($column => $value)|Traversable for single row insert or Nette\Database\Table\Selection|string for INSERT ... SELECT
	 * @return Nette\Database\Table\ActiveRow or FALSE in case of an error or number of affected rows for INSERT ... SELECT
	 */
	public function insert($data)
	{
		if ($data instanceof \Nette\Database\Table\Selection) {
			$data = $data->getSql();

		} elseif ($data instanceof \Traversable) {
			$data = iterator_to_array($data);
		}

		$return = $this->connection->query("INSERT INTO $this->delimitedName", $data);

		if (!is_array($data)) {
			return $return->rowCount();
		}

		$this->checkReferenceNewKeys = TRUE;

		if (!isset($data[$this->primary]) && ($id = $this->connection->lastInsertId())) {
			$data[$this->primary] = $id;
			return $this->rows[$id] = $this->createRow($data); // HACK

		} else {
			return $this->createRow($data); // HACK

		}
	}



	/**
	 * Returns referenced row.
	 * @param  string
	 * @param  string
	 * @param  bool  checks if rows contains the same primary value relations
	 * @return Nette\Database\Table\Selection or array() if the row does not exist
	 */
	public function getReferencedTable($table, $column, $checkReferenceNewKeys = FALSE)
	{
		$referenced = & $this->referenced["$table.$column"];
		if ($referenced === NULL || $checkReferenceNewKeys || $this->checkReferenceNewKeys) {
			$keys = array();
			$this->execute();
			foreach ($this->rows as $row) {
				if ($row[$column] === NULL)
					continue;

				$key = $row[$column] instanceof \Nette\Database\Table\ActiveRow ? $row[$column]->getPrimary() : $row[$column];
				$keys[$key] = TRUE;
			}

			if ($referenced !== NULL && $keys === array_keys($this->rows)) {
				$this->checkReferenceNewKeys = FALSE;
				return $referenced;
			}

			if ($keys) {
				$referenced = $this->createSelection($table); // HACK
				$referenced->where($table . '.' . $referenced->primary, array_keys($keys));
			} else {
				$referenced = array();
			}
		}

		return $referenced;
	}
}
