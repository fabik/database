<?php

namespace Database\Model;

use Nette\ObjectMixin;



/**
 * Single row representation.
 *
 * @author     Jan-Sebastian Fabík
 */
class ActiveRow extends \Nette\Database\Table\ActiveRow
{
	/**
	 * Returns property value. Do not call directly.
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws \Nette\MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		if ($this->hasProperty($name)) {
			return parent::__get($name);
		} else {
			return ObjectMixin::get($this, $name);
		}
	}



	/**
	 * Sets value of a property. Do not call directly.
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws \Nette\MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		if ($this->hasProperty($name)) {
			parent::__set($name, $value);
		} else {
			ObjectMixin::set($this, $name, $value);
		}
	}



	/**
	 * Is property defined?
	 * @param  string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		if ($this->hasProperty($name)) {
			return parent::__isset($name);
		} else {
			return ObjectMixin::has($this, $name);
		}
	}



	/**
	 * Access to undeclared property.
	 * @param  string  property name
	 * @return void
	 * @throws \Nette\MemberAccessException
	 */
	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}



	/**
	 * Determines whether the original row has got a property with the given
	 * name.
	 * @param  string
	 * @return bool
	 */
	private function hasProperty($name)
	{
		if (parent::__isset($name)) {
			return TRUE;
		}

		try {
			$this->getTable()->getConnection()->getDatabaseReflection()->getBelongsToReference($this->getTable()->getName(), $name);
		} catch (\PDOException $e) {
			return FALSE;
		}

		return TRUE;
	}
}
