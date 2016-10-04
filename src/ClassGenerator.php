<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 09:58
 */
namespace samsonframework\generator;

use samsonframework\generator\exception\ClassNameNotFoundException;

/**
 * Class generator class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class ClassGenerator extends AbstractGenerator
{
    use AbstractFinalTrait;

    /** OOP public visibility */
    const VISIBILITY_PUBLIC = 'public';

    /** OOP protected visibility */
    const VISIBILITY_PROTECTED = 'protected';

    /** OOP private visibility */
    const VISIBILITY_PRIVATE = 'private';

    /** @var string Class name */
    protected $className;

    /** @var string Parent class name */
    protected $parentClassName;

    /** @var string Class namespace */
    protected $namespace;

    /** @var array Collection of class uses */
    protected $uses = [];

    /** @var array Collection of class interfaces */
    protected $interfaces = [];

    /** @var array Collection of class used traits */
    protected $traits = [];

    /** @var string Multi-line file description */
    protected $fileDescription;

    /**
     * ClassGenerator constructor.
     *
     * @param string           $className Class name
     * @param AbstractGenerator $parent    Parent generator
     */
    public function __construct(string $className = null, AbstractGenerator $parent = null)
    {
        $this->className = $className;

        parent::__construct($parent);
    }

    /**
     * Set class file description.
     *
     * @param array $description Collection of class file description lines
     *
     * @return ClassGenerator
     */
    public function defDescription(array $description) : ClassGenerator
    {
        $commentsGenerator = new CommentsGenerator($this);
        foreach ($description as $line) {
            $commentsGenerator->defLine($line);
        }

        $this->fileDescription = $commentsGenerator->code();

        return $this;
    }

    /**
     * Set class namespace.
     *
     * @param string $namespace
     *
     * @return ClassGenerator
     */
    public function defNamespace(string $namespace) : ClassGenerator
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set class name.
     *
     * @param string $className
     *
     * @return ClassGenerator
     */
    public function defName(string $className) : ClassGenerator
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Set class use.
     *
     * @param string $use Use class name
     * @param string $alias Use class name
     *
     * @return ClassGenerator
     */
    public function defUse(string $use, string $alias = null) : ClassGenerator
    {
        // Store the alias of class
        if ($alias) {
            $this->uses[$alias] = $use;
        } else {
            $this->uses[] = $use;
        }

        return $this;
    }

    /**
     * Set parent class.
     *
     * @param string $className Parent class name
     *
     * @return ClassGenerator
     */
    public function defExtends(string $className) : ClassGenerator
    {
        $this->parentClassName = $className;

        return $this;
    }

    /**
     * Set implements interfaces.
     *
     * @param string $interfaceName Interface name
     *
     * @return ClassGenerator
     */
    public function defImplements(string $interfaceName) : ClassGenerator
    {
        $this->interfaces[] = $interfaceName;

        return $this;
    }

    /**
     * Set class trait use.
     *
     * @param string $trait Trait class name
     *
     * @return ClassGenerator
     */
    public function defTrait(string $trait) : ClassGenerator
    {
        $this->traits[] = $trait;

        return $this;
    }

    /**
     * Set protected class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProtectedProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defProperty($name, $type, $value, $description)->defProtected();
    }

    /**
     * Set class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProperty(string $name, string $type, $value = null, string $description = null) : PropertyGenerator
    {
        return (new PropertyGenerator($name, $value, $this))
            ->setIndentation($this->indentation)
            ->increaseIndentation()
            ->defComment()
            ->defVar($type, $description)
            ->end();
    }

    /**
     * Set protected static class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defProtectedStaticProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defStaticProperty($name, $type, $value, $description)->defProtected();
    }

    /**
     * Set static class property.
     *
     * @param string $name        Property name
     * @param string $type        Property type
     * @param mixed  $value       Property value
     * @param string $description Property description
     *
     * @return PropertyGenerator
     */
    public function defStaticProperty(string $name, string $type, $value, string $description = null) : PropertyGenerator
    {
        return $this->defProperty($name, $type, $value, $description)->defStatic();
    }

    /**
     * Set protected class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defProtectedMethod(string $name) : MethodGenerator
    {
        return $this->defMethod($name)->defProtected();
    }

    /**
     * Set public class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defMethod(string $name) : MethodGenerator
    {
        return (new MethodGenerator($name, $this))->setIndentation($this->indentation)->increaseIndentation();
    }

    /**
     * Set protected static class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defProtectedStaticMethod(string $name) : MethodGenerator
    {
        return $this->defStaticMethod($name)->defProtected();
    }

    /**
     * Set public static class method.
     *
     * @param string $name Method name
     *
     * @return MethodGenerator
     */
    public function defStaticMethod(string $name) : MethodGenerator
    {
        return $this->defMethod($name)->defStatic();
    }

    /**
     * Set class constant.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return $this|ClassConstantGenerator
     */
    public function defConstant(string $name, $value, string $type, string $description) : ClassConstantGenerator
    {
        return (new ClassConstantGenerator($name, $value, $this))
            ->setIndentation($this->indentation)
            ->increaseIndentation()
            ->defComment()
                ->defLine($type.' '.$description)
            ->end();
    }

    protected function buildUsesCode(array $formattedCode) : array
    {
        // Add uses
        foreach ($this->uses as $alias => $use) {
            $formattedCode[] = 'use ' . $use . (is_string($alias) ? ' as ' . $alias : '') . ';';
        }

        // One empty line after uses if we have them
        if (count($this->uses)) {
            $formattedCode[] = '';
        }

        return $formattedCode;
    }

    protected function buildCommentsCode(array $formattedCode) : array
    {
        // Add comments
        if (array_key_exists(CommentsGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[CommentsGenerator::class];
        }

        return $formattedCode;
    }

    protected function buildTraitsCode(array $formattedCode, string $innerIndentation) : array
    {
        // Add traits
        foreach ($this->traits as $trait) {
            $formattedCode[] = $innerIndentation . 'use ' . $trait . ';';
        }

        // One empty line after traits if we have them
        if (count($this->traits)) {
            $formattedCode[] = '';
        }

        return $formattedCode;
    }

    protected function buildFileDescriptionCode(array $formattedCode) : array
    {
        // Prepend file description if present
        if ($this->fileDescription !== null) {
            array_unshift($formattedCode, $this->fileDescription);
        }

        return $formattedCode;
    }

    protected function buildConstantsCode(array $formattedCode) : array
    {
        // Add constants
        if (array_key_exists(ClassConstantGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[ClassConstantGenerator::class];
        }

        return $formattedCode;
    }

    protected function buildPropertiesCode(array $formattedCode) : array
    {
        if (array_key_exists(PropertyGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[PropertyGenerator::class];
        }

        return $formattedCode;
    }

    protected function buildMethodsCode(array $formattedCode) : array
    {
        if (array_key_exists(MethodGenerator::class, $this->generatedCode)) {
            $formattedCode[] = $this->generatedCode[MethodGenerator::class];
        }

        return $formattedCode;
    }

    protected function buildNamespaceCode(array $formattedCode = []) : array
    {
        if ($this->namespace === null) {
            throw new \InvalidArgumentException('Class namespace should be defined');
        }

        $formattedCode[] = 'namespace ' . $this->namespace . ';';

        // One empty line after namespace
        $formattedCode[] = '';

        return $formattedCode;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @throws ClassNameNotFoundException
     */
    public function code(int $indentation = 0) : string
    {
        if (!$this->className) {
            throw new ClassNameNotFoundException('Class name should be defined');
        }

        $formattedCode = $this->buildNamespaceCode();
        $formattedCode = $this->buildFileDescriptionCode($formattedCode);
        $formattedCode = $this->buildUsesCode($formattedCode);
        $formattedCode = $this->buildCommentsCode($formattedCode);

        // Add previously generated code
        $formattedCode[] = $this->buildDefinition();
        $formattedCode[] = '{';

        $indentationString = $this->indentation($indentation);
        $innerIndentation = $this->indentation(1);

        $formattedCode = $this->buildTraitsCode($formattedCode, $innerIndentation);
        $formattedCode = $this->buildConstantsCode($formattedCode);
        $formattedCode = $this->buildPropertiesCode($formattedCode);
        $formattedCode = $this->buildMethodsCode($formattedCode);

        $formattedCode[] = '}';

        return implode("\n" . $indentationString, $formattedCode);
    }

    /**
     * Build class definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return ($this->isFinal ? 'final ' : '') .
        ($this->isAbstract ? 'abstract ' : '') .
        'class ' .
        $this->className .
        ($this->parentClassName ? ' extends ' . $this->parentClassName : '') .
        (count($this->interfaces) ? rtrim(' implements ' . implode(', ', $this->interfaces), ', ') : '');
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
