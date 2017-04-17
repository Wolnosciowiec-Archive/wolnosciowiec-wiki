<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Domain\Service;

interface CollectionDataProviderInterface
{
    /**
     * @param string $projectPath
     * @return array
     */
    public function provideCollectionsConfigurationFor(string $projectPath): array;
}