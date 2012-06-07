<?php

namespace Database;
use Nette\Database\Connection,
	Nette\Object;



/**
 * Model manager.
 *
 * @author     Jan-Sebastian Fabík
 */
class ModelManager extends Object implements IModelManager
{
	/** @param Nette\Database\Connection */
	protected $connection;

	/** @param IRowFactory */
	protected $rowFactory;



	/**
	 * @param  Nette\Database\Connection
	 * @param  IRowFactory
	 */
	public function __construct(Connection $connection, IRowFactory $rowFactory)
	{
		$this->connection = $connection;
		$this->rowFactory = $rowFactory;
	}



	/** @return Nette\Database\Connection */
	public function getConnection()
	{
		return $this->connection;
	}



	/** @return IRowFactory */
	public function getRowFactory()
	{
		return $this->rowFactory;
	}
}
