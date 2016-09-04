<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 14:31
 */
namespace tests;

use PHPUnit\Framework\TestCase;
use samsonphp\generator\TraitGenerator;

/**
 * Class TraitGeneratorTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class TraitGeneratorTest extends TestCase
{
    /** @var TraitGenerator */
    protected $traitGenerator;

    public function setUp()
    {
        $this->traitGenerator = new TraitGenerator('testClass');
    }

    public function testCode()
    {
        $generated = $this->traitGenerator
            ->defNamespace('testname\space')
            ->code();

        $expected = <<<'PHP'
namespace testname\space;

trait testClass
{
}
PHP;

        static::assertEquals($expected, $generated);
    }
}
