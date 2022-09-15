<?php

namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\{
	AutoLoaderInterface,
	BuilderInterface,
	Container,
	ManagerInterface,
};
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\Container
 */
class ContainerTest extends TestCase {

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/BuilderInterface.php";
		require_once __DIR__ . "/../src/ManagerInterface.php";
		require_once __DIR__ . "/../src/Container.php";
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstruct(): void {
		$Constructor = (new ReflectionClass(Container::class))
			->getConstructor();
		$Builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();
		$Manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();
		$MockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["setBuilder", "setManager"])
			->getMock();

		$MockSut->expects($this->once())
			->method("setBuilder")
			->with($this->identicalTo($Builder))
			->will($this->returnSelf());
		$MockSut->expects($this->once())
			->method("setManager")
			->with($this->identicalTo($Manager))
			->will($this->returnSelf());

		$Constructor->invoke($MockSut, $Builder, $Manager);
	}

	/**
	 * @covers ::getBuilder
	 *
	 * @return void
	 */
	public function testGetBuilderClonesBuilder(): void {
		$Stub = $this->createStub(BuilderInterface::class);
		$Class = new ReflectionClass(Container::class);
		$Instance = $Class->newInstanceWithoutConstructor();
		$Property = $Class->getProperty("Builder");

		$Property->setAccessible(true);
		$Property->setValue($Instance, $Stub);

		$Result = $Instance->getBuilder();

		$this->assertNotSame($Stub, $Result);
		$this->assertInstanceOf(get_class($Stub), $Result);
	}

	/**
	 * @covers ::getBuilder
	 *
	 * @return array
	 */
	public function testGetBuilderReturnsBuilderInterface(): array {
		$Class = new ReflectionClass(Container::class);
		$Instance = $Class->newInstanceWithoutConstructor();
		$Property = $Class->getProperty("Builder");
		$Builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();

		$Property->setAccessible(true);
		$Property->setValue($Instance, $Builder);

		$Result = $Instance->getBuilder();

		$this->assertInstanceOf(BuilderInterface::class, $Result);

		return [$Builder, $Result];
	}

	/**
	 * @coversNothing
	 * @depends testGetBuilderReturnsBuilderInterface
	 * @param array $result
	 *
	 * @return void
	 */
	public function testGetBuilderReturnsClone(array $result): void {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertNotSame($hash0, $hash1);
	}

	/**
	 * @covers ::getManager
	 *
	 * @return void
	 */
	public function testGetManager(): void {
		$Class = new ReflectionClass(Container::class);
		$Instance = $Class->newInstanceWithoutConstructor();
		$Property = $Class->getProperty("Manager");
		$Manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();

		$Property->setAccessible(true);
		$Property->setValue($Instance, $Manager);

		$Result = $Instance->getManager();

		$hash0 = spl_object_hash($Manager);
		$hash1 = spl_object_hash($Result);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::register
	 *
	 * @return mixed
	 */
	public function testRegister() {
		$AutoLoader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$Manager = $this->getMockBuilder(ManagerInterface::class)
			->setMethods(["has", "register", "unregister"])
			->getMock();
		$MockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["getManager"])
			->getMock();

		$Manager->expects($this->never())
			->method("has");
		$Manager->expects($this->never())
			->method("unregister");
		$Manager->expects($this->once())
			->method("register")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue("yolo"));
		$MockSut->expects($this->once())
			->method("getManager")
			->will($this->returnValue($Manager));

		return $MockSut->register($AutoLoader);
	}

	/**
	 * @coversNothing
	 * @depends testRegister
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testRegisterReturnsIdentifier($result): void {
		$this->assertSame("yolo", $result);
	}

	/**
	 * @covers ::setBuilder
	 *
	 * @return array
	 */
	public function testSetBuilder(): array {
		$Class = new ReflectionClass(Container::class);
		$Property = $Class->getProperty("Builder");
		$Instance = $Class->newInstanceWithoutConstructor();
		$Builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();

		$Property->setAccessible(true);
		$Property->setValue($Instance, $Builder);

		$result = $Instance->setBuilder($Builder);

		$hash0 = spl_object_hash($Builder);
		$hash1 = spl_object_hash($Property->getValue($Instance));

		$this->assertSame($hash0, $hash1);

		return [$Instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetBuilder
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetBuilderReturnsSelf(array $result): void {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::setManager
	 *
	 * @return array
	 */
	public function testSetManager(): array {
		$Class = new ReflectionClass(Container::class);
		$Property = $Class->getProperty("Manager");
		$Instance = $Class->newInstanceWithoutConstructor();
		$Manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();

		$Property->setAccessible(true);
		$Property->setValue($Instance, $Manager);

		$result = $Instance->setManager($Manager);

		$hash0 = spl_object_hash($Manager);
		$hash1 = spl_object_hash($Property->getValue($Instance));

		$this->assertSame($hash0, $hash1);

		return [$Instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetManager
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetManagerReturnsSelf(array $result): void {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::setup
	 *
	 * @return mixed
	 */
	public function testSetup() {
		$AutoLoader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$Builder = $this->getMockBuilder(BuilderInterface::class)
			->setMethods(["add", "build", "set"])
			->getMock();
		$MockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["getBuilder", "register"])
			->getMock();

		$Builder->expects($this->exactly(2))
			->method("add")
			->withConsecutive(
				[$this->equalTo("Imma"), $this->equalTo("/firin")],
				[$this->equalTo("Mah"), $this->equalTo("/laz0r")],
			)
			->will($this->returnSelf());
		$Builder->expects($this->once())
			->method("build")
			->will($this->returnValue($AutoLoader));
		$Builder->expects($this->never())
			->method("set");

		$MockSut->expects($this->once())
			->method("getBuilder")
			->will($this->returnValue($Builder));
		$MockSut->expects($this->once())
			->method("register")
			->with($this->identicalTo($AutoLoader))
			->will($this->returnValue("xvFZjo5PgG0"));

		$result = $MockSut->setup([
			["Imma", "/firin"],
			["Mah", "/laz0r"],
		]);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSetup
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSetupReturnsIdentifier($result): void {
		$this->assertSame("xvFZjo5PgG0", $result);
	}

}

/* vi:set ts=4 sw=4 noet: */
