<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 15:29
 */
namespace samsonframework\generator;

/**
 * Class ClassConstantGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassConstantGenerator extends PropertyGenerator
{
    /**
     * @inheritdoc
     */
    public function code(): string
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
