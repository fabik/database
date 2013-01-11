fabik/database
==============

This is a database layer for Nette Framework based on `Nette\Database`.

## Installation

Get the source code using [Composer](http://getcomposer.org/) (add `"fabik/database": "1.1.*"` to your `composer.json`) or directly download `Database` to your libs directory.

## Example of use

1. Create the database:

	```mysql
	CREATE TABLE `users` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`username` varchar(255) NOT NULL,
		`password` char(40) NOT NULL,
		`email` varchar(255) NOT NULL,
		`firstname` varchar(255) NOT NULL,
		`surname` varchar(255) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY (`username`)
	) ENGINE=InnoDB;

	CREATE TABLE `articles` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`content` longtext NOT NULL,
		`author_id` int unsigned NOT NULL,
		PRIMARY KEY (`id`),
		KEY (`author_id`),
		FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
	) ENGINE=InnoDB;
	```

2. Register the compiler extension in the bootstrapper.

	```php
	use Fabik\Database\DatabaseExtension;

	$configurator->onCompile[] = function($configurator, $compiler) {
		$compiler->addExtension('database', new DatabaseExtension);
	};
	```

3. Add the following sections to your `config.neon` file:

	```neon
	nette:
		database:
			default:
				dsn: '%database.driver%:host=%database.host%;dbname=%database.dbname%'
				user: %database.user%
				password: %database.password%

	database:
		rowFactory:
			classes:
				articles: Blog\Article
				users: Blog\User

	services:
		articles: Blog\Articles
		users: Blog\Users
	```

4. Create classes for rows (e.g. `Article`, `User`) and tables (e.g. `Articles`, `Users`):

	```php
	<?php

	namespace Blog;

	use Fabik\Database\ActiveRow,
		Fabik\Database\Table;



	class Article extends ActiveRow
	{
	}



	class User extends ActiveRow
	{
		/** @return string */
		public function getRealname()
		{
			return "$this->firstname $this->surname";
		}



		/** @param string */
		public function setRealname($realname)
		{
			list($this->firstname, $this->surname) = explode(' ', $realname);
		}
	}



	class Articles extends Table
	{
		protected $name = 'articles';
	}



	class Users extends Table
	{
		protected $name = 'users';
	}
	```

5. Now you can use it as follows:

	```php
	$articles = $container->articles;

	foreach ($articles->findAll() as $article) {
		echo "$article->title was written by $article->author->realname\n";
	}
	```
