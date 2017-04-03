<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.04.17 at 08:57
 */

namespace samsonframework\generator;

/**
 * Class IfGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class IfGenerator extends AbstractGenerator
{
    use CodeTrait;

    /** @var bool Flag that condition is first */
    protected $isFirstCondition = true;

    /**
     * @inheritdoc
     */
    public function defCondition(string $definition = ''): ConditionGenerator
    {
        $conditionGenerator = (new ConditionGenerator(!$this->isFirstCondition, $this))
            ->setIndentation($this->indentation)
            ->defDefinition($definition);

        // Change flag
        $this->isFirstCondition = false;

        return $conditionGenerator;
    }

    /**
     * @inheritdoc
     */
    public function code(int $indentation = 0): string
    {
        // Close condition statement
        $this->code[] = $this->indentation($indentation) . '}';

        $code = implode("\n" . $this->indentation($indentation), $this->code);

        // Add conditions code
        if (array_key_exists(ConditionGenerator::class, $this->generatedCode)) {
            $code = $this->generatedCode[ConditionGenerator::class] . "\n" . $code;
        }

        // Add comments
        if (array_key_exists(FunctionCommentsGenerator::class, $this->generatedCode)) {
            $code = $this->generatedCode[FunctionCommentsGenerator::class] . "\n" . $code;
        }

        // Pass code to parent to preserve order
        $this->parent->defLine($code);

        return $code;
    }
}
