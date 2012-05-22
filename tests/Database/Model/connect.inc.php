<?php

$connection = new Database\Model\Connection('mysql:host=localhost;dbname=test', 'root');

$connection->setCacheStorage(new Nette\Caching\Storages\DevNullStorage);

$connection->setDatabaseReflection(new Nette\Database\Reflection\DiscoveredReflection(new Nette\Caching\Storages\DevNullStorage));

$connection->setRowClasses(array(
	'articles' => 'Blog\Article',
	'users' => 'Blog\User',
));

Nette\Database\Helpers::loadFromFile($connection, __DIR__ . '/files/schema.sql');
