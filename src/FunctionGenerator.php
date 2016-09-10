<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:30
 */
namespace samsonframework\generator;

/**
 * Function generation class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FunctionGenerator extends AbstractGenerator
{
    use CodeTrait;

    /** @var string Function name */
    protected $name;

    /** @var array Collection of function arguments */
    protected $arguments = [];

    /** @var array Collection of function arguments descriptions */
    protected $argumentDescriptions = [];

    /** @var array Collection of function arguments default values */
    protected $argumentDefaults = [];

    /** @var string Return type hint */
    protected $returnType;

    /**
     * FunctionGenerator constructor.
     *
     * @param string            $name   Function name
     * @param AbstractGenerator $parent Parent Generator
     */
    public function __construct(string $name, AbstractGenerator $parent = null)
    {
        $this->name = $name;

        parent::__construct($parent);
    }

    /**
     * Set function argument.
     *
     * @param string      $name        Argument name
     * @param string|null $type        Argument type
     * @param string      $description Argument description
     * @param mixed      $defaultValue Argument default value
     *
     * @return FunctionGenerator
     */
    public function defArgument(string $name, string $type = null, string $description = null, $defaultValue = null) : FunctionGenerator
    {
        $this->arguments[$name] = $type;
        $this->argumentDescriptions[$name] = $description;
        $this->argumentDefaults[$name] = $defaultValue;

        return $this;
    }

    /**
     * Set return value type
     *
     * @param string|null $type Return type hint
     *
     * @return FunctionGenerator
     */
    public function defReturnType(string $type) : FunctionGenerator
    {
        $this->returnType = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        $innerIndentation = $this->indentation(1);

        // Get return type code
        $returnType = $this->returnType ? ' : ' . $this->returnType : '';

        $formattedCode = [
            $this->buildDefinition() . '(' . $this->buildArguments($this->arguments, $this->argumentDefaults) . ')' . $returnType,
            '{'
        ];

        // Prepend inner indentation to code
        foreach ($this->code as $codeLine) {
            $formattedCode[] = $innerIndentation.$codeLine;
        }
        $formattedCode[] = '}';

        $code = implode("\n" . $this->indentation($indentation), $formattedCode);

        // Add comments
        if (array_key_exists(FunctionCommentsGenerator::class, $this->generatedCode)) {
            $code = $this->generatedCode[FunctionCommentsGenerator::class] . "\n" . $code;
        }

        return $code;
    }

    /**
     * Build function definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return 'function ' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function defComment() : CommentsGenerator
    {
        return (new FunctionCommentsGenerator($this->arguments, $this->argumentDescriptions, $this))
            ->setIndentation($this->indentation);
    }
}
