<?php
namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\AbstractIncludeHelper;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\AbstractIncludeHelper
 * @runTestsInSeparateProcesses
 */
class AbstractIncludeHelperTest extends TestCase {

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AbstractIncludeHelper.php";
	}

	/**
	 * @covers ::includeFile
	 */
	public function testIncludeFileIncludesFile() {
		$before = get_included_files();
		AbstractIncludeHelper::includeFile(__DIR__ . "/_files/dummy.php");
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before))
		);
	}

	/**
	 * @covers ::includeFile
	 */
	public function testIncludeFileReturnsResult() {
		$res = AbstractIncludeHelper::includeFile(__DIR__ . "/_files/return.php");

		$this->assertTrue($res);
	}

	/**
	 * @covers ::includeFileOnce
	 */
	public function testIncludeFileOnceIncludesFile() {
		$before = get_included_files();
		AbstractIncludeHelper::includeFileOnce(__DIR__ . "/_files/dummy.php");
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before))
		);
	}

	/**
	 * @covers ::includeFileOnce
	 */
	public function testIncludeFileOnceReturnsResult() {
		$res = AbstractIncludeHelper::includeFileOnce(__DIR__ . "/_files/return.php");

		$this->assertTrue($res);
	}

	/**
	 * @covers ::requireFile
	 */
	public function testRequireFileIncludesFile() {
		$before = get_included_files();
		AbstractIncludeHelper::requireFile(__DIR__ . "/_files/dummy.php");
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before))
		);
	}

	/**
	 * @covers ::requireFile
	 */
	public function testRequireFileReturnsResult() {
		$res = AbstractIncludeHelper::requireFile(__DIR__ . "/_files/return.php");

		$this->assertTrue($res);
	}

	/**
	 * @covers ::requireFileOnce
	 */
	public function testRequireFileOnceIncludesFile() {
		$before = get_included_files();
		AbstractIncludeHelper::requireFileOnce(__DIR__ . "/_files/dummy.php");
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before))
		);
	}

	/**
	 * @covers ::requireFileOnce
	 */
	public function testRequireFileOnceReturnsResult() {
		$res = AbstractIncludeHelper::requireFileOnce(__DIR__ . "/_files/return.php");

		$this->assertTrue($res);
	}

}

/* vi:set ts=4 sw=4 noet: */
