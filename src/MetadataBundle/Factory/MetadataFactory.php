<?php declare(strict_types=1);

namespace MetadataBundle\Factory;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;
use MetadataBundle\Domain\Entity\EmptyMetadata;
use MetadataBundle\Domain\Entity\MetadataInterface;
use MetadataBundle\Domain\Factory\MetadataFactoryInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Parser\MetadataParsingException;

/**
 * Creates metadata objects per file
 *
 * eg. for "src/index.md" there could be a "src/.meta.index.yml"
 * with internally defined type "article". So the "Article" object could be created from it
 */
class MetadataFactory implements MetadataFactoryInterface
{
    /**
     * Cache
     *
     * @var \MetadataBundle\Domain\Entity\MetadataInterface[] $objects
     */
    protected $objects = [];

    /**
     * @var StorageManagerInterface $storageManager
     */
    protected $storageManager;

    /**
     * Mapping between: name => class name
     *
     * eg. article => \Some\Namespace\Extensions\Article
     *
     * @var string[] $handlers
     */
    protected $handlers = [];

    /**
     * @var SerializerInterface $serializer
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer, StorageManagerInterface $storageManager)
    {
        $this->serializer = $serializer;
        $this->storageManager = $storageManager;
    }

    /**
     * @inheritdoc
     */
    public function createForFile(string $projectName, string $path, FileSystemAccessInterface $filesystem): MetadataInterface
    {
        $metadataPath = $this->findMetaDataPath($filesystem, $path);

        if ($metadataPath) {
            return $this->loadMetadata($filesystem, $metadataPath, $path, $projectName);
        }

        return new EmptyMetadata();
    }

    public function addMetadata(\MetadataBundle\Domain\Entity\MetadataInterface $metadata): MetadataFactoryInterface
    {
        $this->handlers[$metadata->getType()] = get_class($metadata);
        return $this;
    }

    /**
     * @todo: Do the validation (YAML schema validation?)
     *
     * @param FileSystemAccessInterface $filesystem
     * @param string $metadataPath
     * @param string $path
     * @param string $projectName
     *
     * @throws MetadataParsingException
     * @return MetadataInterface
     */
    protected function loadMetadata(FileSystemAccessInterface $filesystem, string $metadataPath, string $path, string $projectName): \MetadataBundle\Domain\Entity\MetadataInterface
    {
        if (isset($this->objects[$path])) {
            return $this->objects[$path];
        }

        $parsed = Yaml::parse($filesystem->readFile($metadataPath));
        $type = $parsed['meta']['type'] ?? '';

        if (!isset($this->handlers[$type])) {
            throw new MetadataParsingException('Type "' . $type . '" is not supported as a metadata, used in "' . $path . '"');
        }

        /** @var \MetadataBundle\Domain\Entity\MetadataInterface|\MetadataBundle\Domain\Entity\BaseMetadata $metadata */
        $metadata = $this->objects[$path] = $this->serializer->deserialize(json_encode($parsed['meta']['data']), $this->handlers[$type], 'json');
        $metadata->setFilePath($path);
        $metadata->setRelativePath(str_replace($this->storageManager->findPathFor($projectName) . '/src', '', $path));

        return $metadata;
    }

    protected function findMetaDataPath(FileSystemAccessInterface $filesystem, string $path): string
    {
        $metadataPath = $filesystem->getDirName($path) .
            '/.meta.' . $filesystem->getFileName($path) . '.yml';

        return is_file($metadataPath) ? $metadataPath : '';
    }
}
