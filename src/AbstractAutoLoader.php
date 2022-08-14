<?php
namespace Laz0r\AutoLoader;

/**
 * Base class for AutoLoaderInterface implementations
 */
abstract class AbstractAutoLoader implements AutoLoaderInterface {

	protected const INCLUDE_HELPER = AbstractIncludeHelper::class;
	protected const REQUIRE_METHOD = "requireFile";

	abstract public function load(string $qcn): void;

	/**
	 * require a file
	 *
	 * @param string $file   Path of file to require
	 *
	 * @return void
	 */
	protected function requireFile(string $file): void {
		call_user_func([static::INCLUDE_HELPER, static::REQUIRE_METHOD], $file);
	}

}

/* vi:set ts=4 sw=4 noet: */
