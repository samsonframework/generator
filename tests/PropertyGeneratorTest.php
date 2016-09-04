<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 12:04
 */
namespace samsonframework\generator\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\PropertyGenerator;

/**
 * Class PropertyGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class PropertyGeneratorTest extends TestCase
{
    /** @var PropertyGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new PropertyGenerator('testProperty');
    }

    public function testProperty()
    {
        $generated = $this->generator->code();

        $expected = <<<'PHP'
public $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testProtectedProperty()
    {
        $generated = $this->generator->defProtected()->code();

        $expected = <<<'PHP'
protected $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPrivateProperty()
    {
        $generated = $this->generator->defPrivate()->code();

        $expected = <<<'PHP'
private $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithTypeHint()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithStringValue()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->defValue('I am string')
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty = 'I am string';
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithNullValue()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->defValue(null)
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithIntValue()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->defValue(1)
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty = 1;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithFloatValue()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->defValue(1.4)
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty = 1.4;
PHP;

        static::assertEquals($expected, $generated);
    }

    public function testPropertyWithArrayValue()
    {
        $generated = $this->generator->defPrivate()
            ->defComment()
            ->defVar('testType', 'Test description')
            ->end()
            ->defValue([1 => ['test' => 'PropertyGeneratorTest::class'], 'catch' => 2.33])
            ->code();

        $expected = <<<'PHP'
/** @var testType Test description */
private $testProperty = [
    1 => [
        'test' => PropertyGeneratorTest::class,
    ],
    'catch' => 2.33,
];
PHP;

        static::assertEquals($expected, $generated);
    }
}
