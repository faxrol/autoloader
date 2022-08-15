<?php

namespace Laz0r\AutoLoader;

use Laz0r\AutoLoader\Exception\InvalidAutoLoaderException;
use ReflectionClass;

/**
 * Builds an AutoLoaderInterface for use with PSR-4 or equivalent
 */
class Builder implements BuilderInterface {

	/** @var string */
	protected const DEFAULT_PRODUCT = Psr4AutoLoader::class;

	/**
	 * @var string Class name of product to build
	 * @psalm-var class-string
	 */
	protected string $product;

	/**
	 * @var array[] Namespace (string) => Paths (string[])
	 * @psalm-var array<string, string[]>
	 */
	protected array $namespaces = [];

	/**
	 * Constructor
	 *
	 * @param string|null $product (Optional) Class name of AutoLoaderInterface to build
	 * @psalm-param ?class-string $product
	 */
	public function __construct(?string $product = null) {
		/** @psalm-var class-string $qcn */
		$qcn = static::DEFAULT_PRODUCT;

		$this->setProduct($product ?? $qcn);
	}

	public function add(
		string $namespace,
		string $path,
		bool $prepend = false
	): BuilderInterface {
		$namespace = $this->canonicalizeNamespace($namespace);
		$path = $this->canonicalizePath($path);
		$paths = $this->namespaces[$namespace] ?? [];

		if (!$prepend) {
			array_push($paths, $path);
		} else {
			array_unshift($paths, $path);
		}

		$this->namespaces[$namespace] = $paths;

		return $this;
	}

	public function build(): AutoLoaderInterface {
		return $this->configureLoader(
			$this->createLoader(),
		);
	}

	/**
	 * Get class name of product (AutoLoaderInterface implementation)
	 *
	 * @return string
	 */
	public function getProduct(): string {
		return $this->product;
	}

	public function set(string $namespace, string $path): BuilderInterface {
		$namespace = $this->canonicalizeNamespace($namespace);
		$path = $this->canonicalizePath($path);

		$this->namespaces[$namespace] = [$path];

		return $this;
	}

	/**
	 * Set class name of AutoLoaderInterface to build
	 *
	 * @param string $product
	 * @psalm-param class-string $product
	 *
	 * @return $this
	 * @throws \Laz0r\AutoLoader\Exception\InvalidAutoLoaderException
	 */
	public function setProduct(string $product) {
		if (!is_subclass_of($product, AutoLoaderInterface::class)) {
			throw new InvalidAutoLoaderException(sprintf(
				"AutoLoaderInterface not implemented by \"%s\"",
				$product,
			));
		}

		$this->product = $product;

		return $this;
	}

	/**
	 * Configure an AutoLoaderInterface instance
	 *
	 * @param \Laz0r\AutoLoader\AutoLoaderInterface $AutoLoader
	 *
	 * @return \Laz0r\AutoLoader\AutoLoaderInterface
	 */
	protected function configureLoader(
		AutoLoaderInterface $AutoLoader
	): AutoLoaderInterface {
		/* Assumes the loader ingests its configuration as properties.
		 * This is implementation-specific, as the interface does not
		 * define how this should be done (at the time of writing).
		 */
		foreach ($this->getAll() as $namespace => $paths) {
			$AutoLoader->$namespace = $paths;
		}

		return $AutoLoader;
	}

	/**
	 * Create new instance of AutoLoaderInterface
	 *
	 * @return \Laz0r\AutoLoader\AutoLoaderInterface
	 */
	protected function createLoader(): AutoLoaderInterface {
		/** @psalm-var class-string $qcn */
		$qcn = $this->getProduct();
		$RC = new ReflectionClass($qcn);
		$Ret = $RC->newInstance();

		assert($Ret instanceof AutoLoaderInterface);

		return $Ret;
	}

	/**
	 * Get paths for all namespaces
	 *
	 * @return array[]
	 * @psalm-return array<string, string[]>
	 */
	protected function getAll(): array {
		return $this->namespaces;
	}

	/**
	 * Return canonical namespace identifier
	 *
	 * @param string $namespace
	 *
	 * @return string
	 */
	protected function canonicalizeNamespace(string $namespace): string {
		return trim($namespace, "\\");
	}

	/**
	 * Return canonical path
	 *
	 * Used to ensure the paths ends with a "/", but that made it
	 * impossible to add a namespace without a path, using just
	 * the include_path to resolve filenames. It was kept so any
	 * derivative class can easily override this behavior.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	protected function canonicalizePath(string $path): string {
		return $path;
	}

}

/* vi:set ts=4 sw=4 noet: */
