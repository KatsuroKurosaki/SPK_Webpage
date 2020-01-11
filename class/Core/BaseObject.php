<?php

namespace Core;

class BaseObject
{

	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}

	public function __get($property)
	{

		if (property_exists($this, $property)) {
			return $this->$property;
		}

		$trace = debug_backtrace();
		trigger_error(
			'Propiedad indefinida mediante __get(): ' . $property .
				' en ' . $trace[0]['file'] .
				' en la línea ' . $trace[0]['line'],
			E_USER_NOTICE
		);
		return null;
	}

	public function toObject(bool $lower = false, bool $ignoreNullValues = false): \stdClass
	{
		if ($this != null) {
			$buffer = [];
			$list = get_class_methods($this);

			foreach ($list as $key => $value) {
				if (substr($value, 0, 3) === "get") {

					$reflection = new \ReflectionMethod($this, $value);
					if ($reflection->isPublic()) {

						if (is_callable([$this, $value])) {
							$data = $this->$value();
							if (!$ignoreNullValues || $data != null) {

								$value = $lower ? strtolower($value) : $value;
								if ($data instanceof \Core\BaseObject) {
									$buffer[substr($value, 3, strlen($value))] = $data->toObject($lower, $ignoreNullValues);
								} elseif (is_array($data)) {
									$buffer[substr($value, 3, strlen($value))] = [];
									foreach ($data as $dataKey => $dataValue) {
										if ($dataValue instanceof \Core\BaseObject) {
											$buffer[substr($value, 3, strlen($value))][$dataKey] = $dataValue->toObject($lower, $ignoreNullValues);
										} else {
											$buffer[substr($value, 3, strlen($value))] = $data;
										}
									}
								} else {
									$buffer[substr($value, 3, strlen($value))] = $data;
								}
							}
						}
					}
				}
			}

			return (object) $buffer;
		}
		return null;
	}

	public function __destruct()
	{
		if ($this != null) {
			$buffer = [];
			$list = (array) $this;

			foreach ($list as $key => $value) {
				$keyStr = str_replace(get_class($this), "", $key);
				$keyStr = substr($keyStr, 3, strlen($keyStr));
				unset($this->$keyStr);
			}
		}
	}

	public function checkClassName($classname, $compare)
	{
		if (gettype($classname) == "object") {
			$classname = get_class($classname);
		}

		if ($pos = strrpos($classname, '\\')) {
			$tmp = substr($classname, $pos + 1);
			return ($tmp === $compare);
		}
		return false;
	}
}
