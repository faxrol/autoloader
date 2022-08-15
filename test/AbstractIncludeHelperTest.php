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
	 *
	 * @return void
	 */
	public function testIncludeFileIncludesFile(): void {
		$before = get_included_files();
		AbstractIncludeHelper::includeFile(
			__DIR__ . "/_files/dummy.php",
		);
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before)),
		);
	}

	/**
	 * @covers ::includeFile
	 *
	 * @return void
	 */
	public function testIncludeFileReturnsResult(): void {
		$res = AbstractIncludeHelper::includeFile(
			__DIR__ . "/_files/return.php",
		);

		$this->assertTrue($res);
	}

	/**
	 * @covers ::includeFileOnce
	 *
	 * @return void
	 */
	public function testIncludeFileOnceIncludesFile(): void {
		$before = get_included_files();
		AbstractIncludeHelper::includeFileOnce(
			__DIR__ . "/_files/dummy.php",
		);
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before)),
		);
	}

	/**
	 * @covers ::includeFileOnce
	 *
	 * @return void
	 */
	public function testIncludeFileOnceReturnsResult(): void {
		$res = AbstractIncludeHelper::includeFileOnce(
			__DIR__ . "/_files/return.php",
		);

		$this->assertTrue($res);
	}

	/**
	 * @covers ::requireFile
	 *
	 * @return void
	 */
	public function testRequireFileIncludesFile(): void {
		$before = get_included_files();
		AbstractIncludeHelper::requireFile(
			__DIR__ . "/_files/dummy.php",
		);
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before)),
		);
	}

	/**
	 * @covers ::requireFile
	 *
	 * @return void
	 */
	public function testRequireFileReturnsResult(): void {
		$res = AbstractIncludeHelper::requireFile(
			__DIR__ . "/_files/return.php",
		);

		$this->assertTrue($res);
	}

	/**
	 * @covers ::requireFileOnce
	 *
	 * @return void
	 */
	public function testRequireFileOnceIncludesFile(): void {
		$before = get_included_files();
		AbstractIncludeHelper::requireFileOnce(
			__DIR__ . "/_files/dummy.php",
		);
		$after = get_included_files();

		$this->assertSame(
			[__DIR__ . "/_files/dummy.php"],
			array_values(array_diff($after, $before)),
		);
	}

	/**
	 * @covers ::requireFileOnce
	 *
	 * @return void
	 */
	public function testRequireFileOnceReturnsResult(): void {
		$res = AbstractIncludeHelper::requireFileOnce(
			__DIR__ . "/_files/return.php",
		);

		$this->assertTrue($res);
	}

}

/* vi:set ts=4 sw=4 noet: */
