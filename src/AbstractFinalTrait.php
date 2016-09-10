<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 12:07
 */
namespace samsonframework\generator;

/**
 * Class AbstractFinalTrait
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
trait AbstractFinalTrait
{
    /** @var bool Flag that method is abstract */
    protected $isAbstract = false;

    /** @var bool Flag that method is final */
    protected $isFinal = false;

    /**
     * Set to be final.
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function defFinal()
    {
        if ($this->isAbstract) {
            throw new \InvalidArgumentException('Method cannot be final as it is already abstract');
        }

        $this->isFinal = true;

        return $this;
    }

    /**
     * Set to be abstract.
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function defAbstract()
    {
        if ($this->isFinal) {
            throw new \InvalidArgumentException('Method cannot be abstract as it is already final');
        }

        $this->isAbstract = true;

        return $this;
    }
}
