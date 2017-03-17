<?php declare(strict_types=1);

namespace WikiBundle\Factory\Payload;

use WikiBundle\Domain\Entity\Payload;
use WikiBundle\Domain\Factory\Payload\PayloadFactoryInterface;

/**
 * Combines multiple factories to create a object
 * from different types of input requests
 */
class PayloadFactory implements PayloadFactoryInterface
{
    /**
     * @var PayloadFactoryInterface[] $factories
     */
    private $factories = [];

    /**
     * Used by the inversion of control container
     *
     * @param PayloadFactoryInterface $factory
     * @return PayloadFactoryInterface
     */
    public function addFactory(PayloadFactoryInterface $factory): PayloadFactoryInterface
    {
        $this->factories[] = $factory;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function create(string $json): Payload
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->create($json);
            } catch (\Exception $e) {
                // pass
            }
        }

        return new Payload();
    }
}
