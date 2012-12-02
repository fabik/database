<?php

namespace Fabik\Database;



/**
 * Model manager.
 *
 * @author     Jan-Sebastian Fabík
 */
interface IModelManager
{
	/** @return \Nette\Database\Connection */
	function getConnection();

	/** @return IRowFactory */
	function getRowFactory();

	/** @return \Nette\Database\IReflection */
	function getDatabaseReflection();

	/** @return \Nette\Caching\IStorage */
	function getCacheStorage();
}
