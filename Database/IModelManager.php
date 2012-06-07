<?php

namespace Database;



/**
 * Model manager.
 *
 * @author     Jan-Sebastian Fabík
 */
interface IModelManager
{
	/** @return Nette\Database\Connection */
	function getConnection();

	/** @return IRowFactory */
	function getRowFactory();
}
