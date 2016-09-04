<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 03.09.16 at 11:30
 */
namespace samsonframework\generator;

/**
 * Class method generation class.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class MethodGenerator extends FunctionGenerator
{
    use VisibilityTrait;
    use AbstractFinalTrait;

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function defLine(string $code)
    {
        if ($this->isAbstract === true) {
            throw new \InvalidArgumentException('Abstract class cannot have code');
        }

        return parent::defLine($code);
    }

    /**
     * {@inheritdoc}
     */
    public function code(int $indentation = 0) : string
    {
        if ($this->isAbstract === true) {
            return $this->buildDefinition() . '(' . $this->buildArguments($this->arguments) . ');';
        } else {
            return parent::code($indentation);
        }
    }

    /**
     * Build function definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return $this->indentation($this->indentation) .
        ($this->isFinal ? 'final ' : '') .
        ($this->isAbstract ? 'abstract ' : '') .
        $this->visibility . ' ' .
        ($this->isStatic ? 'static ' : '') .
        'function ' . $this->name;
    }
}
