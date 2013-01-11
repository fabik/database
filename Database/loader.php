<?php

/**
 * Database layer for Nette Framework based on Nette\Database.
 *
 * @author     Jan-Sebastian Fabík
 */



spl_autoload_register(function ($type) {
	static $paths = array(
		'fabik\database\activerow' => 'ActiveRow.php',
		'fabik\database\databaseextension' => 'DatabaseExtension.php',
		'fabik\database\duplicateentryexception' => 'exceptions.php',
		'fabik\database\groupedselection' => 'GroupedSelection.php',
		'fabik\database\imodelmanager' => 'IModelManager.php',
		'fabik\database\imodelmanageraccessor' => 'IModelManagerAccessor.php',
		'fabik\database\irowfactory' => 'IRowFactory.php',
		'fabik\database\modelmanager' => 'ModelManager.php',
		'fabik\database\rowfactory' => 'RowFactory.php',
		'fabik\database\selection' => 'Selection.php',
		'fabik\database\table' => 'Table.php',
	);

	$type = ltrim(strtolower($type), '\\'); // PHP namespace bug #49143

	if (isset($paths[$type])) {
		require_once __DIR__ . '/' . $paths[$type];
	}
});
