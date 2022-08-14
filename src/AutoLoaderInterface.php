<?php
namespace Laz0r\AutoLoader;

/**
 * Allows ManagerInterface to register any implementation
 */
interface AutoLoaderInterface {

	/**
	 * Attempt to load (include/require) $qcn
	 *
	 * @param string $qcn Qualified Class Name
	 * @psalm-param class-string $qcn
	 *
	 * @return void
	 */
	public function load(string $qcn): void;

}

/* vi:set ts=4 sw=4 noet: */
