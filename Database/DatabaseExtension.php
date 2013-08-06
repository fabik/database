<?php

namespace Fabik\Database;

use Nette\DI\CompilerExtension;



/**
 * Compiler extension for the database library.
 *
 * @author     Jan-Sebastian Fabík
 */
class DatabaseExtension extends CompilerExtension
{
	public $defaults = array(
		'rowFactory' => array(
			'classes' => array(), // [table name => class name]
			'defaultClass' => 'Fabik\Database\ActiveRow',
		),
	);



	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('modelManager'))
			->setClass('Fabik\Database\ModelManager');

		$builder->addDefinition($this->prefix('rowFactory'))
			->setClass('Fabik\Database\RowFactory', array($config['rowFactory']['classes'], $config['rowFactory']['defaultClass']));
	}
}
