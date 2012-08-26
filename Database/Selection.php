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
	protected function createSelectionInstance($table = NULL)
	{
		return new Selection($table, $this->manager);
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
			$referencing = $this->createGroupedSelectionInstance($table, $column); // HACK
		}

		$this->execute(); // HACK
		return $referencing->setActive($active)->where("$table.$column", array_keys((array) $this->data)); // HACK
	}
}
