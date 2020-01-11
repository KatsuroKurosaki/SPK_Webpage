<?php

namespace Core;

class BaseClass extends \Core\BaseObject
{

	protected $id = null;

	/** Getters **/
	public function getId()
	{
		return $this->id;
	}

	/** Setters **/
	public function setId(int $value)
	{
		$this->id = $value;
	}
}
