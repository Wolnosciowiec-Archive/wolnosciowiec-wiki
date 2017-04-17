<?php declare(strict_types=1);

namespace WikiBundle\Domain\Context;

abstract class AbstractContext
{
    public function __construct(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $methodName = 'set' . ucfirst((string)$name);

            if (!method_exists($this, $methodName)) {
                throw new \InvalidArgumentException('Invalid parameter "' . $name . '" passed to the $parameters');
            }

            $this->$methodName($value);
        }
    }
}
