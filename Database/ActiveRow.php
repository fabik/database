<?php

namespace Fabik\Database;

use Nette\ObjectMixin,
	Nette\Utils\Strings;



/**
 * Single row representation.
 *
 * @author     Jan-Sebastian Fabík
 */
class ActiveRow extends \Nette\Database\Table\ActiveRow
{
	public function &__get($key)
	{
		$column = $this->formatColumnName($key);
		if (parent::__isset($column) || !ObjectMixin::has($this, $key)) {
			return parent::__get($column);
		} else {
			return ObjectMixin::get($this, $key);
		}
	}



	public function __set($key, $value)
	{
		$column = $this->formatColumnName($key);
		if (parent::__isset($column) || !ObjectMixin::has($this, $key)) {
			parent::__set($column, $value);
		} else {
			ObjectMixin::set($this, $key, $value);
		}
	}



	public function __isset($key)
	{
		$column = $this->formatColumnName($key);
		return parent::__isset($column) || ObjectMixin::has($this, $key);
	}



	public function __unset($key)
	{
		$column = $this->formatColumnName($key);
		if (parent::__isset($column) || !ObjectMixin::has($this, $key)) {
			parent::__unset($column);
		} else {
			ObjectMixin::remove($this, $key);
		}
	}



	/**
	 * Formats column name.
	 * @param  string
	 * @return string
	 */
	public function formatColumnName($key)
	{
		static $cache = array();
		$name = & $cache[$key];
		if ($name === NULL) {
			$name = Strings::replace($key, '#[A-Z]#', function ($match) {
				return '_' . strtolower($match[0]);
			});
		}
		return $name;
	}



	/**
	 * Gets column value.
	 * @param  string
	 * @return string
	 */
	public function getColumnValue($name)
	{
		return parent::__get($name);
	}



	/**
	 * Sets column value.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function setColumnValue($name, $value)
	{
		parent::__set($name, $value);
	}
}
