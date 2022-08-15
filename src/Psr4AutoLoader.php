<?php

namespace Laz0r\AutoLoader;

/**
 * PSR-4 compliant autoloader
 *
 * This class can be hydrated by assigning properties.
 * Assign namespaces as properties having string path
 * arrays as their value.
 */
class Psr4AutoLoader extends AbstractAutoLoader {

	public function load(string $qcn): void {
		$path = $this->getPathFromQcn($qcn);

		if ($path !== false) {
			$this->requireFile($path);
		}
	}

	/**
	 * Locate filesystem path of given QCN
	 *
	 * @param string $qcn
	 * @psalm-param class-string $qcn
	 *
	 * @return string|false Full path or false if not found
	 */
	protected function getPathFromQcn(string $qcn) {
		return $this->searchClass(
			...$this->splitNamespace($qcn),
		);
	}

	/**
	 * Locates the include file for $class in $path
	 *
	 * @param string $path
	 * @param string $class
	 *
	 * @return string|false Full path or false if not found
	 */
	protected function resolveInclude(string $path, string $class) {
		return stream_resolve_include_path("$path$class.php");
	}

	/**
	 * Locate filesystem path of given namespace/class combination
	 *
	 * @param string $namespace
	 * @param string $class
	 *
	 * @return string|false Path to include file, or false on error
	 */
	protected function searchClass(string $namespace, string $class) {
		if (isset($this->$namespace)) {
			/** @var string $path */
			foreach ($this->$namespace as $path) {
				$file = $this->resolveInclude($path, $class);

				if ($file !== false) {
					return $file;
				}
			}
		}

		if ($namespace === "") {
			return false;
		}

		$split = $this->splitNamespace($namespace);

		return $this->searchClass(
			$split[0],
			$split[1] . DIRECTORY_SEPARATOR . $class,
		);
	}

	/**
	 * Splits a namespace string on the last sub-namespace separator
	 *
	 * @param string $namespace E.g. "Foo\\Bar\\Quux"
	 *
	 * @return string[] E.g. ["Foo\\Bar", "Quux"]
	 */
	protected function splitNamespace(string $namespace): array {
		$pos = strrpos($namespace, "\\");

		return $pos !== false
			? [
				substr($namespace, 0, $pos),
				substr($namespace, $pos + 1),
			]
			: ["", $namespace];
	}

}

/* vi:set ts=4 sw=4 noet: */
