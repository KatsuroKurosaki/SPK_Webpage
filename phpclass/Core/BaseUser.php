<?php

namespace Core;

class BaseUser extends \Core\BaseObject
{
	protected $id = null;
	protected $idEncripted = null;
	protected $name = null;

	public function __construct()
	{
	}

	public function getName()
	{
		return $this->__get("name");
	}

	public function getId()
	{
		return $this->__get("idEncripted");
	}

	public function setName(string $value)
	{
		$this->__set("name", $value);
	}

	public function setId(string $value)
	{
		$this->__set("idEncripted", $value);
		$this->decryptId();
	}

	/**
	 *Codificacion
	 */
	public function encryptId()
	{
		$crypt = new \Crypt\StringEncoder();
		$encripted = $crypt->encryptSSL($this->__get("id"));
		$this->__set("idEncripted", $encripted);
		unset($crypt);
	}

	private function decryptId()
	{
		$crypt = new \Crypt\StringEncoder();
		$this->__set("id", $crypt->decryptSSL($this->__get("idEncripted")));
		unset($crypt);

		return $this->__get("id");
	}
}
