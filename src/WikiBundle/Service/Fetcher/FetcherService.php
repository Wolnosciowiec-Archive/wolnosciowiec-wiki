<?php declare(strict_types=1);

namespace WikiBundle\Service\Fetcher;

use Psr\Log\LoggerInterface;
use WikiBundle\Domain\Service\Fetcher\FetcherInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Fetcher\RepositoryNotFoundException;

/**
 * Fetches a repository allowed in config
 */
class FetcherService
{
    /** @var FetcherInterface[] $fetcher */
    private $fetchers = [];

    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(
        StorageManagerInterface $storageManager,
        LoggerInterface $logger
    ) {
        $this->storageManager = $storageManager;
        $this->logger         = $logger;
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
     * @throws \InvalidArgumentException
     * @throws RepositoryNotFoundException
     *
     * @return string
     */
    public function cloneRepository(string $fetcherName, string $url, string $branch): string
    {
        if (!isset($this->fetchers[$fetcherName])) {
            $this->logger->error('Unrecognized fetcher "' . $fetcherName . '" requested');
            throw new \InvalidArgumentException('Unrecognized fetcher "' . $fetcherName . '" requested');
        }

        $repositoryName = $this->storageManager->getRepositoryName($url, $branch);

        if (!$repositoryName) {
            $this->logger->error('Repository "' . $repositoryName . '" forbidden by config');
            throw new RepositoryNotFoundException('Specified url and branch are not allowed, forbidden by the configuration');
        }

        /** @var FetcherInterface $fetcher */
        $fetcher = $this->fetchers[$fetcherName];

        $this->logger->info('Using "' . $fetcherName . '" fetcher');
        return $fetcher->fetch($repositoryName, $url, $branch);
    }
}
