<?php

namespace Laz0r\AutoLoader;

/**
 * Wraps instances of BuilderInterface and ManagerInterface.
 * Facilitates easy configuration using convenience methods.
 */
class Container {

	protected BuilderInterface $Builder;

	protected ManagerInterface $Manager;

	/**
	 * Constructor
	 *
	 * @param \Laz0r\AutoLoader\BuilderInterface $Builder
	 * @param \Laz0r\AutoLoader\ManagerInterface $Manager
	 */
	public function __construct(
		BuilderInterface $Builder,
		ManagerInterface $Manager
	) {
		$this->setBuilder($Builder);
		$this->setManager($Manager);
	}

	/**
	 * Return a copy of BuilderInterface
	 *
	 * @return \Laz0r\AutoLoader\BuilderInterface
	 */
	public function getBuilder(): BuilderInterface {
		return clone $this->Builder;
	}

	/**
	 * Return instance of ManagerInterface
	 *
	 * @return \Laz0r\AutoLoader\ManagerInterface
	 */
	public function getManager(): ManagerInterface {
		return $this->Manager;
	}

	/**
	 * Convenience method
	 *
	 * Registers an instance of AutoLoaderInterface with the manager.
	 *
	 * @param \Laz0r\AutoLoader\AutoLoaderInterface $AutoLoader
	 *
	 * @return string Unique identifier for the AutoLoaderInterface object
	 */
	public function register(AutoLoaderInterface $AutoLoader): string {
		return $this
			->getManager()
			->register($AutoLoader);
	}

	/**
	 * Change the BuilderInterface instance
	 *
	 * @param \Laz0r\AutoLoader\BuilderInterface $Builder
	 *
	 * @return $this
	 */
	public function setBuilder(BuilderInterface $Builder) {
		$this->Builder = $Builder;

		return $this;
	}

	/**
	 * Change the ManagerInterface instance
	 *
	 * @param \Laz0r\AutoLoader\ManagerInterface $Manager
	 *
	 * @return $this
	 */
	public function setManager(ManagerInterface $Manager) {
		$this->Manager = $Manager;

		return $this;
	}

	/**
	 * Convenience method
	 *
	 * Pass an array or Traversable with arrays that have
	 * a namespace as the first value and a corresponding
	 * path as the second value. E.g.: [["Foo", "./src"]]
	 *
	 * @param iterable $config
	 *
	 * @return string Unique identifier for the AutoLoaderInterface object
	 */
	public function setup(iterable $config): string {
		$Builder = $this->getBuilder();

		/** @var array $entry */
		foreach ($config as $entry) {
			assert(array_key_exists(0, $entry));
			assert(is_string($entry[0]));
			assert(array_key_exists(1, $entry));
			assert(is_string($entry[1]));

			$Builder->add($entry[0], $entry[1]);
		}

		return $this->register(
			$Builder->build(),
		);
	}

}

/* vi:set ts=4 sw=4 noet: */
