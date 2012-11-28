<?php

namespace Fabik\Database;



/**
 * Model manager accessor.
 *
 * @author     Jan-Sebastian Fabík
 */
interface IModelManagerAccessor
{
	/** @return IModelManager */
	function getModelManager();
}
