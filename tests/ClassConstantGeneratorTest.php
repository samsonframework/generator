<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 15:32
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonframework\generator\ClassConstantGenerator;

/**
 * Class ClassConstantGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassConstantGeneratorTest extends TestCase
{
    /** @var ClassConstantGenerator */
    protected $generator;

    public function setUp()
    {
        $this->generator = new ClassConstantGenerator('testProperty');
    }

    public function testConstant()
    {
        $generated = $this->generator->code();

        $expected = <<<'PHP'
const testProperty;
PHP;

        static::assertEquals($expected, $generated);
    }
}
