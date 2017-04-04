<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:37
 */
namespace samsonframework\generator;

/**
 * Abstract code generator.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
abstract class AbstractGenerator
{
    /** @var AbstractGenerator Parent class generator */
    protected $parent;

    /** @var array Generated code grouped by generator class name */
    protected $generatedCode = [];

    /** @var int Indentation level */
    protected $indentation = 0;

    /**
     * AbstractGenerator constructor.
     *
     * @param AbstractGenerator $parent Parent generator
     */
    public function __construct(AbstractGenerator $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Close current generator and return parent.
     *
     * @return AbstractGenerator|ClassGenerator|FunctionGenerator|MethodGenerator|PropertyGenerator|ClassConstantGenerator|ConditionGenerator|IfGenerator
     */
    public function end() : AbstractGenerator
    {
        // Generate code
        $generatedCode = $this->code();

        // Avoid creating empty strings
        if ($generatedCode !== '') {
            // Create array item
            $class = get_class($this);
            if (!array_key_exists($class, $this->parent->generatedCode)) {
                $this->parent->generatedCode[$class] = [];
            }

            // Pass generated code to parent
            $this->parent->generatedCode[$class][] = $generatedCode;
        }

        return $this->parent;
    }

    /**
     * Generate code.
     *
     * @return string Generated code
     */
    abstract public function code(): string;

    /**
     * Set Comments block.
     *
     * @return CommentsGenerator Comments block generator
     */
    public function defComment() : CommentsGenerator
    {
        return (new CommentsGenerator($this))->setIndentation($this->indentation);
    }

    /**
     * Decrease indentation.
     *
     * @param int $indentation
     *
     * @return $this|AbstractGenerator|ClassGenerator
     */
    public function setIndentation(int $indentation): AbstractGenerator
    {
        $this->indentation = $indentation;

        return $this;
    }

    /**
     * Build nested class code array.
     *
     * @param string $className     Nested class name
     * @param array  $formattedCode Collection of code
     *
     * @return array Collection of code with added nested class code
     */
    protected function buildNestedCode(string $className, array $formattedCode = []): array
    {
        $code = $this->getNestedCode($className);
        if ($code !== '') {
            $formattedCode[] = $code;
        }

        return $formattedCode;
    }

    /**
     * Get generated nested code.
     *
     * @param string $className Nested class name
     *
     * @return string Generated nested code or empty string
     */
    protected function getNestedCode(string $className): string
    {
        if (array_key_exists($className, $this->generatedCode)) {
            return ltrim(implode("", $this->generatedCode[$className]), "\n");
        } else {
            return '';
        }
    }

    /**
     * Generate correct value.
     *
     * Method handles arrays, numerics, strings and constants.
     *
     * @param mixed $value Value
     *
     * @return mixed Value
     */
    protected function parseValue($value)
    {
        // If item value is array - recursion
        if (is_array($value)) {
            return $this->arrayValue($value);
        } elseif (is_numeric($value) || is_float($value)) {
            return $value;
        } elseif ($value === null) {
            return null;
        } else {
            try { // Try to evaluate
                eval('$value2 = ' . $value . ';');
                return $value;
            } catch (\Throwable $e) { // Consider it as a string
                return '\''.$value.'\'';
            }
        }
    }

    /**
     * Get array values definition.
     *
     * @param array $items Array key-value pairs collection
     *
     * @return string Array value definition
     */
    protected function arrayValue(array $items = array())
    {
        $result = ['['];
        if (count($items)) {
            $this->increaseIndentation();

            // Iterate array items
            foreach ($items as $key => $value) {
                // Start array key definition
                $result[] = "\n"
                    . $this->indentation($this->indentation)
                    . $this->parseValue($key)
                    . ' => '
                    . $this->parseValue($value)
                    . ',';
            }

            $this->decreaseIndentation();
        }
        $result[] = "\n".$this->indentation($this->indentation).']';

        return implode('', $result);
    }

    /**
     * Increase indentation.
     *
     * @return $this|AbstractGenerator
     */
    public function increaseIndentation(): AbstractGenerator
    {
        return $this->setIndentation($this->indentation + 1);
    }

    /**
     * Get indentation string.
     *
     * @param int $indentation Code level
     *
     * @return string Indentation string
     */
    protected function indentation(int $indentation = 0): string
    {
        return implode('', $indentation > 0 ? array_fill(0, $indentation, '    ') : []);
    }

    /**
     * Decrease indentation.
     *
     * @return $this|AbstractGenerator
     */
    public function decreaseIndentation(): AbstractGenerator
    {
        return $this->setIndentation($this->indentation - 1);
    }
}
