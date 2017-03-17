<?php declare(strict_types=1);

namespace WikiBundle\Domain\Factory\Payload;

use WikiBundle\Domain\Entity\Payload;

/**
 * Internal payload object creator
 * from data that comes from the outside
 */
interface PayloadFactoryInterface
{
    /**
     * Create a payload from input JSON
     * that comes from an external service
     *
     * @param string $json
     * @return Payload
     */
    public function create(string $json): Payload;
}
