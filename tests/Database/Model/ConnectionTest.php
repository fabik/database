<?php

namespace Database\Model\Test;

require_once __DIR__ . '/../../bootstrap.inc.php';



/**
 * Test class for Database\Model\Connection.
 *
 * @author     Jan-Sebastian Fabík
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Database\Model\Connection */
	protected $object;



	protected function setUp()
	{
		include __DIR__ . '/connect.inc.php';
		$this->object = $connection;
	}



	public function testTable()
	{
		$this->assertInstanceOf('Database\Model\Selection', $this->object->table('articles'));
		$this->assertInstanceOf('Database\Model\Selection', $this->object->table('users'));
	}



	public function testGetRowClass()
	{
		$this->assertEquals('Blog\Article', $this->object->getRowClass('articles'));
		$this->assertEquals('Blog\User', $this->object->getRowClass('users'));
		$this->assertEquals('Database\Model\ActiveRow', $this->object->getRowClass('unknown'));
	}
}
