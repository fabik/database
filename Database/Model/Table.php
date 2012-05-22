<?php

namespace Database\Model;

use Nette\Object;
use Nette\InvalidStateException;
use Nette\NotSupportedException;



/**
 * Representation of table.
 *
 * @author     Jan-Sebastian Fabík
 */
abstract class Table extends Object implements \ArrayAccess
{
	/** @var string */
	protected $name;

	/** @param \Database\Model\Connection */
	protected $connection;



	/**
	 * @param  string
	 * @param  \Database\Model\Connection
	 * @throws \Nette\InvalidStateException
	 */
	public function __construct($name, Connection $connection)
	{
		$this->name = $name;
		$this->connection = $connection;
	}



	/**
	 * Creates selector for the table.
	 * @return \Database\Model\Selection
	 */
	protected function getTable()
	{
		return $this->connection->table($this->name);
	}



	/**
	 * Finds all rows.
	 * @return \Database\Model\ActiveRow[]|\Database\Model\Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}



	/**
	 * Finds rows with the given properties.
	 * @param  mixed[] $by
	 * @return \Database\Model\ActiveRow[]|\Database\Model\Selection
	 */
	public function findBy(array $by)
	{
		return $this->findAll()->where($by);
	}



	/**
	 * Finds a row by the given properties.
	 * @param  mixed[] $by
	 * @return \Database\Model\ActiveRow|FALSE
	 */
	public function findOneBy(array $by)
	{
		return $this->findBy($by)->limit(1)->fetch();
	}



	/**
	 * Finds a row by the primary key.
	 * @param  mixed $key
	 * @return \Database\Model\ActiveRow|FALSE
	 */
	public function find($key)
	{
		return $this->getTable()->find($key)->fetch();
	}



	/**
	 * Creates and inserts new row to database.
	 * @param  mixed[] $values
	 * @return \Database\Model\ActiveRow
	 * @throws \Database\Model\DuplicateEntryException
	 */
	public function create(array $values)
	{
		try {
			return $this->getTable()->insert($values);

		} catch (\PDOException $e) {
			if ((int) $e->getCode() === 23000) {
				throw new DuplicateEntryException($e->getMessage(), NULL, $e);
			} else {
				throw $e;
			}
		}
	}



	/********************* interface ArrayAccess *********************/



	/**
	 * Mimic row.
	 * @param  string row ID
	 * @param  ActiveRow
	 * @return ActiveRow
	 */
	public function offsetSet($key, $value)
	{
		throw new NotSupportedException;
	}



	/**
	 * Returns specified row.
	 * @param  string row ID
	 * @return ActiveRow or NULL if there is no such row
	 */
	public function offsetGet($key)
	{
		return $this->find($key);
	}



	/**
	 * Tests if row exists.
	 * @param  string row ID
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return (bool) count($this->getTable()->find($key));
	}



	/**
	 * Removes row from result set.
	 * @param  string row ID
	 * @return NULL
	 */
	public function offsetUnset($key)
	{
		$this->getTable()->find($key)->delete();
	}
}
