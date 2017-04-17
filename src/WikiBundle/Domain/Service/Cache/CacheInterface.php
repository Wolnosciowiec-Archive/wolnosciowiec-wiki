<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\Cache;

interface CacheInterface
{
    const DEFAULT_TTL = 120;

    /**
     * @param string $projectName
     * @param string $keyName
     * @return mixed
     */
    public function get(string $projectName, string $keyName);

    /**
     * @param string $projectName
     * @param string $keyName
     *
     * @return bool
     */
    public function exists(string $projectName, string $keyName): bool;

    /**
     * Clear the cache for all projects/repositories
     */
    public function clearForAllProjects();

    /**
     * @param string $projectName
     * @param string $keyName
     * @param mixed  $value
     *
     * @return CacheInterface
     */
    public function set(string $projectName, string $keyName, $value): CacheInterface;

    /**
     * @param string $projectName
     * @param string $query Should support wildcards
     */
    public function delete(string $projectName, string $query);
}
