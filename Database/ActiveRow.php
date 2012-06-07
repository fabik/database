<?php

namespace Database;
use Nette\ObjectMixin;



/**
 * Single row representation.
 *
 * @author     Jan-Sebastian Fabík
 */
class ActiveRow extends \Nette\Database\Table\ActiveRow
{
	public function &__get($key)
	{
		if (parent::__isset($key) || !ObjectMixin::has($this, $key)) {
			return parent::__get($key);
		} else {
			return ObjectMixin::get($this, $key);
		}
	}



	public function __set($key, $value)
	{
		if (parent::__isset($key) || !ObjectMixin::has($this, $key)) {
			parent::__set($key, $value);
		} else {
			ObjectMixin::set($this, $key, $value);
		}
	}



	public function __isset($key)
	{
		return parent::__isset($key) || ObjectMixin::has($this, $key);
	}



	public function __unset($key)
	{
		if (parent::__isset($key) || !ObjectMixin::has($this, $key)) {
			parent::__unset($key);
		} else {
			ObjectMixin::remove($this, $key);
		}
	}
}
