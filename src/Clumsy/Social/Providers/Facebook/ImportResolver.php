<?php namespace Clumsy\Social\Providers\Facebook;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class ImportResolver {

	protected static $namespace = "\\Clumsy\\Social\\Providers\\Facebook\\Resources\\";

	public static function __callStatic($name ,$arguments)
	{
		$service = static::$namespace.studly_case($name);

		$reflection = new \ReflectionClass($service);
		$service = $reflection->newInstanceArgs($arguments);

		return $service->import();
	}
}