<?php declare(strict_types=1);

namespace MetadataBundle\Event;

use Psr\Log\LoggerInterface;
use WikiBundle\Domain\Event\FilePreCompileEvent;
use MetadataBundle\Domain\Factory\MetadataFactoryInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Adds metadata information about current processed file
 * and exposes via templating manager under "metadata" variable
 *
 * Example usage in Twig:
 *     {{ metadata.getTitle() }}
 */
class MetadataEventListener
{
    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var \MetadataBundle\Domain\Factory\MetadataFactoryInterface $metadataFactory */
    private $metadataFactory;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        StorageManagerInterface $storageManager,
        LoggerInterface $logger
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->storageManager = $storageManager;
        $this->logger         = $logger;
    }

    public function onPreCompileFile(FilePreCompileEvent $event)
    {
        $this->logger->info('Loading metadata for "' . $event->getContext()->getPath() . '"');

        $metadata = $this->metadataFactory->createForFile(
            $event->getContext()->getRepositoryName(),
            $event->getContext()->getPath(),
            $event->getProcessor()->getFilesystem()
        );

        if ($metadata->getAlias()) {
            $event->getContext()->setTargetPath($metadata->getAlias());
        }

        // put metadata into variables, so it will be available during the rendering of the file
        $variables = $event->getContext()->getVariables();
        $variables['metadata'] = $metadata;
        $event->getContext()->setVariables($variables);
    }
}
