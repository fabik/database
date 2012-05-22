<?php

namespace Database\Model\Test;

use Database\Model\Selection;
use Blog\Articles;
use Blog\Users;

require_once __DIR__ . '/../../bootstrap.inc.php';



/**
 * Test class for Database\Model\Table.
 *
 * @author     Jan-Sebastian Fabík
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Blog\Articles */
	protected $articles;

	/** @var \Blog\Users */
	protected $users;



	protected function setUp()
	{
		include __DIR__ . '/connect.inc.php';
		$this->articles = new Articles('articles', $connection);
		$this->users = new Users('users', $connection);
	}



	public function testFindAll()
	{
		$articles = $this->articles->findAll();
		$this->assertInstanceof('Database\Model\Selection', $articles);
		$this->assertInstanceof('Blog\Article', $articles[1]);
		$this->assertEquals(1, $articles[1]->id);
	}



	public function testFindAllCount()
	{
		$articles = $this->articles->findAll();
		$this->assertCount(2, $articles);
	}



	public function testFindBy()
	{
		$articles = $this->articles->findBy(array('title' => 'Cinderella'));
		$this->assertInstanceof('Database\Model\Selection', $articles);
		$this->assertInstanceof('Blog\Article', $articles[2]);
		$this->assertEquals(2, $articles[2]->id);
	}



	public function testFindByCount()
	{
		$articles = $this->articles->findBy(array('title' => 'Cinderella'));
		$this->assertCount(1, $articles);
	}



	public function testFindOneBy()
	{
		$article = $this->articles->findOneBy(array('title' => 'Cinderella'));
		$this->assertInstanceof('Blog\Article', $article);
		$this->assertEquals(2, $article->id);
	}



	public function testFindOneByNotFound()
	{
		$article = $this->articles->findOneBy(array('title' => 'undefined'));
		$this->assertFalse($article);
	}



	public function testFind()
	{
		$article = $this->articles->find(1);
		$this->assertInstanceof('Blog\Article', $article);
		$this->assertEquals(1, $article->id);
	}



	public function testFindNotFound()
	{
		$article = $this->articles->find(3);
		$this->assertFalse($article);
	}



	public function testOffsetGet()
	{
		$user = $this->users[1];
		$this->assertEquals(1, $user->id);
		$this->assertEquals("demo", $user->name);
	}



	public function testOffsetExists()
	{
		$this->assertTrue(isset($this->users[1]));
		$this->assertFalse(isset($this->users[4]));
	}



	public function testOffsetUnset()
	{
		unset($this->users[1]);
		$this->assertFalse(isset($this->users[1]));
	}



	public function testCreate()
	{
		$article = $this->articles->create(array(
			'title' => 'Test title',
			'content' => 'Test content',
			'author_id' => 1
		));
		$this->assertEquals(3, $article->id);
		$this->assertCount(3, $this->articles->findAll());
		$this->assertCount(2, $this->users->find(1)->articles);
	}



	/**
	 * @expectedException Database\Model\DuplicateEntryException
	 */
	public function testCreateDuplicateEntry()
	{
		$article = $this->users->create(array(
			'name' => 'duplicate',
			'password' => sha1('xxx'),
			'email' => 'demo@example.com',
			'firstname' => 'John',
			'surname' => 'Doe',
		));
	}
}
