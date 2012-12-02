<?php

namespace Fabik\Database;

use Nette\InvalidStateException,
	Nette\Object;



/**
 * Representation of table.
 *
 * @author     Jan-Sebastian Fabík
 */
abstract class Table extends Object
{
	/** @var IModelManager */
	protected $manager;



	/**
	 * @param  IModelManager
	 */
	public function __construct(IModelManager $manager)
	{
		if (!isset($this->name)) {
			$class = get_called_class();
			throw new InvalidStateException("Property \$name must be defined in $class.");
		}
		$this->manager = $manager;
	}



	/**
	 * @return Selection
	 */
	protected function getTable()
	{
		return new Selection($this->name, $this->manager);
	}



	/**
	 * Finds all rows.
	 * @return Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}



	/**
	 * Finds rows with the given properties.
	 * @param  string
	 * @param  mixed...
	 * @return Selection
	 */
	public function findBy($where)
	{
		$args = func_get_args();
		return callback($this->findAll(), 'where')->invokeArgs($args);
	}



	/**
	 * Finds a row by the given properties.
	 * @param  string
	 * @param  mixed...
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function findOneBy($where)
	{
		$args = func_get_args();
		return callback($this, 'findBy')->invokeArgs($args)->limit(1)->fetch();
	}



	/**
	 * Finds a row by the primary key.
	 * @param  mixed
	 * @return \Nette\Database\Table\ActiveRow|FALSE
	 */
	public function find($key)
	{
		return $this->getTable()->get($key);
	}



	/**
	 * Creates and inserts a row to the database.
	 * @param  mixed[]
	 * @return \Nette\Database\Table\ActiveRow
	 * @throws DuplicateEntryException
	 */
	public function create($values)
	{
		try {
			return $this->getTable()->insert($values);

		} catch (\PDOException $e) {
			if ($e->getCode() == 23000) { // intentionally ==
				throw new DuplicateEntryException($e->getMessage(), NULL, $e);

			} else {
				throw $e;
			}
		}
	}



	/**
	 * Creates and inserts a row to the database or updates an existing one.
	 * @param  mixed[]
	 * @param  mixed[]
	 * @return \Nette\Database\Table\ActiveRow
	 */
	public function createOrUpdate($uniqueKeys, $values = array())
	{
		if ($values) {
			if ($row = $this->findOneBy($uniqueKeys)) {
				$row->update($values);
				return $this->findOneBy($uniqueKeys);
			} else {
				return $this->create($uniqueKeys + $values);
			}

		} else {
			$connection = $this->manager->getConnection();
			$driver = $connection->getSupplementalDriver();

			$pairs = array();
			foreach ($uniqueKeys as $key => $value) {
				$pairs[] = $driver->delimite($key) . ' = ?';
			}

			$table = $driver->delimite($this->name);
			$pairs = implode(', ', $pairs);
			$values = array_values($uniqueKeys);

			$connection->queryArgs(
				"INSERT INTO $table SET $pairs ON DUPLICATE KEY UPDATE $pairs",
				array_merge($values, $values)
			);

			return $this->findOneBy($uniqueKeys);
		}
	}



	/**
	 * Inserts a row to the database.
	 * @param  mixed[]
	 * @return void
	 */
	public function insert($row)
	{
		$this->insertAll(array($row));
	}



	/**
	 * Inserts rows to the database.
	 * @param  mixed[][]
	 * @return void
	 */
	public function insertAll($rows)
	{
		$connection = $this->manager->getConnection();
		$driver = $connection->getSupplementalDriver();
		$connection->query('INSERT INTO ' . $driver->delimite($this->name), $rows);
	}



	/**
	 * Replaces a row in the database.
	 * @param  mixed[]
	 * @return void
	 */
	public function replace($row)
	{
		$this->replaceAll(array($row));
	}



	/**
	 * Replaces rows in the database.
	 * @param  mixed[][]
	 * @return void
	 */
	public function replaceAll($rows)
	{
		$connection = $this->manager->getConnection();
		$driver = $connection->getSupplementalDriver();
		$connection->query('REPLACE INTO ' . $driver->delimite($this->name), $rows);
	}
}
