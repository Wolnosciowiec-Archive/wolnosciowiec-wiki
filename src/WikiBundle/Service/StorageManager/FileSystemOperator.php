<?php declare(strict_types=1);

namespace WikiBundle\Service\StorageManager;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;

/**
 * Implements the direct access to the local filesystem
 */
class FileSystemOperator implements FileSystemAccessInterface
{
    /**
     * @var Filesystem $fs
     */
    private $fs;

    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * @inheritdoc
     */
    public function mkdir(string $path, $mode = 0775): bool
    {
        $this->fs->mkdir($path, $mode);
        return is_dir($path);
    }

    /**
     * @inheritdoc
     */
    public function remove(string $path): bool
    {
        $this->fs->remove($path);
        return !is_dir($path);
    }

    /**
     * @inheritdoc
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @inheritdoc
     */
    public function readFile(string $path): string
    {
        return file_get_contents($path);
    }

    /**
     * @inheritdoc
     */
    public function write(string $path, string $contents)
    {
        file_put_contents($path, $contents);
    }

    /**
     * @inheritdoc
     */
    public function pathsAreSame(string $first, string $second): bool
    {
        return realpath($first) === realpath($second);
    }

    /**
     * @inheritdoc
     */
    public function copyDirectory(string $src, $destination)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var \SplFileInfo[] $iterator */
        foreach ($iterator as $item) {

            if ($item->isDir()) {
                $this->mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                continue;
            }

            $this->copy($item->getRealPath(), $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        }
    }

    /**
     * @inheritdoc
     */
    public function copy(string $src, string $destination)
    {
        $this->fs->copy($src, $destination, true);
    }
}
