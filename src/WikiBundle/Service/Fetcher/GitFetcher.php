<?php declare(strict_types=1);

namespace WikiBundle\Service\Fetcher;

use GitWrapper\GitWrapper;
use WikiBundle\Domain\Service\Fetcher\FetcherInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Fetches repositories from git
 */
class GitFetcher implements FetcherInterface
{
    /** @var GitWrapper $git */
    private $git;

    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    public function __construct(StorageManagerInterface $storageManager)
    {
        $this->storageManager = $storageManager;
        $this->git = new GitWrapper();
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $groupName, string $url, string $branch): string
    {
        $path = $this->storageManager->findPathFor($groupName, true);

        $this->git->cloneRepository($url, $path, [
            'b' => $branch,
        ]);

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'git';
    }
}
