<?php declare(strict_types=1);

namespace WikiBundle\Service\Fetcher;

use GitWrapper\GitException;
use GitWrapper\GitWrapper;
use WikiBundle\Domain\Service\Fetcher\FetcherInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Fetcher\RepositoryNotFoundException;

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

        try {
            $this->git->cloneRepository($url, $path, [
                'b' => $branch,
            ]);

        } catch (GitException $e) {
            throw new RepositoryNotFoundException('Repository "' . $url . '" does not exists');
        }


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
