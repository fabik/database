<?php

namespace Fabik\Database;

use Nette\Caching\IStorage,
	Nette\Database\Connection,
	Nette\Database\IReflection,
	Nette\Database\Reflection\DiscoveredReflection,
	Nette\Object;



/**
 * Model manager.
 *
 * @author     Jan-Sebastian Fabík
 */
class ModelManager extends Object implements IModelManager
{
	/** @param \Nette\Database\Connection */
	protected $connection;

	/** @param IRowFactory */
	protected $rowFactory;

	/** @param \Nette\Database\IReflection */
	protected $reflection;

	/** @param \Nette\Caching\IStorage */
	protected $cacheStorage;



	/**
	 * @param  \Nette\Database\Connection
	 * @param  IRowFactory
	 * @param  \Nette\Database\IReflection|NULL
	 * @param  \Nette\Caching\IStorage
	 */
	public function __construct(Connection $connection, IRowFactory $rowFactory, IReflection $reflection = NULL, IStorage $cacheStorage)
	{
		$this->connection = $connection;
		$this->rowFactory = $rowFactory;
		$this->reflection = $reflection ?: new DiscoveredReflection($connection, $cacheStorage);
		$this->cacheStorage = $cacheStorage;
	}



	/** @return \Nette\Database\Connection */
	public function getConnection()
	{
		return $this->connection;
	}



	/** @return IRowFactory */
	public function getRowFactory()
	{
		return $this->rowFactory;
	}



	/** @return \Nette\Database\IReflection */
	public function getDatabaseReflection()
	{
		return $this->reflection;
	}



	/** @return \Nette\Caching\IStorage */
	public function getCacheStorage()
	{
		return $this->cacheStorage;
	}
}
