<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\CommentsGenerator;

/**
 * Class GenericGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class CommentsGeneratorTest extends TestCase
{
    /** @var CommentsGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new CommentsGenerator();
    }

    public function testMultiLineComment()
    {
        $generated = $this->generator
                ->defLine('Test comment line')
                ->defLine('Test comment line2')
                ->code();

        $expected = <<<PHP
/**
 * Test comment line
 * Test comment line2
 */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testSingleLineComment()
    {
        $generated = $this->generator
            ->defLine('Test comment line')
            ->code();

        $expected = <<<PHP
/** Test comment line */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testVarComment()
    {
        $generated = $this->generator
            ->defVar('testType', 'Test description')
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testVarMultilineComment()
    {
        $generated = $this->generator
            ->defLine('Test comment')
            ->defVar('testType', 'Test description')
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment
 * @var testType Test description
 */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testParamComment()
    {
        $generated = $this->generator
            ->defLine('Test comment')
            ->defParam('testName', 'testType', 'Test description')
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment
 * @param testType $testName Test description
 */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testReturnComment()
    {
        $generated = $this->generator
            ->defLine('Test comment')
            ->defReturn('ReturnType', 'Return description')
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment
 * @return ReturnType Return description
 */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testMethodComment()
    {
        $generated = $this->generator
            ->defLine('Test comment')
            ->defMethod('testMethod', 'testType')
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment
 * @method testType testMethod()
 */
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testMethodWithArgumentsComment()
    {
        $generated = $this->generator
            ->defLine('Test comment')
            ->defMethod('testMethod', 'testType', ['testArgument' => 'TestType'])
            ->code();

        $expected = <<<'PHP'
/**
 * Test comment
 * @method testType testMethod(TestType $testArgument)
 */
PHP;

        static::assertEquals($expected, $generated);
    }
}
