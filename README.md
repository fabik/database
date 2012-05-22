Database
========

This is a database layer for Nette Framework based on `Nette\Database`.

## Installation

Get the source code:
* Copy `Database` to your libs directory.
* Add `"fabik/database": "0.1.*"` to your `composer.json`.

## Example of use

1. Create the database:

	```mysql
	SET foreign_key_checks = 0;

	CREATE TABLE `articles` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`content` longtext NOT NULL,
		`author_id` int unsigned NOT NULL,
		PRIMARY KEY (`id`),
		KEY (`author_id`),
		FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB;

	CREATE TABLE `users` (
		`id` int unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`password` char(40) NOT NULL,
		`email` varchar(255) NOT NULL,
		`firstname` varchar(255) NOT NULL,
		`surname` varchar(255) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY (`name`)
	) ENGINE=InnoDB;
	```

2. Add the following sections to your `config.neon` file:

	```neon
	parameters:
		database:
			dsn: 'mysql:host=localhost;dbname=test'
			user: user
			password: password

	services:
		connection:
			class: Database\Model\Connection(%database.dsn%, %database.user%, %database.password%)
			setup:
				- setCacheStorage(@cacheStorage)
				- setDatabaseReflection(Nette\Database\Reflection\DiscoveredReflection(@cacheStorage))
				- setRowClasses({
					articles: Blog\Article,
					users: Blog\User
				})

		articles: Blog\Articles(articles, @connection)
		users: Blog\Users(users, @connection)
	```

4. Create classes for rows (e.g. `Article`, `User`) and tables (e.g. `Articles`, `Users`):

	```php
	<?php

	namespace Blog;

	use Database\Model\ActiveRow;



	/**
	 * @property int $id
	 * @property string $title
	 * @property string $content
	 * @property int $author_id
	 * @property User $author
	 */
	class Article extends ActiveRow
	{
	}

	```

	```php
	<?php

	namespace Blog;

	use Database\Model\Table;



	/**
	 * @method Article[]|\Database\Model\Selection findAll()
	 * @method Article[]|\Database\Model\Selection findBy(array $by)
	 * @method Article[]|\Database\Model\Selection|FALSE find($key)
	 * @method Article[]|\Database\Model\Selection|FALSE findOneBy(array $by)
	 */
	class Articles extends Table
	{
	}

	```

	```php
	<?php

	namespace Blog;

	use Database\Model\ActiveRow;



	/**
	 * @property int $id
	 * @property string $name
	 * @property string $password
	 * @property string $firstname
	 * @property string $surname
	 * @property string $role
	 * @property string $realName
	 */
	class User extends ActiveRow
	{
		/**
		 * Gets the real name.
		 * @return string
		 */
		public function getRealName()
		{
			return "$this->firstname $this->surname";
		}



		/**
		 * Sets the real name.
		 * @param string
		 * @return void
		 */
		public function setRealName($realName)
		{
			list($this->firstname, $this->surname) = explode(' ', $realName);
		}
	}

	```

	```php
	<?php

	namespace Blog;

	use Database\Model\Table;



	/**
	 * @method User[]|\Database\Model\Selection findAll()
	 * @method User[]|\Database\Model\Selection findBy(array $by)
	 * @method User[]|\Database\Model\Selection|FALSE find($key)
	 * @method User[]|\Database\Model\Selection|FALSE findOneBy(array $by)
	 */
	class Users extends Table
	{
	}

	```

5. Now you can use it as follows:

	```php
	foreach ($container->articles->findAll() as $article) {
		echo "$article->title was written by $article->author->realName\n";
	}
	```
