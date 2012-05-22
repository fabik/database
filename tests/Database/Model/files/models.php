<?php

namespace Blog;

use Database\Model\ActiveRow;
use Database\Model\Table;



/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $author_id
 * @property-read \Blog\User $author
 */
class Article extends ActiveRow
{
}



/**
 * @method \Blog\Article[]|\Database\Model\Selection findAll()
 * @method \Blog\Article[]|\Database\Model\Selection findBy(array $by)
 * @method \Blog\Article|FALSE find($key)
 * @method \Blog\Article|FALSE findOneBy(array $by)
 */
class Articles extends Table
{
}



/**
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $firstname
 * @property string $surname
 * @property string $role
 * @property string $realName
 * @property-read \Blog\Article[]|\Database\Model\Selection $articles
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
	public function setRealName($realname)
	{
		list($this->firstname, $this->surname) = explode(' ', $realname);
	}



	/**
	 * Gets authored articles.
	 * @return \Blog\Article[]|\Database\Model\Selection
	 */
	public function getArticles()
	{
		return $this->related('article');
	}
}



/**
 * @method \Blog\User[]|\Database\Model\Selection findAll()
 * @method \Blog\User[]|\Database\Model\Selection findBy(array $by)
 * @method \Blog\User|FALSE find($key)
 * @method \Blog\User|FALSE findOneBy(array $by)
 */
class Users extends Table
{
}
