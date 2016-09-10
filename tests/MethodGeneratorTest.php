<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\MethodGenerator;

/**
 * Class GenericGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class MethodGeneratorTest extends TestCase
{
    /** @var MethodGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new MethodGenerator('testMethod');
    }

    public function testDefFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defLine($code)->code();
        $expected = <<<PHP
public function testMethod()
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefStaticFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defStatic()->defLine($code)->code();
        $expected = <<<PHP
public static function testMethod()
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFinalFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defFinal()->defLine($code)->code();
        $expected = <<<PHP
final public function testMethod()
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFinalStaticFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defFinal()->defStatic()->defLine($code)->code();
        $expected = <<<PHP
final public static function testMethod()
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefAbstractFunction()
    {
        $generated = $this->generator->defAbstract()->code();
        $expected = <<<PHP
abstract public function testMethod();
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefAbstractStaticFunction()
    {
        $generated = $this->generator->defAbstract()->defStatic()->code();
        $expected = <<<PHP
abstract public static function testMethod();
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefAbstractFinalFunctionException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->defAbstract()->defFinal()->code();
    }

    public function testDefLineWithAbstractFunctionException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->defAbstract()->defLine('code;')->code();
    }

    public function testDefFinalAbstractFunctionException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->defFinal()->defAbstract()->code();
    }
}
