<?php

namespace Database\Model\Test;

use Database\Model\Selection;
use Blog\User;

require_once __DIR__ . '/../../bootstrap.inc.php';



/**
 * Test class for Database\Model\ActiveRecord.
 *
 * @author     Jan-Sebastian Fabík
 */
class ActiveRecordTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Database\Model\Selection */
	protected $users;

	/** @var \Blog\User */
	protected $user;



	protected function setUp()
	{
		include __DIR__ . '/connect.inc.php';
		$this->users = $this->getMock(
			'Database\Model\Selection',
			array('getCache'),
			array('users', $connection)
		);
		$this->user = new User(array(
			'id' => 1,
			'name' => 'demo',
			'password' => sha1('xxx'),
			'email' => 'demo@example.com',
			'firstname' => 'John',
			'surname' => 'Doe',
		), $this->users);
	}



	public function testGetColumn()
	{
		$this->assertEquals(1, $this->user->id);
		$this->assertEquals('demo', $this->user->name);
	}



	public function testSetColumn()
	{
		$this->user->name = 'demo2';
		$this->assertEquals('demo2', $this->user->name);
	}



	public function testIssetColumn()
	{
		$this->assertTrue(isset($this->user->name));
	}



	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testUnsetColumn()
	{
		unset($this->user->name);
	}



	public function testGetProperty()
	{
		$this->assertEquals("John Doe", $this->user->realName);
	}



	public function testSetProperty()
	{
		$this->user->realName = "Dave Lister";
		$this->assertEquals("Dave", $this->user->firstname);
		$this->assertEquals("Lister", $this->user->surname);
	}



	public function testIssetProperty()
	{
		$this->assertTrue(isset($this->user->realName));
	}



	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testUnsetProperty()
	{
		unset($this->user->realName);
	}



	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testGetUndefined()
	{
		$this->user->undefined;
	}



	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testSetUndefined()
	{
		$this->user->undefined = 5;
	}



	public function testIssetUndefined()
	{
		$this->assertFalse(isset($this->user->undefined));
	}



	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testUnsetUndefined()
	{
		unset($this->user->undefined);
	}
}
