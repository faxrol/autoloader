<?php
namespace Laz0r\AutoLoaderTest;

use Laz0r\AutoLoader\{AutoLoaderInterface, Builder};
use Laz0r\AutoLoader\Exception\InvalidAutoLoaderException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;

/**
 * @coversDefaultClass \Laz0r\AutoLoader\Builder
 */
class BuilderTest extends TestCase {

	public static function setupBeforeClass(): void {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/Exception/ExceptionInterface.php";
		require_once __DIR__ . "/../src/Exception/InvalidAutoLoaderException.php";
		require_once __DIR__ . "/../src/BuilderInterface.php";
		require_once __DIR__ . "/../src/Builder.php";
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructWithArgument() {
		$Constructor = (new ReflectionClass(Builder::class))
			->getConstructor();
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["setProduct"])
			->getMock();

		$MockSut->expects($this->once())
			->method("setProduct")
			->with($this->equalTo("ACME\\Anvil"));

		$Constructor->invoke($MockSut, "ACME\\Anvil");
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructWithoutArgument() {
		$Constructor = (new ReflectionClass(Builder::class))
			->getConstructor();
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["setProduct"])
			->getMock();

		$MockSut->expects($this->once())
			->method("setProduct")
			->with($this->isType("string"));

		$Constructor->invoke($MockSut);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithExistingNamespaceAppend() {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\We\\")
			->will($this->returnValue("We"));
		$MockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/have/")
			->will($this->returnValue("/signal"));

		$Property->setAccessible(true);
		$Property->setValue($MockSut, ["We" => ["/have"]]);

		$result = $MockSut->add("\\We\\", "/have/", false);

		return [$MockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespaceAppend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespaceAppendReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespaceAppend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespaceAppendProperty($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$Property->setAccessible(true);

		$value = $Property->getValue($result[0]);

		$this->assertSame(["We" => ["/have", "/signal"]], $value);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithExistingNamespacePrepend() {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\We\\")
			->will($this->returnValue("We"));
		$MockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/signal/")
			->will($this->returnValue("/have"));

		$Property->setAccessible(true);
		$Property->setValue($MockSut, ["We" => ["/signal"]]);

		$result = $MockSut->add("\\We\\", "/signal/", true);

		return [$MockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespacePrepend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespacePrependReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespacePrepend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespacePrependProperty($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$Property->setAccessible(true);

		$value = $Property->getValue($result[0]);

		$this->assertSame(["We" => ["/have", "/signal"]], $value);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithNewNamespace() {
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\Laz0r\\")
			->will($this->returnValue("Laz0r"));
		$MockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/laz0r/")
			->will($this->returnValue("/laz0r"));

		$result = $MockSut->add("\\Laz0r\\", "/laz0r/");

		return [$MockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithNewNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithNewNamespaceReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithNewNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithNewNamespaceProperty($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$Property->setAccessible(true);

		$value = $Property->getValue($result[0]);

		$this->assertSame(["Laz0r" => ["/laz0r"]], $value);
	}

	/**
	 * @covers ::build
	 *
	 * @return array
	 */
	public function testBuild() {
		$Stub0 = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$Stub1 = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["createLoader", "configureLoader"])
			->getMock();

		$MockSut->expects($this->once())
			->method("createLoader")
			->will($this->returnValue($Stub0));
		$MockSut->expects($this->once())
			->method("configureLoader")
			->with($this->identicalTo($Stub0))
			->will($this->returnValue($Stub1));

		$result = $MockSut->build();

		return [$Stub1, $result];
	}

	/**
	 * @coversNothing
	 * @depends testBuild
	 * @param array $result
	 *
	 * @return void
	 */
	public function testBuildReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getProduct
	 *
	 * @return void
	 */
	public function testGetProduct() {
		$Class = new ReflectionClass(Builder::class);
		$Property = $Class->getProperty("product");
		$Instance = $Class->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Instance, "Yolo");

		$result = $Instance->getProduct();

		$this->assertSame("Yolo", $result);
	}

	/**
	 * @covers ::set
	 *
	 * @return array
	 */
	public function testSet() {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$MockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\Laz0r\\")
			->will($this->returnValue("Laz0r"));
		$MockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/laz0r/src/")
			->will($this->returnValue("/laz0r/src"));

		$Property->setAccessible(true);
		$Property->setValue($MockSut, ["We" => ["/have", "/signal"]]);

		$result = $MockSut->set("\\Laz0r\\", "/laz0r/src/");

		return [$MockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetPropertyHasExistingNamespace($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$Property->setAccessible(true);

		$namespaces = $Property->getValue($result[0]);

		$this->assertArrayHasKey("We", $namespaces);
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetPropertyHasNewNamespace($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$Property->setAccessible(true);

		$namespaces = $Property->getValue($result[0]);

		$this->assertArrayHasKey("Laz0r", $namespaces);

		return $namespaces;
	}

	/**
	 * @coversNothing
	 * @depends testSetPropertyHasNewNamespace
	 * @param array $namespaces
	 *
	 * @return void
	 */
	public function testSetPropertyHasNewPath($namespaces) {
		$this->assertSame(["/laz0r/src"], $namespaces["Laz0r"]);
	}

	/**
	 * @covers ::setProduct
	 *
	 * @return array
	 */
	public function testSetProduct() {
		$qcn = get_class($this->createStub(AutoLoaderInterface::class));
		$Instance = (new ReflectionClass(Builder::class))
			->newInstanceWithoutConstructor();
		$result = $Instance->setProduct($qcn);

		$this->assertTrue(true);

		return [$qcn, $Instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetProduct
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetProductReturn($result) {
		$hash0 = spl_object_hash($result[1]);
		$hash1 = spl_object_hash($result[2]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testSetProduct
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetProductProperty($result) {
		$Property = (new ReflectionClass(Builder::class))
			->getProperty("product");

		$Property->setAccessible(true);

		$this->assertSame($result[0], $Property->getValue($result[1]));
	}

	/**
	 * @covers ::setProduct
	 *
	 * @return void
	 */
	public function testSetProductThrowsException() {
		$this->expectException(InvalidAutoLoaderException::class);

		$Instance = (new ReflectionClass(Builder::class))
			->newInstanceWithoutConstructor();

		$Instance->setProduct("Yolo");
	}

	/**
	 * @covers ::setProduct
	 *
	 * @return void
	 */
	public function testSetProductExceptionPropertyNotChanged() {
		$Class = new ReflectionClass(Builder::class);
		$Instance = $Class->newInstanceWithoutConstructor();
		$Property = $Class->getProperty("product");

		$Property->setAccessible(true);
		$Property->setValue($Instance, "Laz0r");

		try {
			$Instance->setProduct("ACME\\Anvil");
		}
		catch (Throwable $ex) {
		}

		$this->assertSame("Laz0r", $Property->getValue($Instance));
	}

	/**
	 * @covers ::configureLoader
	 *
	 * @return array
	 */
	public function testConfigureLoader() {
		$namespaces = [
			"Laz0r" => ["/laz0r/src"],
			"Series" => ["/of", "/tubes"],
		];
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["getAll"])
			->getMock();
		$AutoLoader = new class() implements AutoLoaderInterface {
			public function load(string $qcn): void {
			}
		};
		$Method = (new ReflectionClass(Builder::class))
			->getMethod("configureLoader");

		$MockSut->expects($this->once())
			->method("getAll")
			->will($this->returnValue($namespaces));

		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut, $AutoLoader);

		return [$AutoLoader, $result];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyZero($result) {
		$this->assertObjectHasAttribute("Laz0r", $result[0]);

		return $result[0];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoaderSetsPropertyZero
	 * @param object $AutoLoader
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyZeroValue($AutoLoader) {
		$this->assertSame(["/laz0r/src"], $AutoLoader->Laz0r);
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyOne($result) {
		$this->assertObjectHasAttribute("Series", $result[0]);

		return $result[0];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoaderSetsPropertyZero
	 * @param object $AutoLoader
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyOneValue($AutoLoader) {
		$this->assertSame(["/of", "/tubes"], $AutoLoader->Series);
	}

	/**
	 * @covers ::createLoader
	 *
	 * @return array
	 */
	public function testCreateLoader() {
		$qcn = get_class($this->createStub(AutoLoaderInterface::class));
		$MockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["getProduct"])
			->getMock();
		$Method = (new ReflectionClass(Builder::class))
			->getMethod("createLoader");

		$MockSut->expects($this->once())
			->method("getProduct")
			->will($this->returnValue($qcn));

		$Method->setAccessible(true);

		$result = $Method->invoke($MockSut);

		return [$qcn, $result];
	}

	/**
	 * @coversNothing
	 * @depends testCreateLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testCreateLoaderReturnsObject($result) {
		$this->assertIsObject($result[1]);
	}

	/**
	 * @coversNothing
	 * @depends testCreateLoader
	 * @depends testCreateLoaderReturnsObject
	 * @param array $result
	 *
	 * @return void
	 */
	public function testCreateLoaderReturnsInstanceOfProduct($result) {
		$this->assertInstanceOf($result[0], $result[1]);
	}

	/**
	 * @covers ::getAll
	 *
	 * @return void
	 */
	public function testGetAll() {
		$Class = new ReflectionClass(Builder::class);
		$Method = $Class->getMethod("getAll");
		$Property = $Class->getProperty("namespaces");
		$Instance = $Class->newInstanceWithoutConstructor();

		$Method->setAccessible(true);
		$Property->setAccessible(true);
		$Property->setValue($Instance, ["Laz0r" => ["/laz0r"]]);

		$result = $Method->invoke($Instance);

		$this->assertSame(["Laz0r" => ["/laz0r"]], $result);
	}

	/**
	 * @covers ::canonicalizeNamespace
	 *
	 * @return void
	 */
	public function testCanonicalizeNamespace() {
		$Class = new ReflectionClass(Builder::class);
		$Method = $Class->getMethod("canonicalizeNamespace");
		$Instance = $Class->newInstanceWithoutConstructor();

		$Method->setAccessible(true);

		$result = $Method->invoke($Instance, "\\Laz0r\\AutoLoader\\");

		$this->assertSame("Laz0r\\AutoLoader", $result);
	}

	/**
	 * @covers ::canonicalizePath
	 *
	 * @return void
	 */
	public function testCanonicalizePath() {
		$Class = new ReflectionClass(Builder::class);
		$Method = $Class->getMethod("canonicalizePath");
		$Instance = $Class->newInstanceWithoutConstructor();

		$Method->setAccessible(true);

		$result = $Method->invoke($Instance, "/laz0r/autoloader/");

		$this->assertSame("/laz0r/autoloader/", $result);
	}

}

/* vi:set ts=4 sw=4 noet: */
