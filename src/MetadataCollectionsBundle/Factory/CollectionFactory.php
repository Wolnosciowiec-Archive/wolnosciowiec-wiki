<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Factory;

use MetadataCollectionsBundle\Collection\FilesCollection;
use MetadataCollectionsBundle\Service\RouteGenerator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MetadataBundle\Domain\Factory\MetadataFactoryInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use MetadataCollectionsBundle\Entity\CollectionDefinition;

/**
 * @todo: Implement cache
 */
class CollectionFactory
{
    /** @var FileSystemAccessInterface $filesystem */
    protected $filesystem;

    /** @var MetadataFactoryInterface $metadataFactory */
    protected $metadataFactory;

    /** @var ExpressionLanguage $expressionLanguage */
    protected $expressionLanguage;

    /** @var RouteGenerator $routeGenerator */
    protected $routeGenerator;

    public function __construct(
        FileSystemAccessInterface $filesystem,
        MetadataFactoryInterface $metadataFactory,
        ExpressionLanguage $expressionLanguage,
        RouteGenerator $routeGenerator
    ) {
        $this->filesystem         = $filesystem;
        $this->metadataFactory    = $metadataFactory;
        $this->expressionLanguage = $expressionLanguage;
        $this->routeGenerator     = $routeGenerator;
    }

    /**
     * @param string $projectName
     * @param CollectionDefinition $definition
     *
     * @return FilesCollection
     */
    public function createByDefinition(string $projectName, CollectionDefinition $definition)
    {
        $collection = new FilesCollection($definition, $this->expressionLanguage);
        $files = $this->filesystem->findFiles($definition->getAbsolutePath());

        foreach ($files as $file) {
            $meta = $this->metadataFactory->createForFile($projectName, $file, $this->filesystem);
            $collection->addElement($meta);

            $meta->setUrl($this->routeGenerator->generateUrl($definition, $meta));
        }

        return $collection;
    }
}
