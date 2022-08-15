<?php

namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\AbstractAutoLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\AbstractAutoLoader
 */
class AbstractAutoLoaderTest extends TestCase {

	private static string $result;

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/AbstractAutoLoader.php";
	}

	/**
	 * @covers ::requireFile
	 *
	 * @return void
	 */
	public function testRequireFile(): void {
		$Sut = new class() extends AbstractAutoLoader {

			/**
			 * @var string
			 */
			protected const INCLUDE_HELPER = AbstractAutoLoaderTest::class;

			/**
			 * @var string
			 */
			protected const REQUIRE_METHOD = "baww";

			public function load(string $qcn): void {
			}

		};
		$Method = (new ReflectionClass($Sut))
			->getMethod("requireFile");

		$Method->setAccessible(true);
		$Method->invoke($Sut, "amaze");

		$this->assertSame("amaze", self::$result);
	}

	public static function baww(string $s): void {
		self::$result = $s;
	}

}

/* vi:set ts=4 sw=4 noet: */
