<?php declare(strict_types=1);

namespace WikiBundle\Factory\Payload;

use WikiBundle\Domain\Entity\Payload;
use WikiBundle\Domain\Factory\Payload\PayloadFactoryInterface;

/**
 * Parses a github payload
 */
class GithubPayloadFactory implements PayloadFactoryInterface
{
    public function create(string $json): Payload
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \Exception('Input payload is not a JSON');
        }

        if (!isset($data['repository']['url']) || !isset($data['repository']['default_branch'])) {
            throw new \Exception('Probably not a github payload');
        }

        return new Payload(
            $data['repository']['url'] ?? '',
            $data['repository']['default_branch'] ?? ''
        );
    }
}