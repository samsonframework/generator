<?php declare(strict_types=1);
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

    /** @var CommentsGenerator */
    protected $commentGenerator;

    /**
     * FunctionGenerator constructor.
     *
     * @param string            $name   Function name
     * @param AbstractGenerator $parent Parent Generator
     */
    public function __construct(string $name, AbstractGenerator $parent = null)
    {
        $this->name = $name;

        $this->commentGenerator = (new CommentsGenerator($this))
            ->setIndentation($this->indentation);

        parent::__construct($parent);
    }

    /**
     * Set function argument.
     *
     * @param string      $name         Argument name
     * @param string|null $type         Argument type
     * @param string      $description  Argument description
     * @param mixed       $defaultValue Argument default value
     *
     * @return FunctionGenerator
     */
    public function defArgument(
        string $name,
        string $type = null,
        string $description = null,
        $defaultValue = null
    ): FunctionGenerator
    {
        $this->arguments[$name] = $type;
        $this->argumentDescriptions[$name] = $description;
        $this->argumentDefaults[$name] = $defaultValue;

        $this->commentGenerator->defParam($name, $type, $description);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setIndentation(int $indentation): AbstractGenerator
    {
        // Set comments indentation
        $this->commentGenerator->setIndentation($indentation);

        return parent::setIndentation($indentation);
    }

    /**
     * Set function description comment.
     *
     * @param array $description Function description
     *
     * @return FunctionGenerator
     */
    public function defDescription(array $description): FunctionGenerator
    {
        // Append description lines
        foreach ($description as $line) {
            $this->commentGenerator->defLine($line);
        }

        // Add one empty line at the end
        $this->commentGenerator->defLine('');

        return $this;
    }

    /**
     * Set return value type
     *
     * @param string|null $type Return type hint
     *
     * @return FunctionGenerator
     */
    public function defReturnType(string $type): FunctionGenerator
    {
        $this->returnType = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function code(): string
    {
        $formattedCode[] =
            $this->indentation($this->indentation) . $this->buildDefinition()
            . '(' . $this->buildArguments($this->arguments, $this->argumentDefaults) . ')'
            . ($this->returnType ? ' : ' . $this->returnType : '');

        // Prepend inner indentation to code
        $innerIndentation = $this->indentation(1);
        $formattedCode[] = '{';
        foreach ($this->code as $codeLine) {
            $formattedCode[] = $innerIndentation . $codeLine;
        }
        $formattedCode[] = '}';

        $comments = $this->getNestedCode(CommentsGenerator::class);

        return "\n\n" . ($comments !== '' ? $comments . "\n" : '') . implode("\n" . $this->indentation($this->indentation), $formattedCode);
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
    public function defComment(): CommentsGenerator
    {
        return $this->commentGenerator;
    }
}
