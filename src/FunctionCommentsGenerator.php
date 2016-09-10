<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 10:43
 */
namespace samsonframework\generator;

/**
 * Class FunctionCommentsGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FunctionCommentsGenerator extends CommentsGenerator
{
    /** @var array Argument name to type collection */
    protected $arguments = [];

    /** @var array Argument name to description collection */
    protected $descriptions = [];

    /**
     * FunctionCommentsGenerator constructor.
     *
     * @param array                  $arguments    Argument name to type collection
     * @param array                  $descriptions Argument name to description collection
     * @param AbstractGenerator|null $parent       Parent generator
     */
    public function __construct(array $arguments, array $descriptions, AbstractGenerator $parent = null)
    {
        $this->arguments = $arguments;
        $this->descriptions = $descriptions;

        parent::__construct($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        // Add parameters comments
        foreach ($this->arguments as $argument => $type) {
            $this->defParam($argument, $type, $this->descriptions[$argument] ?? '');
        }

        return parent::code($indentation);
    }
}
