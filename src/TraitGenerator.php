<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 14:30
 */
namespace samsonframework\generator;

/**
 * Class TraitGenerator
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class TraitGenerator extends ClassGenerator
{
    /**
     * Build class definition.
     *
     * @return string Function definition
     */
    protected function buildDefinition()
    {
        return 'trait ' . $this->className;
    }
}
