<?php declare(strict_types=1);

namespace WikiBundle\Domain\Processor;

use Symfony\Component\Finder\SplFileInfo;
use WikiBundle\Domain\Context\FileProcessContext;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;

interface FileProcessorInterface
{
    /**
     * Process whole directory
     *
     * @param string $repositoryPath
     * @param string $repositoryName
     */
    public function processRepository(string $repositoryPath, string $repositoryName);

    /**
     * Process single file
     *
     * @param FileProcessContext $context
     *
     * @return string File contents
     */
    public function processFile(FileProcessContext $context): string;

    public function getFilesystem(): FileSystemAccessInterface;
}
