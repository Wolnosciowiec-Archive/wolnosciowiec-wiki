<?php declare(strict_types=1);

namespace WikiBundle\Service\Fetcher;

use WikiBundle\Domain\Service\Fetcher\FetcherInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Fetches a repository allowed in config
 */
class FetcherService
{
    /** @var FetcherInterface[] $fetcher */
    private $fetchers = [];

    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    public function __construct(StorageManagerInterface $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * Push a fetcher to the service
     * -----------------------------
     *   Used by the Dependency Injection container
     *
     * @param FetcherInterface $fetcher
     * @return FetcherService
     */
    public function addFetcher(FetcherInterface $fetcher): FetcherService
    {
        if ($fetcher instanceof $this) {
            throw new \InvalidArgumentException('Cannot add self as fetcher');
        }

        $this->fetchers[$fetcher->getName()] = $fetcher;
        return $this;
    }

    /**
     * Clone a repository into a temporary directory and return
     * the directory path
     *
     * @param string $fetcherName
     * @param string $url
     * @param string $branch
     *
     * @return string
     */
    public function cloneRepository(string $fetcherName, string $url, string $branch): string
    {
        if (!isset($this->fetchers[$fetcherName])) {
            throw new \InvalidArgumentException('Unrecognized fetcher "' . $fetcherName . '" requested');
        }

        $repositoryName = $this->storageManager->getRepositoryName($url, $branch);

        if (!$repositoryName) {
            throw new \InvalidArgumentException('Specified url and branch are not allowed, forbidden by the configuration');
        }

        /** @var FetcherInterface $fetcher */
        $fetcher = $this->fetchers[$fetcherName];
        return $fetcher->fetch($repositoryName, $url, $branch);
    }
}
