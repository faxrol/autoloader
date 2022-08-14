<?php
namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\{AutoLoaderInterface, Psr4AutoLoader};
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\Psr4AutoLoader
 */
class Psr4AutoLoaderTest extends TestCase {

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/AbstractAutoLoader.php";
		require_once __DIR__ . "/../src/Psr4AutoLoader.php";
	}

	/**
	 * @covers ::load
	 *
	 * @return void
	 */
	public function testLoadUnknownClass() {
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"getPathFromQcn",
				"requireFile",
			])
			->getMock();

		$MockSut->expects($this->never())
			->method("requireFile");
		$MockSut->expects($this->once())
			->method("getPathFromQcn")
			->with($this->equalTo("Laz0r\\Widget\\Nuke"))
			->will($this->returnValue(false));

		$MockSut->load("Laz0r\\Widget\\Nuke");
	}

	/**
	 * @covers ::load
	 *
	 * @return void
	 */
	public function testLoadKnownClass() {
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"getPathFromQcn",
				"requireFile",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("getPathFromQcn")
			->with($this->equalTo("Laz0r\\Widget\\Nuke"))
			->will($this->returnValue("/laz0r/widget/nuke.php"));
		$MockSut->expects($this->once())
			->method("requireFile")
			->with($this->equalTo("/laz0r/widget/nuke.php"));

		$MockSut->load("Laz0r\\Widget\\Nuke");
	}

	/**
	 * @covers ::getPathFromQcn
	 */
	public function testGetPathFromQcn() {
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
				->setMethods(["splitNamespace", "searchClass"])
				->getMock();
		$MockSut->expects($this->once())
			->method("splitNamespace")
			->with($this->identicalTo("Laz0r\\AutoLoader\\Wow"))
			->will($this->returnValue(["Laz0r\\AutoLoader", "Amaze"]));
		$MockSut->expects($this->once())
			->method("searchClass")
			->with(
				$this->identicalTo("Laz0r\\AutoLoader"),
				$this->identicalTo("Amaze")
			)
			->will($this->returnValue("very.php"));

		$Method = (new ReflectionClass($MockSut))
			->getMethod("getPathFromQcn");
		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut, "Laz0r\\AutoLoader\\Wow");

		$this->assertSame("very.php", $result);
	}

	/**
	 * @covers ::resolveInclude
	 * @runInSeparateProcess
	 *
	 * @return void
	 */
	public function testResolveInclude() {
		$Sut = new Psr4AutoLoader();
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("resolveInclude");

		$Method->setAccessible(true);

		set_include_path(__DIR__);

		$result = $Method->invoke($Sut, "_files/", "dummy");
		$expect = __DIR__ . DIRECTORY_SEPARATOR . "_files" .
			DIRECTORY_SEPARATOR . "dummy.php";

		$this->assertSame($expect, $result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClassRecursive() {
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$MockSut->expects($this->never())
			->method("resolveInclude");
		$MockSut->expects($this->once())
			->method("splitNamespace")
			->with($this->equalTo("Laz0r\\Widget"))
			->will($this->returnValue(["Laz0r", "Widget"]));
		$MockSut->expects($this->once())
			->method("searchClass")
			->with(
				$this->equalTo("Laz0r"),
				$this->equalTo("Widget" . DIRECTORY_SEPARATOR . "Nuke")
			)
			->will($this->returnValue("/nuke.php"));

		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut, "Laz0r\\Widget", "Nuke");

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClassRecursive
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassRecursiveReturnValue($result) {
		$this->assertSame("/nuke.php", $result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClassEmptyNamespace() {
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$MockSut->expects($this->never())
			->method("resolveInclude");
		$MockSut->expects($this->never())
			->method("splitNamespace");
		$MockSut->expects($this->never())
			->method("searchClass");

		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut, "", "Nuke");

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClassEmptyNamespace
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassEmptyNamespaceReturnsFalse($result) {
		$this->assertFalse($result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClass() {
		$namespace = "Laz0r\\Widget";
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$MockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$MockSut->$namespace = ["/herp/derp", "/laz0r/widget"];
		$MockSut->expects($this->never())
			->method("splitNamespace");
		$MockSut->expects($this->never())
			->method("searchClass");
		$MockSut->expects($this->exactly(2))
			->method("resolveInclude")
			->withConsecutive(
				[
					$this->equalTo("/herp/derp"),
					$this->equalTo("Nuke"),
				],
				[
					$this->equalTo("/laz0r/widget"),
					$this->equalTo("Nuke"),
				]
			)
			->will($this->onConsecutiveCalls(false, "/nuke.php"));

		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut, $namespace, "Nuke");

		$Method->setAccessible(true);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClass
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassReturnsFile($result) {
		$this->assertSame("/nuke.php", $result);
	}

	/**
	 * @covers ::splitNamespace
	 *
	 * @return array
	 */
	public function testSplitNamespace() {
		$Sut = new Psr4AutoLoader();
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("splitNamespace");

		$Method->setAccessible(true);

		$result = $Method->invoke($Sut, "Laz0r\\Widget\\Nuke");

		$this->assertIsArray($result);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArrayCount($result) {
		$this->assertCount(2, $result);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArrayFirstMember($result) {
		$this->assertSame("Laz0r\\Widget", $result[0]);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArraySecondMember($result) {
		$this->assertSame("Nuke", $result[1]);
	}

	/**
	 * @covers ::splitNamespace
	 *
	 * @return array
	 */
	public function testSplitNamespaceWithoutNamespace() {
		$Sut = new Psr4AutoLoader();
		$Method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("splitNamespace");

		$Method->setAccessible(true);

		$result = $Method->invoke($Sut, "Nuke");

		$this->assertIsArray($result);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArrayCount($result) {
		$this->assertCount(2, $result);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArrayFirstMember($result) {
		$this->assertSame("", $result[0]);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArraySecondMember($result) {
		$this->assertSame("Nuke", $result[1]);
	}

}

/* vi:set ts=4 sw=4 noet: */
