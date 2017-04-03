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
            ->increaseIndentation()
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
        $code = '';

        // Add child if groups code
        if (array_key_exists(self::class, $this->generatedCode)) {
            $code .= $this->generatedCode[self::class];
        }

        // Add conditions code
        if (array_key_exists(ConditionGenerator::class, $this->generatedCode)) {
            $code .= $this->generatedCode[ConditionGenerator::class];
        }

        // Close condition block
        if ($code !== '') {
            $code .= "\n".'}';
        }

        // Separate code in lines and add to parent code
        if ($code !== '') {
            foreach (explode("\n", $code) as $codeLine) {
                $this->parent->defLine($codeLine);
            }
        }

        return $code;
    }
}
