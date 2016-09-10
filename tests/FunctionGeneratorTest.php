<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\FunctionGenerator;

/**
 * Class GenericGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FunctionGeneratorTest extends TestCase
{
    /** @var FunctionGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new FunctionGenerator('testFunction');
    }

    public function testDefFunction()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defLine($code)->code();
        $expected = <<<PHP
function testFunction()
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFunctionWithComments()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator
            ->defArgument('testArgument', 'SuperType', 'Description for argument')
            ->defComment()
            ->defLine('Test comment line')
            ->defLine('Test comment line2')
            ->end()
            ->defLine($code)
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment line
 * Test comment line2
 * @param SuperType $testArgument Description for argument
 */
function testFunction(SuperType $testArgument)
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFunctionWithArgument()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defArgument('testArgument')->defLine($code)->code();
        $expected = <<<'PHP'
function testFunction($testArgument)
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFunctionWithTypedArgument()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator->defArgument('testArgument', 'array')->defLine($code)->code();
        $expected = <<<'PHP'
function testFunction(array $testArgument)
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefReturnTypeFunction()
    {
        $code = 'return [];';
        $generated = $this->generator->defReturnType('array')->defLine($code)->code();
        $expected = <<<'PHP'
function testFunction() : array
{
    return [];
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFunctionWithMultipleTypedArgument()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator
            ->defArgument('testArgument', 'array')
            ->defArgument('testArgument2')
            ->defArgument('testArgument3', 'TestType')
            ->defLine($code)
            ->code();
        $expected = <<<'PHP'
function testFunction(array $testArgument, $testArgument2, TestType $testArgument3)
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testDefFunctionWithMultipleTypedArgumentAndDefaultValue()
    {
        $code = 'echo(\'test\')';
        $generated = $this->generator
            ->defArgument('testArgument', 'array')
            ->defArgument('testArgument2', 'int', 'Integer', 1)
            ->defArgument('testArgument3', 'TestType')
            ->defLine($code)
            ->defComment()->end()
            ->code();
        $expected = <<<'PHP'
/**
 * @param array $testArgument 
 * @param int $testArgument2 Integer
 * @param TestType $testArgument3 
 */
function testFunction(array $testArgument, int $testArgument2 = 1, TestType $testArgument3)
{
    echo('test')
}
PHP;

        static::assertEquals($expected, $generated);
    }
}
