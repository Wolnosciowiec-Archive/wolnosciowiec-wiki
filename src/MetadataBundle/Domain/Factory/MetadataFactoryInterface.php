<?php declare(strict_types=1);

namespace MetadataBundle\Domain\Factory;

use MetadataBundle\Domain\Entity\MetadataInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;

interface MetadataFactoryInterface
{
    /**
     * Creates a metadata object
     * Example types: Article
     *
     * If a metadata file was not found or was not correct, then an EmptyMetadata file will be returned
     *
     * @param string $projectName
     * @param string $path
     * @param FileSystemAccessInterface $filesystem
     *
     * @return MetadataInterface
     */
    public function createForFile(string $projectName, string $path, FileSystemAccessInterface $filesystem): MetadataInterface;
}
