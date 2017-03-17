<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\StorageManager;

interface StorageManagerInterface
{
    /**
     * Finds a path for a repository
     *
     * @param string $repositoryName
     * @param bool   $freeUp Remove all contents and re-create the directory
     * @return string
     */
    public function findPathFor(string $repositoryName, bool $freeUp = false): string;

    public function getKnownRepositories(): array;

    public function getRepositoryName(string $url, string $branch);

    public function findCompiledPathFor(string $repositoryName, string $srcFilePath): string;
}
