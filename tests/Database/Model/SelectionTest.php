<?php

namespace Database\Model\Test;

use Database\Model\Selection;

require_once __DIR__ . '/../../bootstrap.inc.php';



/**
 * Test class for Database\Model\Selection.
 *
 * @author     Jan-Sebastian Fabík
 */
class SelectionTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Database\Model\Selection */
	protected $articles;

	/** @var \Database\Model\Selection */
	protected $users;



	protected function setUp()
	{
		include __DIR__ . '/connect.inc.php';
		$this->articles = new Selection('articles', $connection);
		$this->users = new Selection('users', $connection);
	}



	public function testCount()
	{
		$this->assertCount(2, $this->articles);
		$this->assertCount(3, $this->users);
	}



	public function testGetByKey()
	{
		$article = $this->articles[1];
		$this->assertInstanceof('Blog\Article', $article);
		$users = $this->users[1];
		$this->assertInstanceof('Blog\User', $users);
	}



	public function testBelongsTo()
	{
		$article = $this->articles[1];
		$author = $article->ref('users', 'author_id');
		$this->assertInstanceof('Blog\User', $author);
		$this->assertEquals(1, $author->id);
	}



	public function testBelongsToShorthand()
	{
		$article = $this->articles[1];
		$author = $article->author;
		$this->assertInstanceof('Blog\User', $author);
		$this->assertEquals(1, $author->id);
	}



	public function testHasMany()
	{
		$author = $this->users[1];
		$articles = $author->related('articles', 'author_id');
		$article = $articles[1];
		$this->assertInstanceof('Blog\Article', $article);
		$this->assertEquals(1, $article->id);
	}



	public function testHasManyCount()
	{
		$this->assertCount(1, $this->users[1]->related('articles', 'author_id'));
		$this->assertCount(0, $this->users[3]->related('articles', 'author_id'));
	}
}
