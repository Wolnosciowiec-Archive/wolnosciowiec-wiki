<?php declare(strict_types=1);

namespace WikiBundle\Service\Cache;

use Predis\Client;
use Doctrine\Common\Cache\PredisCache;
use WikiBundle\Domain\Service\Cache\CacheInterface;

class Redis implements CacheInterface
{
    /** @var Client $cache */
    protected $cache;

    public function __construct(Client $predis)
    {
        $this->cache  = $predis;
    }

    public function set(string $projectName, string $keyName, $value, $ttl = CacheInterface::DEFAULT_TTL) : CacheInterface
    {
        $this->cache->set($this->getKeyName($projectName, $keyName), $value, 'ex', $ttl);
        return $this;
    }

    public function get(string $projectName, string $keyName)
    {
        return $this->cache->get($this->getKeyName($projectName, $keyName));
    }

    public function delete(string $projectName, string $query)
    {
        $keys = $this->cache->keys($this->getKeyName($projectName, $query));
        $this->cache->del($keys);
    }

    public function exists(string $projectName, string $keyName): bool
    {
        return (bool)$this->cache->exists($this->getKeyName($projectName, $keyName));
    }

    public function clearForAllProjects()
    {
        $this->delete('', '*');
    }

    protected function getKeyName(string $projectName, string $keyName): string
    {
        if ($projectName) {
            $keyName = $projectName . '.';
        }

        return 'wolnosciowiec-wiki.' . $keyName;
    }
}
