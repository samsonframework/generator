<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 04.09.16 at 10:13
 */
namespace samsonframework\generator;

/**
 * Trait for generators that can generate internal code.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
trait CodeTrait
{
    /** @var array Collection of code lines */
    protected $code = [];

    /**
     * Add function code line.
     *
     * @param string $code Code line
     *
     * @return $this
     */
    public function defLine(string $code)
    {
        $this->code[] = $code;

        return $this;
    }

    /**
     * Build arguments list with types.
     *
     * @param array $arguments Arguments collection
     * @param array $defaults Arguments default values collection
     *
     * @return string Arguments list
     */
    protected function buildArguments(array $arguments, array $defaults = []) : string
    {
        $argumentsString = [];
        $defaults = array_filter($defaults);
        foreach ($arguments as $argumentName => $argumentType) {
            // Group name with type
            $argumentsString[] = ($argumentType !== null ? $argumentType . ' ' : '') .
                '$' . $argumentName .
                (array_key_exists($argumentName, $defaults) ? ' = ' . $defaults[$argumentName] : '');
        }

        return implode(', ', $argumentsString);
    }
}
