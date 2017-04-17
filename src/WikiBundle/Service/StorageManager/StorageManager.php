<?php declare(strict_types=1);

namespace WikiBundle\Service\StorageManager;

use Symfony\Component\Filesystem\Filesystem;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

class StorageManager implements StorageManagerInterface
{
    /** @var string $storagePath */
    private $storagePath = '';

    /** @var string $compiledFilesPath */
    private $compiledFilesPath = '';

    /** @var FileSystemAccessInterface $filesystem */
    private $filesystem;

    /** @var array $knownRepositories */
    private $knownRepositories = [];

    public function __construct(
        string $storagePath,
        string $compiledFilesPath,
        FileSystemAccessInterface $filesystem
    ) {
        $this->storagePath = $storagePath;
        $this->compiledFilesPath = $compiledFilesPath;
        $this->filesystem = $filesystem;
    }

    private function escape(string $path): string
    {
        $path = str_replace('..', '', $path);
        $path = str_replace('/', '', $path);
        $path = str_replace("\x0", '', $path);

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function findPathFor(string $repositoryName, bool $freeUp = false): string
    {
        $path = $this->storagePath . '/' . $this->escape($repositoryName);

        if ($freeUp) {
            if ($this->filesystem->isDir($path)) {
                $this->filesystem->remove($path);
            }

            $this->filesystem->mkdir($path);
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function findCompiledPathFor(string $repositoryName, string $srcFilePath): string
    {
        $filePath = str_replace($this->normalizePath($this->storagePath), '', $this->normalizePath($srcFilePath));
        $dir = $this->compiledFilesPath . '/' . dirname($filePath);

        if (!$this->filesystem->isDir($dir)) {
            $this->filesystem->mkdir($dir, 0775);
        }

        return $this->compiledFilesPath . '/' . $filePath;
    }

    /**
     * @see http://php.net/realpath
     * @author Sven Arduwie
     *
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * @inheritdoc
     */
    public function getRepositoryName(string $url, string $branch)
    {
        return array_search($url . '@' . $branch, $this->knownRepositories);
    }

    /**
     * @param array $knownRepositories
     * @return StorageManagerInterface
     */
    public function setKnownRepositories(array $knownRepositories): StorageManagerInterface
    {
        $this->knownRepositories = $knownRepositories;
        return $this;
    }

    /**
     * @return array
     */
    public function getKnownRepositories(): array
    {
        return $this->knownRepositories;
    }

    public function getFilesystem(): FileSystemAccessInterface
    {
        return $this->filesystem;
    }
}
