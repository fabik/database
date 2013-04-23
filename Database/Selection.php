<?php

namespace Fabik\Database;



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
		if (NETTE_VERSION_ID >= 20100) {
			parent::__construct($manager->getConnection(), $table, $manager->getDatabaseReflection(), $manager->getCacheStorage());
		} else {
			parent::__construct($table, $manager->getConnection());
		}
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
	 * @return \Nette\Database\Table\ActiveRow
	 */
	protected function createRow(array $data)
	{
		return $this->manager->getRowFactory()->createRow($data, $this);
	}



	/**
	 * Creates a selection.
	 * @param  string
	 * @return Selection
	 */
	public function createSelectionInstance($table = NULL)
	{
		return new Selection($table ?: $this->name, $this->manager);
	}



	/**
	 * Creates a new grouped selection.
	 * @param  string
	 * @param  string
	 * @return GroupedSelection
	 */
	protected function createGroupedSelectionInstance($table, $column)
	{
		return new GroupedSelection($this, $table, $column);
	}
}
