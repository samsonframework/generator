<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.04.17 at 08:02
 */

namespace samsonframework\generator;

/**
 * Class ConditionGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ConditionGenerator extends AbstractGenerator
{
    use CodeTrait;

    /** @var string Condition statement definition */
    protected $definition = '';

    /** @var bool If condition statement is nested */
    protected $nested;

    /**
     * ConditionGenerator constructor.
     *
     * @param bool                   $nested Flag if condition statement is nested
     * @param AbstractGenerator|null $parent Parent generator
     */
    public function __construct(bool $nested = false, AbstractGenerator $parent = null)
    {
        $this->nested = $nested;

        parent::__construct($parent);
    }

    /**
     * Set condition statement definition.
     *
     * @param string $definition Condition statement definition
     *
     * @return ConditionGenerator Condition generator
     */
    public function defDefinition(string $definition): ConditionGenerator
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception If condition is not nested and definition is not defined
     */
    public function code(): string
    {
        $innerIndentation = $this->indentation(1);

        // Generate condition statement definition code
        if ($this->definition === '') {
            if ($this->nested) { // Empty definition means else statement
                $formattedCode =  ["\n".'} else {'];
            } else {
                throw new \Exception('Cannot create not nested condition statement with empty definition');
            }
        } else { // Regular condition statement beginning
            $formattedCode = [
                ($this->nested
                    ? "\n".'} elseif ('
                    : 'if (') . $this->definition . ') {'
            ];
        }

        // Prepend inner indentation to code
        foreach ($this->code as $codeLine) {
            $formattedCode[] = $innerIndentation . $codeLine;
        }

        return implode("\n", $formattedCode);
    }
}
