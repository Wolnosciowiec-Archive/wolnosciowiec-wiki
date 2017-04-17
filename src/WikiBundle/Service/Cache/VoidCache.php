<?php declare(strict_types=1);

namespace WikiBundle\Service\Cache;

use WikiBundle\Domain\Service\Cache\CacheInterface;

class VoidCache implements CacheInterface
{
    public function set(string $projectName, string $keyName, $value, $ttl = CacheInterface::DEFAULT_TTL) : CacheInterface
    {
        return $this;
    }

    public function get(string $projectName, string $keyName)
    {
        return null;
    }

    public function delete(string $projectName, string $query)
    {
    }

    public function exists(string $projectName, string $keyName): bool
    {
        return false;
    }

    public function clearForAllProjects()
    {
    }
}
