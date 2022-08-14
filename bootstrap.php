<?php
namespace Laz0r\AutoLoader;

use Laz0r\AutoLoader\Exception\AlreadyRegisteredException;
use Laz0r\AutoLoader\Exception\ExceptionInterface;
use Laz0r\AutoLoader\Exception\InvalidAutoLoaderException;
use Laz0r\AutoLoader\Exception\InvalidIdentifierException;

if (!class_exists(AbstractIncludeHelper::class, false)) {
	require __DIR__ . "/src/AbstractIncludeHelper.php";
}

return call_user_func(
	function($map) {
		$fnFilter = function($k): bool {
			return !(interface_exists($k, false) xor class_exists($k, false));
		};
		$fnMap = function($file) {
			AbstractIncludeHelper::requireFile(__DIR__ . "/src/$file.php");
		};

		array_map($fnMap, array_filter($map, $fnFilter, ARRAY_FILTER_USE_KEY));

		return new Container(new Builder(), new Manager());
	},
	[
		ExceptionInterface::class => "Exception/ExceptionInterface",
		AlreadyRegisteredException::class => "Exception/AlreadyRegisteredException",
		InvalidAutoLoaderException::class => "Exception/InvalidAutoLoaderException",
		InvalidIdentifierException::class => "Exception/InvalidIdentifierException",
		AutoLoaderInterface::class => "AutoLoaderInterface",
		BuilderInterface::class => "BuilderInterface",
		ManagerInterface::class => "ManagerInterface",
		AbstractAutoLoader::class => "AbstractAutoLoader",
		Builder::class => "Builder",
		Container::class => "Container",
		Manager::class => "Manager",
		Psr4AutoLoader::class => "Psr4AutoLoader",
	]
);

/* vi:set ts=4 sw=4 noet: */
