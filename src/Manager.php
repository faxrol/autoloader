<?php

namespace Laz0r\AutoLoader;

use Laz0r\AutoLoader\Exception\{
	AlreadyRegisteredException,
	InvalidIdentifierException,
};

/**
 * Registers (and unregisters) AutoLoaderInterface instances
 */
class Manager implements ManagerInterface {

	/**
	 * @var \Laz0r\AutoLoader\AutoLoaderInterface[]
	 */
	protected array $loaders = [];

	public function has(string $identifier): bool {
		return isset($this->loaders[$identifier]);
	}

	public function register(
		AutoLoaderInterface $AutoLoader,
		bool $prepend = false
	): string {
		$ret = $this->getIdentifier($AutoLoader);

		if (!$this->has($ret)) {
			$this->setLoader($ret, $AutoLoader);

			$this->wrapSplAutoloadRegister(
				$this->getAutoloadFunction($AutoLoader),
				$prepend,
			);

			return $ret;
		}

		throw new AlreadyRegisteredException(
			"Instance of AutoLoaderInterface already registered",
			17,
		);
	}

	public function unregister(string $identifier): ManagerInterface {
		if ($this->has($identifier)) {
			$AutoLoader = $this->getLoader($identifier);

			$this->removeLoader($identifier);

			$this->wrapSplAutoloadUnregister(
				$this->getAutoloadFunction($AutoLoader),
			);

			return $this;
		}

		throw new InvalidIdentifierException(
			sprintf("Invalid identifier \"%s\".", $identifier),
			2,
		);
	}

	/**
	 * Get the autoload function of an AutoLoaderInterface
	 *
	 * @param \Laz0r\AutoLoader\AutoLoaderInterface $AutoLoader
	 *
	 * @return callable
	 * @psalm-return callable(string):void
	 */
	protected function getAutoloadFunction(
		AutoLoaderInterface $AutoLoader
	): callable {
		/**
		 * @var callable $ret
		 * @psalm-var callable(string):void $ret
		 */
		$ret = [$AutoLoader, "load"];

		return $ret;
	}

	/**
	 * Get an instance of AutoLoaderInterface by identifier
	 *
	 * @param string $identifier
	 *
	 * @return \Laz0r\AutoLoader\AutoLoaderInterface
	 */
	protected function getLoader(string $identifier): AutoLoaderInterface {
		return $this->loaders[$identifier];
	}

	/**
	 * Create a unique identifier for a given object
	 *
	 * @param object $Object
	 *
	 * @return string
	 */
	protected function getIdentifier(object $Object): string {
		return spl_object_hash($Object);
	}

	/**
	 * Remove an instance of AutoLoaderInterface
	 *
	 * @param string $identifier
	 *
	 * @return void
	 */
	protected function removeLoader(string $identifier): void {
		unset($this->loaders[$identifier]);
	}

	/**
	 * Set an instance of AutoLoaderInterface by identifier
	 *
	 * @param string $identifier
	 * @param \Laz0r\AutoLoader\AutoLoaderInterface $AutoLoader
	 *
	 * @return void
	 */
	protected function setLoader(
		string $identifier,
		AutoLoaderInterface $AutoLoader
	): void {
		$this->loaders[$identifier] = $AutoLoader;
	}

	/**
	 * Wraps spl_autoload_register
	 *
	 * @param callable $autoload_function
	 * @psalm-param callable(string):void $autoload_function
	 * @param bool $prepend
	 *
	 * @return void
	 */
	protected function wrapSplAutoloadRegister(
		callable $autoload_function,
		bool $prepend = false
	): void {
		spl_autoload_register($autoload_function, true, $prepend);
	}

	/**
	 * Wraps spl_autoload_unregister
	 *
	 * @param callable $autoload_function
	 * @psalm-param callable(string):void $autoload_function
	 *
	 * @return void
	 */
	protected function wrapSplAutoloadUnregister(
		callable $autoload_function
	): void {
		spl_autoload_unregister($autoload_function);
	}

}

/* vi:set ts=4 sw=4 noet: */
