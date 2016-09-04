<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 15:29
 */
namespace samsonphp\generator;

/**
 * Class ClassConstantGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassConstantGenerator extends PropertyGenerator
{
    /**
     * Generate code.
     *
     * @param int $indentation Code level
     *
     * @return string Generated code
     */
    public function code(int $indentation = 0) : string
    {
        $code = $this->indentation($this->indentation)
            . 'const '
            . $this->name
            . ';';

        // Add comments
        if (array_key_exists(CommentsGenerator::class, $this->generatedCode)) {
            $code = $this->generatedCode[CommentsGenerator::class] . "\n" . $code;
        }

        return $code;
    }
}
