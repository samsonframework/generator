<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:30
 */
namespace samsonframework\generator;

/**
 * Class property generation class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class PropertyGenerator extends AbstractGenerator
{
    use VisibilityTrait;

    /** @var string Property name */
    protected $name;

    /** @var string Property value */
    protected $value;

    /**
     * PropertyGenerator constructor.
     *
     * @param string                 $name   Property name
     * @param mixed                  $value  Property value
     * @param AbstractGenerator|null $parent Parent generator
     */
    public function __construct(string $name, $value = null, AbstractGenerator $parent = null)
    {
        $this->name = $name;
        $this->value = $value;

        parent::__construct($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        $code = $this->indentation($indentation)
            . $this->visibility
            . ' '
            . ($this->isStatic ? 'static ' : '')
            . '$'
            . $this->name
            . ';';

        // Add comments
        if (array_key_exists(CommentsGenerator::class, $this->generatedCode)) {
            $code = $this->generatedCode[CommentsGenerator::class] . "\n" . $code;
        }

        return $code;
    }
}
