<?php
//[PHPCOMPRESSOR(remove,start)]
namespace samsonphp\generator;

class Generator
{
    /** Single quote for string value **/
    const QUOTE_SINGLE = "'";

    /** Double quote for string value **/
    const QUOTE_DOUBLE = '"';

    /** @var string Generated code */
    public $code = '';

    /** @var integer Level of code line tabbing for new lines */
    public $tabs = 0;

    /** @var string Current class name */
    public $class;

    /**
     * Add simple text to current code position
     * @param string $text Text to add
     * @return self
     */
    public function text($text = '')
    {
        $this->code .= $text;

        return $this;
    }

    /**
     * Add current tabbing level to current line.
     *
     * @param string $endText Text to add after tabs
     * @param integer $tabs Amount of tabs to add
     * @param string $startText Text to add before tabs
     * @return Generator Chaining
     */
    public function tabs($endText = '', $tabs = null, $startText = '')
    {
        // Generate tabs array
        $tabs = isset($tabs) ? array_fill(0, $tabs, "\t") : array();

        // Add necessary amount of tabs to line and append text
        $this->text($startText.implode('', $tabs) . $endText);

        return $this;
    }

    /**
     * Add new line to code.
     *
     * @param string $text Code to add to new line
     * @param integer $tabs Tabs count
     * @return self
     */
    public function newline($text = '', $tabs = null)
    {
        // If no tabs count is specified set default tabs
        if (!isset($tabs)) {
            $tabs = $this->tabs;
        }

        return $this->tabs($text, $tabs, "\n");
    }

    /**
     * Add single line comment to code
     * @param string $text Comment text
     * @return self Chaining
     */
    public function comment($text = '')
    {
        return isset($text{0}) ? $this->newline("// " . $text) : $this;
    }

    /**
     * Add multi-line comment. If array with one line is passed
     * we create special syntax comment in one line, usually
     * used for class variable definition in more compact form.
     *
     * @param array $lines Array of comments lines
     * @return self Chaining
     */
    public function multicomment(array $lines = array())
    {
        // If array is not empty
        if (sizeof($lines)) {
            $this->newline("/**");

            // Multi-comment with single line
            if (sizeof($lines) === 1) {
                $this->text(' '.$lines[0].' */');
            } else { // Iterate comments lines and if comment line is not empty
                foreach ($lines as $line) {
                    if (isset($line{0})) {
                        $this->newline(" * " . $line);
                    }
                }

                return $this->newline(" */");
            }

        }

        return $this;
    }

    /**
     * Add string value definition
     * @param string $value String value to add
     * @param string $tabs Tabs count
     * @param string $quote Type of quote
     * @return self
     */
    public function stringvalue($value, $tabs = null, $quote = self::QUOTE_SINGLE)
    {
        return $this->tabs($quote . $value . $quote, $tabs);
    }

    /**
     * Add array values definition
     * @param array $items Array key-value pairs collection
     * @return self Chaining
     */
    public function arrayvalue(array $items = array())
    {
        $this->text('array(');
        $this->tabs++;

        // Iterate array items
        foreach ($items as $key => $value) {
            // Start array key definition
            $this->newline()->stringvalue($key)->text(' => ');

            // If item value is array - recursion
            if (is_array($value)) {
                $this->arrayvalue($value)->text(',');
            } else {
                $this->stringvalue($value)->text(',');
            }
        }

        $this->newline(')');
        $this->tabs--;

        return $this;
    }

    /**
     * Add variable definition with array merging.
     *
     * @param string $name Variable name
     * @param array $value Array of key-value items for merging it to other array
     * @param string $arrayName Name of array to merge to, if no is specified - $name is used
     * @return self Chaining
     */
    public function defarraymerge($name, array $value, $arrayName = null)
    {
        // If no other array is specified - set it to current
        if (!isset($arrayName)) {
            $arrayName = $name;
        }

        return $this->defvar($name, $value, ' = array_merge( ' . $arrayName . ', ', '')->text(');');
    }

    /**
     * Add variable definition.
     *
     * @param string $name Variable name
     * @param string $value Variable default value
     * @param string $after String to insert after variable definition
     * @param string $end Closing part of variable definition
     * @param string $quote Type of quote
     * @return Generator Chaining
     */
    public function defVar($name, $value = null, $after = ' = ', $end = ';', $quote = self::QUOTE_SINGLE)
    {
        // Output variable definition
        $this->newline($name);

        // Get variable type
        switch (gettype($value)) {
            case 'integer':
            case 'boolean':
            case 'double':
                $this->text($after)->text($value)->text($end);
                break;
            case 'string':
                $this->text($after)->stringvalue($value, 0, $quote)->text($end);
                break;
            case 'array':
                $this->text($after)->arrayvalue($value)->text($end);
                break;
            case 'NULL':
            case 'object':
            case 'resource':
            default:
                $this->text(';');
        }

        return $this;
    }

    /**
     * Add class definition.
     *
     * @param string $name Class name
     * @param string $extends Parent class name
     * @param array $implements Interfaces names collection
     * @return self Chaining
     */
    public function defClass($name, $extends = null, array $implements = array())
    {
        // If we define another class, and we were in other class context
        if (isset($this->class) && ($name !== $this->class)) {
            // Close old class context
            $this->endclass();
        }

        // Save new class name
        $this->class = $name;

        // Class definition start
        $this->newline('class ' . $name);

        // Parent class definition
        if (isset($extends)) {
            $this->text(' extends ' . $extends);
        }

        // Interfaces
        if (sizeof($implements)) {
            $this->text(' implements ' . implode(',', $implements));
        }

        $this->newline('{');

        $this->tabs++;

        return $this;
    }

    /**
     * Close current class context.
     *
     * @return self Chaining
     */
    public function endclass()
    {
        // Close class definition
        $this->newline('}')
            // Add one empty line after class definition
        ->newline('');

        $this->tabs--;

        return $this;
    }

    /**
     * Add class variable definition.
     *
     * @param string $name Variable name
     * @param string $visibility Variable accessibility level
     * @param string|null $comment Variable description
     * @param string $value Variable default value
     * @return self Chaining
     */
    public function defClassVar($name, $visibility = 'public', $comment = null, $value = null)
    {
        if (isset($comment) && isset($comment{0})) {
            $this->multicomment(array($comment));
        }

        return $this->defvar($visibility . ' ' . $name, $value)->newline();
    }

    /**
     * Add class constant definition.
     *
     * @param string $name Constant name
     * @param string $value Variable default value
     * @param string|null $comment Variable description
     * @return self Chaining
     */
    public function defClassConst($name, $value, $comment = null)
    {
        return $this->defClassVar(strtoupper($name), 'const', $comment, $value);
    }

    /**
     * Write file to disk
     * @param string $name Path to file
     * @param string $format Output file format
     */
    public function write($name, $format = 'php')
    {
        $code = $this->flush();

        if ($format === 'php') {
            $code = '<?php ' . $code;
        }

        file_put_contents($name, $code, 0775);
    }

    /**
     * Flush internal data and return it.
     *
     * @return string Current generated code
     */
    public function flush()
    {
        // We should use 4 spaces instead of tabs
        $code = str_replace("\t", '    ', $this->code);

        $this->tabs = 0;
        $this->code = '';
        $this->class = null;

        return $code;
    }

    /**
     * Add function definition
     * @param string $name Function name
     * @return self Chaining
     */
    public function deffunction($name)
    {
        return $this->newline('function ' . $name . '()')
            ->newline('{')
            ->tabs('', 1);
    }

    /**
     * Close current function context
     * @return\samson\activerecord\Generator
     */
    public function endfunction()
    {
        return $this->newline('}')->newline('');
    }

    /**
     * Constructor
     * @param string $namespace Code namespace
     */
    public function __construct($namespace = null)
    {
        // If namespace is defined - set it
        if (isset($namespace)) {
            $this->defnamespace($namespace);
        }
    }

    /**
     * Add namespace declaration
     * @param string $name Namespace name
     * @return self
     */
    private function defnamespace($name)
    {
        return $this->newline('namespace ' . $name . ';')->newline();
    }
}
//[PHPCOMPRESSOR(remove,end)]
