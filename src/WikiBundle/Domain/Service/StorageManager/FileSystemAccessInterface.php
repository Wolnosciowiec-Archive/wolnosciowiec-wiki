<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\StorageManager;

interface FileSystemAccessInterface
{
    /**
     * Make a directory
     *
     * @param string $path
     * @param int $mode
     *
     * @return bool
     */
    public function mkdir(string $path, $mode = 0775): bool;

    /**
     * Remove a path
     *
     * @param string $path
     * @return bool
     */
    public function remove(string $path): bool;

    /**
     * Does the directory exists at specified path?
     *
     * @param string $path
     * @return bool
     */
    public function isDir(string $path): bool;

    /**
     * Checks if the file exists
     *
     * @param string $path
     * @return bool
     */
    public function isFile(string $path): bool;

    /**
     * Verifies if file is hidden
     *
     * @param string $path
     * @return bool
     */
    public function isHidden(string $path): bool;

    /**
     * Returns file contents
     *
     * @param string $path
     * @return string
     */
    public function readFile(string $path): string;

    /**
     * Write file contents
     *
     * @param string $path
     * @param string $contents
     */
    public function write(string $path, string $contents);

    /**
     * @param string $first
     * @param string $second
     *
     * @return bool
     */
    public function pathsAreSame(string $first, string $second): bool;

    /**
     * Copy a directory
     *
     * @param string $src
     * @param $destination
     */
    public function copyDirectory(string $src, $destination);

    /**
     * Copy a file
     *
     * @param string $src
     * @param string $destination
     */
    public function copy(string $src, string $destination);

    /**
     * Find files
     *
     * @param string $path
     * @return string[]
     */
    public function findFiles(string $path): array;

    /**
     * @param string $path
     * @return string
     */
    public function guessMimeType(string $path): string;

    /**
     * @param string $path
     * @return string
     */
    public function guessExtension(string $path): string;
}
