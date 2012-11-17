<?php

/**
 * Database layer for Nette Framework based on Nette\Database.
 *
 * @author     Jan-Sebastian Fabík
 */



spl_autoload_register(function ($type) {
	static $paths = array(
		'database\activerow' => 'ActiveRow.php',
		'database\duplicateentryexception' => 'exceptions.php',
		'database\groupedselection' => 'GroupedSelection.php',
		'database\imodelmanager' => 'IModelManager.php',
		'database\imodelmanageraccessor' => 'IModelManagerAccessor.php',
		'database\irowfactory' => 'IRowFactory.php',
		'database\modelmanager' => 'ModelManager.php',
		'database\rowfactory' => 'RowFactory.php',
		'database\selection' => 'Selection.php',
		'database\table' => 'Table.php',
	);

	$type = ltrim(strtolower($type), '\\'); // PHP namespace bug #49143

	if (isset($paths[$type])) {
		require_once __DIR__ . '/' . $paths[$type];
	}
});
