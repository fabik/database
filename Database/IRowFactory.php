<?php

namespace Fabik\Database;



/**
 * Factory for rows.
 *
 * @author     Jan-Sebastian Fabík
 */
interface IRowFactory
{
	/**
	 * Creates a new row.
	 * @param  mixed[]
	 * @param  \Nette\Database\Table\Selection
	 * @return \Nette\Database\Table\ActiveRow
	 */
	function createRow(array $data, \Nette\Database\Table\Selection $table);
}
