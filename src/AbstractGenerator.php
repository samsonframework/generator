<?php declare(strict_types = 1);
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
     * MethodGenerator constructor.
     *
     * @param AbstractGenerator $parent Parent generator
     */
    public function __construct(AbstractGenerator $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Increase indentation.
     *
     * @return $this|AbstractGenerator
     */
    public function increaseIndentation() : AbstractGenerator
    {
        $this->indentation++;

        return $this;
    }

    /**
     * Decrease indentation.
     *
     * @return $this|AbstractGenerator
     */
    public function decreaseIndentation() : AbstractGenerator
    {
        $this->indentation--;

        return $this;
    }

    /**
     * Close current generator and return parent.
     *
     * @return AbstractGenerator|ClassGenerator|FunctionGenerator|MethodGenerator|PropertyGenerator|ClassConstantGenerator
     */
    public function end() : AbstractGenerator
    {
        // Create array item
        $class = get_class($this);
        if (!array_key_exists($class, $this->parent->generatedCode)) {
            $this->parent->generatedCode[$class] = '';
        }

        // Pass generated code to parent
        $this->parent->generatedCode[$class] .= $this->code($this->indentation);

        return $this->parent;
    }

    /**
     * Generate code.
     *
     * @param int $indentation Code level
     *
     * @return string Generated code
     */
    abstract public function code(int $indentation = 0) : string;

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
    public function setIndentation(int $indentation) : AbstractGenerator
    {
        $this->indentation = $indentation;

        return $this;
    }

    /**
     * Get indentation string.
     *
     * @param int $indentation Code level
     *
     * @return string Indentation string
     */
    protected function indentation(int $indentation = 0) : string
    {
        return implode('', $indentation > 0 ? array_fill(0, $indentation, '    ') : []);
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
}
