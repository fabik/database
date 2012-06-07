<?php

namespace Database;
use Nette\Object;



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
	 * @throws Nette\InvalidStateException
	 */
	public function __construct(IModelManager $manager)
	{
		if (!isset($this->name)) {
			$class = get_called_class();
			throw new \Nette\InvalidStateException("Property \$name must be defined in $class.");
		}
		$this->manager = $manager;
	}



	/** @return Selection */
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
	 * @return ActiveRow|FALSE
	 */
	public function findOneBy($where)
	{
		$args = func_get_args();
		return callback($this, 'findBy')->invokeArgs($args)->limit(1)->fetch();
	}



	/**
	 * Finds a row by the primary key.
	 * @param  mixed
	 * @return ActiveRow|FALSE
	 */
	public function find($key)
	{
		return $this->getTable()->find($key)->fetch();
	}



	/**
	 * Creates and inserts a row to the database.
	 * @param  mixed[]
	 * @return ActiveRow
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
