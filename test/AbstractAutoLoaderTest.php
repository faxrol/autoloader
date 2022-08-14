<?php
namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\AbstractAutoLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\AbstractAutoLoader
 */
class AbstractAutoLoaderTest extends TestCase {

	private static $result;

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/AbstractAutoLoader.php";
	}

	/**
	 * @covers ::requireFile
	 */
	public function testRequireFile() {
		$Sut = new class() extends AbstractAutoLoader {

			protected const INCLUDE_HELPER = AbstractAutoLoaderTest::class;
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

	public static function baww(string $s) {
		self::$result = $s;
	}

}

/* vi:set ts=4 sw=4 noet: */
