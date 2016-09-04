<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 12:03
 */
namespace samsonframework\generator;

/**
 * Class VisibilityTrait
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
trait VisibilityTrait
{
    /** @var string Method visibility */
    protected $visibility = ClassGenerator::VISIBILITY_PUBLIC;

    /** @var bool Flag that method is static */
    protected $isStatic = false;

    /**
     * Set method to be static.
     *
     * @return $this
     */
    public function defStatic()
    {
        $this->isStatic = true;

        return $this;
    }

    /**
     * Set protected property visibility.
     *
     * @return $this
     */
    public function defProtected()
    {
        return $this->defVisibility(ClassGenerator::VISIBILITY_PROTECTED);
    }

    /**
     * Set property visibility.
     *
     * @param string $visibility Property visibility
     *
     * @return $this
     */
    protected function defVisibility(string $visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Set private property visibility.
     *
     * @return $this
     */
    public function defPrivate()
    {
        return $this->defVisibility(ClassGenerator::VISIBILITY_PRIVATE);
    }
}
