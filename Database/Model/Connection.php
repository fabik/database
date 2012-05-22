<?php

namespace Database\Model;



/**
 * Represents a connection between PHP and a database server.
 *
 * @author     Jan-Sebastian Fabík
 */
class Connection extends \Nette\Database\Connection
{
	/** @var string[] */
	protected $rowClasses;

	/** @var string */
	protected $defaultRowClass = 'Database\Model\ActiveRow';



	/**
	 * Sets row classes definition.
	 * @param  string[]
	 * @return \Database\Model\Connection provides a fluent interface
	 */
	public function setRowClasses(array $rowClasses)
	{
		$this->rowClasses = $rowClasses;
		return $this;
	}



	/**
	 * Creates selector for table.
	 * @param  string
	 * @return \Database\Model\Selection
	 */
	public function table($name)
	{
		return new Selection($name, $this);
	}



	/**
	 * Gets name of row class for the given table.
	 * @param  string
	 * @return string
	 */
	public function getRowClass($table)
	{
		return isset($this->rowClasses[$table]) ? $this->rowClasses[$table] : $this->defaultRowClass;
	}
}
