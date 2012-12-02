<?php

namespace Fabik\Database;

use Nette\Object;



/**
 * Factory for rows.
 *
 * @author     Jan-Sebastian Fabík
 */
class RowFactory extends Object implements IRowFactory
{
	/** @var string[] */
	protected $classes;

	/** @var string */
	protected $defaultClass;



	/**
	 * @param  string[]
	 * @param  string
	 */
	public function __construct($classes = array(), $defaultClass = 'Fabik\Database\ActiveRow')
	{
		$this->classes = $classes;
		$this->defaultClass = $defaultClass;
	}



	/**
	 * Creates a new row.
	 * @param  mixed[]
	 * @param  \Nette\Database\Table\Selection
	 * @return \Nette\Database\Table\ActiveRow
	 */
	public function createRow(array $data, \Nette\Database\Table\Selection $table)
	{
		$class = $this->getRowClass($table->getName());
		return new $class($data, $table);
	}



	/**
	 * Gets name of row class for the given table.
	 * @param  string
	 * @return string
	 */
	public function getRowClass($table)
	{
		return isset($this->classes[$table]) ? $this->classes[$table] : $this->defaultClass;
	}
}
