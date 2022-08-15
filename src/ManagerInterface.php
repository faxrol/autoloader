<?php

namespace Laz0r\AutoLoader;

/**
 * For registering AutoLoaderInterface instances
 */
interface ManagerInterface {

	/**
	 * Test if a given identifier has been registered
	 *
	 * @param string $identifier Result of register()
	 *
	 * @return bool
	 */
	public function has(string $identifier): bool;

	/**
	 * Register an instance of AutoLoaderInterface
	 *
	 * @param \Laz0r\AutoLoader\AutoLoaderInterface $AutoLoader
	 * @param bool                                  $prepend
	 *
	 * @return string Unique identifier for the instance
	 * @throws \Laz0r\AutoLoader\Exception\AlreadyRegisteredException
	 */
	public function register(
		AutoLoaderInterface $AutoLoader,
		bool $prepend = false
	): string;

	/**
	 * Unregister an instance of AutoLoaderInterface
	 *
	 * The identifier may be reused for newly registered instances.
	 *
	 * @param string $identifier
	 *
	 * @return $this
	 * @throws \Laz0r\AutoLoader\Exception\InvalidIdentifierException
	 */
	public function unregister(string $identifier): self;

}

/* vi:set ts=4 sw=4 noet: */
