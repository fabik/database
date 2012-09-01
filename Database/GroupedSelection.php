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
}
