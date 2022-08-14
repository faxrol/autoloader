<?php
namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\Exception\AlreadyRegisteredException;
use Laz0r\AutoLoader\Exception\InvalidIdentifierException;
use Laz0r\AutoLoader\Manager;
use Laz0r\AutoLoader\{AutoLoaderInterface, ManagerInterface};
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\Manager
 */
class ManagerTest extends TestCase {

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/Exception/ExceptionInterface.php";
		require_once __DIR__ . "/../src/Exception/AlreadyRegisteredException.php";
		require_once __DIR__ . "/../src/Exception/InvalidIdentifierException.php";
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/ManagerInterface.php";
		require_once __DIR__ . "/../src/Manager.php";
	}

	/**
	 * @covers ::has
	 *
	 * @return void
	 */
	public function testHasWithUnknownIdentifier() {
		$Sut = new Manager();

		$result = $Sut->has("Yolo");

		$this->assertFalse($result);
	}

	/**
	 * @covers ::has
	 *
	 * @return void
	 */
	public function testHasWithKnownIdentifier() {
		$Property = (new ReflectionClass(Manager::class))
			->getProperty("loaders");
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);
		$Sut = new Manager();

		$Property->setAccessible(true);
		$Property->setValue($Sut, ["Yolo" => $AutoLoader]);

		$result = $Sut->has("Yolo");

		$this->assertTrue($result);
	}

	/**
	 * @covers ::register
	 *
	 * @return void
	 */
	public function testRegisterThrowsException() {
		$this->expectException(AlreadyRegisteredException::class);
		$this->expectExceptionCode(17);

		$AutoLoader = $this->createStub(AutoLoaderInterface::class);
		$MockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getIdentifier",
				"has",
				"setLoader",
				"wrapSplAutoloadRegister",
			])
			->getMock();

		$MockSut->expects($this->never())
			->method("getAutoloadFunction");
		$MockSut->expects($this->never())
			->method("setLoader");
		$MockSut->expects($this->never())
			->method("wrapSplAutoloadRegister");
		$MockSut->expects($this->once())
			->method("getIdentifier")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue("YOLO"));
		$MockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("YOLO"))
			->will($this->returnValue(true));

		$MockSut->register($AutoLoader);
	}

	/**
	 * @covers ::register
	 *
	 * @return mixed
	 */
	public function testRegister() {
		$callable = function() {};
		$AutoLoader = $this->createMock(AutoLoaderInterface::class);
		$MockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getIdentifier",
				"has",
				"setLoader",
				"wrapSplAutoloadRegister",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("getIdentifier")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue("YOLO"));
		$MockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("YOLO"))
			->will($this->returnValue(false));
		$MockSut->expects($this->once())
			->method("setLoader")
			->with(
				$this->equalTo("YOLO"),
				$this->identicalTo($AutoLoader)
			);
		$MockSut->expects($this->once())
			->method("getAutoloadFunction")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue($callable));
		$MockSut->expects($this->once())
			->method("wrapSplAutoloadRegister")
			->with(
				$this->identicalTo($callable),
				$this->equalTo(true)
			);

		$result = $MockSut->register($AutoLoader, true);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testRegister
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testRegisterReturnsIdentifier($result) {
		$this->assertSame("YOLO", $result);
	}

	/**
	 * @covers ::unregister
	 *
	 * @return void
	 */
	public function testUnregisterThrowsException() {
		$this->expectException(InvalidIdentifierException::class);
		$this->expectExceptionCode(2);

		$MockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getLoader",
				"has",
				"removeLoader",
				"wrapSplAutoloadUnregister",
			])
			->getMock();

		$MockSut->expects($this->never())
			->method("getAutoloadFunction");
		$MockSut->expects($this->never())
			->method("getLoader");
		$MockSut->expects($this->never())
			->method("removeLoader");
		$MockSut->expects($this->never())
			->method("wrapSplAutoloadUnregister");
		$MockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue(false));

		$MockSut->unregister("Yolo");
	}

	/**
	 * @covers ::unregister
	 *
	 * @return array
	 */
	public function testUnregister() {
		$callable = function() {};
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);
		$MockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getLoader",
				"has",
				"removeLoader",
				"wrapSplAutoloadUnregister",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue(true));
		$MockSut->expects($this->once())
			->method("getLoader")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue($AutoLoader));
		$MockSut->expects($this->once())
			->method("removeLoader")
			->with($this->equalTo("Yolo"));
		$MockSut->expects($this->once())
			->method("getAutoloadFunction")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue($callable));
		$MockSut->expects($this->once())
			->method("wrapSplAutoloadUnregister")
			->with($this->identicalTo($callable));

		$result = $MockSut->unregister("Yolo");

		return [$MockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testUnregister
	 * @param array $result
	 *
	 * @return void
	 */
	public function testUnregisterReturnsSelf($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getAutoloadFunction
	 *
	 * @return void
	 */
	public function testGetAutoloadFunction() {
		$Sut = new Manager();
		$Method = (new ReflectionClass(Manager::class))
			->getMethod("getAutoloadFunction");
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);

		$Method->setAccessible(true);

		$result = $Method->invoke($Sut, $AutoLoader);

		$this->assertIsCallable($result);
	}

	/**
	 * @covers ::getLoader
	 *
	 * @return void
	 */
	public function testGetLoader() {
		$Sut = new Manager();
		$Class = new ReflectionClass(Manager::class);
		$Method = $Class->getMethod("getLoader");
		$Property = $Class->getProperty("loaders");
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);

		$Method->setAccessible(true);
		$Property->setAccessible(true);
		$Property->setValue($Sut, ["Yolo" => $AutoLoader]);

		$result = $Method->invoke($Sut, "Yolo");

		$hash0 = spl_object_hash($AutoLoader);
		$hash1 = spl_object_hash($result);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getIdentifier
	 *
	 * @return void
	 */
	public function testGetIdentifierReturnsString() {
		$Sut = new Manager();
		$Method = (new ReflectionClass(Manager::class))
			->getMethod("getIdentifier");

		$Method->setAccessible(true);

		$result = $Method->invoke($Sut, (object)[]);

		$this->assertIsString($result);
	}

	/**
	 * @covers ::getIdentifier
	 *
	 * @return void
	 */
	public function testGetIdentifier() {
		$Object0 = (object)[];
		$Object1 = (object)[];
		$Sut = new Manager();

		$Method = (new ReflectionClass(Manager::class))
			->getMethod("getIdentifier");

		$Method->setAccessible(true);

		$result0 = $Method->invoke($Sut, $Object0);
		$result1 = $Method->invoke($Sut, $Object1);

		$this->assertNotSame($result0, $result1);
	}

	/**
	 * @covers ::removeLoader
	 *
	 * @return void
	 */
	public function testRemoveLoader() {
		$Sut = new Manager();
		$Class = new ReflectionClass(Manager::class);
		$Method = $Class->getMethod("removeLoader");
		$Property = $Class->getProperty("loaders");
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);

		$Property->setAccessible(true);
		$Property->setValue($Sut, ["YOLO" => $AutoLoader]);
		$Method->setAccessible(true);
		$Method->invoke($Sut, "YOLO");

		$this->assertArrayNotHasKey("YOLO", $Property->getValue($Sut));
	}

	/**
	 * @covers ::setLoader
	 */
	public function testSetLoaderSetsProperty() {
		$Sut = new Manager();
		$Class = new ReflectionClass(Manager::class);
		$Method = $Class->getMethod("setLoader");
		$Property = $Class->getProperty("loaders");
		$AutoLoader = $this->createStub(AutoLoaderInterface::class);

		$Method->setAccessible(true);
		$Method->invoke($Sut, "Yolo", $AutoLoader);

		$Property->setAccessible(true);

		$loaders = $Property->getValue($Sut);

		$this->assertArrayHasKey("Yolo", $loaders);

		return [$AutoLoader, $loaders];
	}

	/**
	 * @coversNothing
	 * @depends testSetLoaderSetsProperty
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetLoaderSetsPropertyAssignsLoader($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]["Yolo"]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::wrapSplAutoloadRegister
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @return void
	 */
	public function testWrapSplAutoloadRegister() {
		spl_autoload_register(function(){});

		$autoloaders = spl_autoload_functions();
		$Sut = new Manager();
		$Method = (new ReflectionClass(Manager::class))
			->getMethod("wrapSplAutoloadRegister");

		array_unshift($autoloaders, "print_r");

		$Method->setAccessible(true);
		$Method->invoke($Sut, "print_r", true);

		$result = spl_autoload_functions();

		spl_autoload_unregister("print_r");

		$this->assertSame($autoloaders, $result);
	}

	/**
	 * @covers ::wrapSplAutoloadUnregister
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 *
	 * @return void
	 */
	public function testWrapSplAutoloadUnregister() {
		spl_autoload_register(function(){});

		$autoloaders = spl_autoload_functions();
		$Sut = new Manager();
		$Method = (new ReflectionClass(Manager::class))
			->getMethod("wrapSplAutoloadUnregister");

		spl_autoload_register("print_r", true);

		$Method->setAccessible(true);
		$Method->invoke($Sut, "print_r");

		$result = spl_autoload_functions();

		$this->assertSame($autoloaders, $result);
	}

}

/* vi:set ts=4 sw=4 noet: */
