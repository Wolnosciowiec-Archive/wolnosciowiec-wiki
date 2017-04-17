<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Service;

use MetadataCollectionsBundle\Domain\Service\CollectionDataProviderInterface;
use Symfony\Component\Yaml\Yaml;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Exception\InvalidConfigurationException;

class CollectionDataProvider implements CollectionDataProviderInterface
{
    /**
     * @var FileSystemAccessInterface $filesystem
     */
    protected $filesystem;

    public function __construct(FileSystemAccessInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function provideCollectionsConfigurationFor(string $projectPath): array
    {
        $path = $projectPath . '/../collections.yml';

        if ($this->filesystem->isFile($path)) {
            $parsed = Yaml::parse($this->filesystem->readFile($path));

            if (!isset($parsed['collections'])) {
                throw new InvalidConfigurationException('collections.yml at "' . $projectPath . '" should contain a section "collections"');
            }

            return $parsed['collections'];
        }

        return [];
    }
}
