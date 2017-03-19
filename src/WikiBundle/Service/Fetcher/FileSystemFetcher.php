<?php declare(strict_types=1);

namespace WikiBundle\Service\Fetcher;

use WikiBundle\Domain\Service\Fetcher\FetcherInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Fetcher\RepositoryNotFoundException;

class FileSystemFetcher implements FetcherInterface
{
    /**
     * @var FileSystemAccessInterface $filesystem
     */
    private $filesystem;

    /**
     * @var StorageManagerInterface $storageManager
     */
    private $storageManager;

    public function __construct(FileSystemAccessInterface $filesystem, StorageManagerInterface $storageManager)
    {
        $this->filesystem = $filesystem;
        $this->storageManager = $storageManager;
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $groupName, string $remotePath, string $branch): string
    {
        $path = $this->storageManager->findPathFor($groupName, false);

        if ($this->filesystem->pathsAreSame($path, $remotePath)) {
            return $path;
        }

        if (!$this->filesystem->isDir($remotePath)) {
            throw new RepositoryNotFoundException('Repository does not exists in "' . $remotePath . '" path');
        }

        // clean up
        $this->filesystem->remove($path);

        // copy fresh data
        $this->filesystem->copyDirectory($remotePath, $path);

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'filesystem';
    }
}