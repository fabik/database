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
				articles: App\Model\Database\Article
				users: App\Model\Database\User

	services:
		articleDao: App\Model\Database\Articles
		userDao: App\Model\Database\Users
	```

4. Create interfaces for entities and data access objects.

	```php
	namespace App\Model;



	/**
	 * @property int $id
	 * @property string $title
	 * @property string $content
	 * @property IUser $author
	 */
	interface IArticle
	{
	}



	/**
	 * @property int $id
	 * @property string $username
	 * @property string $password
	 * @property string $email
	 * @property string $firstname
	 * @property string $surname
	 * @property string $realname
	 * @property-read IArticle[]|\Traversable $authoredArticles
	 */
	interface IUser
	{
	}



	interface IArticleDao
	{
		/**
		 * @return IArticle[]|\Traversable
		 */
		function findAll();

		/**
		 * @param  string
		 * @return IArticle[]|\Traversable
		 */
		function search($query);

		/**
		 * @param  int
		 * @return IArticle|NULL
		 */
		function find($id);

		/**
		 * @param  array
		 * @return IArticle
		 */
		function create($values);

		/**
		 * @param  IArticle|array
		 * @return IArticle
		 */
		function save($article);
	}



	interface IUserDao
	{
		/**
		 * @return IUser[]|\Traversable
		 */
		function findAll();

		/**
		 * @param  int
		 * @return IUser|NULL
		 */
		function find($id);

		/**
		 * @param  string
		 * @return IUser|NULL
		 */
		function findOneByEmail($email);

		/**
		 * @param  array
		 * @return IUser
		 */
		function create($values);

		/**
		 * @param  IUser|array
		 * @return IUser
		 */
		function save($user);
	}
	```

5. Create classes for rows and tables that implement these interfaces.

	```php
	namespace App\Model\Database;

	use App\Model\IArticle,
		App\Model\IArticleDao,
		App\Model\IUser,
		App\Model\IUserDao,
		Fabik\Database\ActiveRow,
		Fabik\Database\Table,
		Nette\Database\SqlLiteral;



	class Article extends ActiveRow implements IArticle
	{
		/** @return User */
		public function getAuthor()
		{
			return $this->ref('users', 'author_id');
		}



		/** @param \App\Model\IUser */
		public function setAuthor(IUser $user)
		{
			$this->author_id = $user->id;
		}
	}



	class User extends ActiveRow implements IUser
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



		/** @return Article[]|\Traversable */
		public function getAuthoredArticles()
		{
			return $this->related('articles', 'author_id');
		}
	}



	class Articles extends Table implements IArticleDao
	{
		protected $name = 'articles';



		/**
		 * @param  string
		 * @return IArticle[]|\Traversable
		 */
		public function search($query)
		{
			$pattern = $this->manager->getConnection()->getSupplementalDriver()->formatLike($query, 0);
			return $this->findBy('title LIKE ?', new SqlLiteral($pattern));
		}
	}



	class Users extends Table implements IUserDao
	{
		protected $name = 'users';



		/**
		 * @param  string
		 * @return IUser|NULL
		 */
		public function findOneByEmail($email)
		{
			return $this->findOneBy('email', $email);
		}
	}
	```

6. Now you can use it the following way:

	```php
	namespace App\FrontModule;

	use App\Model\IArticleDao;



	class ArticlesPresenter extends BasePresenter
	{
		/** @var \App\Model\IArticleDao */
		protected $articleDao;



		public function inject(IArticleDao $articleDao)
		{
			$this->articleDao = $articleDao;
		}



		public function renderDefault()
		{
			$this->template->articles = $this->articleDao->findAll();
		}
	}
	```

	```
	{#content}

	{foreach $articles as $article}
		<h1>{$article->title}</h1>
		<em>by {$article->author->realname}</em>
		{!$article->content}
	{/foreach}
	```

It is not recommended using the row and table classes directly, so that the entire application does not depend on the implementation of model. Thus it is anytime possible to replace the storage with another one (e.g. a web service or a different database library).
