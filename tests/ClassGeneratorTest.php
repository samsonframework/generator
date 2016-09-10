<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 10:49
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\ClassGenerator;
use samsonframework\generator\exception\ClassNameNotFoundException;

/**
 * Class ClassGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassGeneratorTest extends TestCase
{
    /** @var ClassGenerator */
    protected $classGenerator;

    public function setUp()
    {
        $this->classGenerator = new ClassGenerator('testClass');
    }

    public function testWithoutNamespaceException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->classGenerator->code();
    }

    public function testDefNamespace()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFinal()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defFinal()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

final class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefAbstract()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defAbstract()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

abstract class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefAbstractFinal()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->classGenerator
            ->defNamespace('testname\space')
            ->defAbstract()
            ->defFinal();
    }

    public function testDefFinalAbstract()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->classGenerator
            ->defNamespace('testname\space')
            ->defFinal()
            ->defAbstract();
    }

    public function testDefUse()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defUse('\testclass\scope\TestClass')
            ->defUse('\testclass\scope2\TestClass')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

use \testclass\scope\TestClass;
use \testclass\scope2\TestClass;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefTrait()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defTrait('\testclass\scope\TestTrait')
            ->defTrait('\testclass\scope2\TestTrait')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    use \testclass\scope\TestTrait;
    use \testclass\scope2\TestTrait;

}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefComment()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defComment()
            ->defLine('Test comment')
            ->defMethod('testMethod', 'TestType')
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

/**
 * Test comment
 * @method TestType testMethod()
 */
class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defDescription(['File description'])
            ->code();

        $expected = <<<'PHP'
/** File description */
namespace testname\space;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefProperty()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProperty('testProperty', 'TestType')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType */
    public $testProperty;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefPropertyWithDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProperty('testProperty', 'TestType', null, 'Property description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType Property description */
    public $testProperty;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefProtectedPropertyWithDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProtectedProperty('testProperty', 'TestType', null, 'Property description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType Property description */
    protected $testProperty;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefStaticPropertyWithDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defStaticProperty('testProperty', 'TestType', null, 'Property description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType Property description */
    public static $testProperty;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefStaticProtectedPropertyWithDescription()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProtectedStaticProperty('testProperty', 'TestType', null, 'Property description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType Property description */
    protected static $testProperty;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefMethod()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defMethod('testMethod')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    public function testMethod()
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefMethodWithArguments()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defMethod('testMethod')
            ->defArgument('testArgument', 'TestType', 'Test description')
            ->defComment()->end()
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @param TestType $testArgument Test description */
    public function testMethod(TestType $testArgument)
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefMethodWithArgumentsAndComment()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defMethod('testMethod')
            ->defArgument('testArgument', 'TestType', 'Test description')
            ->defComment()->defLine('Test comment')->end()
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /**
     * Test comment
     * @param TestType $testArgument Test description
     */
    public function testMethod(TestType $testArgument)
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefProtectedMethodWithArgumentsAndComment()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProtectedMethod('testMethod')
            ->defArgument('testArgument', 'TestType', 'Test description')
            ->defComment()->defLine('Test comment')->end()
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /**
     * Test comment
     * @param TestType $testArgument Test description
     */
    protected function testMethod(TestType $testArgument)
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefStaticMethodWithArgumentsAndComment()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defStaticMethod('testMethod')
            ->defArgument('testArgument', 'TestType', 'Test description')
            ->defComment()->defLine('Test comment')->end()
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /**
     * Test comment
     * @param TestType $testArgument Test description
     */
    public static function testMethod(TestType $testArgument)
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefProtectedStaticMethodWithArgumentsAndComment()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProtectedStaticMethod('testMethod')
            ->defArgument('testArgument', 'TestType', 'Test description')
            ->defComment()->defLine('Test comment')->end()
            ->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /**
     * Test comment
     * @param TestType $testArgument Test description
     */
    protected static function testMethod(TestType $testArgument)
    {
    }
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefConstant()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defConstant('testConst', '', 'TestType', 'Constant description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** TestType Constant description */
    const testConst;
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefPropertyWithStringValue()
    {
        $generated = $this->classGenerator
            ->defNamespace('testname\space')
            ->defProperty('testProperty', 'TestType', 'I am string', 'Property description')->end()
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass
{
    /** @var TestType Property description */
    public $testProperty = 'I am string';
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefClassParent()
    {
        $classGenerator = new ClassGenerator('testClass');
        $generated = $classGenerator
            ->defNamespace('testname\space')
            ->defExtends('ParentClass')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass extends ParentClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefClassImplementsOneInterface()
    {
        $classGenerator = new ClassGenerator('testClass');
        $generated = $classGenerator
            ->defNamespace('testname\space')
            ->defImplements('FirstInterface')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass implements FirstInterface
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefClassImplementsManyInterface()
    {
        $classGenerator = new ClassGenerator('testClass');
        $generated = $classGenerator
            ->defNamespace('testname\space')
            ->defImplements('FirstInterface')
            ->defImplements('SecondInterface')
            ->defImplements('ThirdInterface')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass implements FirstInterface, SecondInterface, ThirdInterface
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefClassImplementsManyInterfaceWithExtends()
    {
        $classGenerator = new ClassGenerator('testClass');
        $generated = $classGenerator
            ->defNamespace('testname\space')
            ->defExtends('ParentClass')
            ->defImplements('FirstInterface')
            ->defImplements('SecondInterface')
            ->defImplements('ThirdInterface')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

class testClass extends ParentClass implements FirstInterface, SecondInterface, ThirdInterface
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefUsesWithAsOperator()
    {
        $classGenerator = new ClassGenerator('testClass');
        $generated = $classGenerator
            ->defNamespace('testname\space')
            ->defUse('\testname\space1\UsefulClass', 'VeryUsefulClass')
            ->defUse('\testname\space2\SimpleClass')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

use \testname\space1\UsefulClass as VeryUsefulClass;
use \testname\space2\SimpleClass;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefClassNameNotFoundException()
    {
        $this->expectException(ClassNameNotFoundException::class);

        (new ClassGenerator())
            ->defNamespace('\test\space')
            ->code();
    }

    public function testDefDefClassName()
    {
        $classGenerator = new ClassGenerator();
        $generated = $classGenerator
            ->defName('testClass')
            ->defNamespace('\test\space')
            ->code();

        $expected = <<<'PHP'
namespace \test\space;

class testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }
}
