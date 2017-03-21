<?php declare(strict_types=1);

namespace WikiBundle\Extension\Layout\Event;

use Psr\Log\LoggerInterface;
use WikiBundle\Domain\Event\FilePostCompileEvent;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

class LayoutEventListener
{
    /**
     * This gives a possibility to create a layout in plain HTML file
     *
     * @const LAYOUT_CONTENT_TAG_NAME Defines a string that will be replaced in rendered layout with a content
     */
    const LAYOUT_CONTENT_TAG_NAME = '{%wiki layout_body_content %wiki}';

    /**
     * @const LAYOUT_CONTENT_VAR_NAME Compiled content will be passed under this variable name
     */
    const LAYOUT_CONTENT_VAR_NAME = 'layout_body_content';

    /**
     * @const LAYOUT_FILE_NAME List of supported names for layout file
     */
    const LAYOUT_FILE_NAME = [
        '.layout.html.twig',
        '.layout.twig',
        '.layout.j2',
        '.layout.html',
    ];

    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(StorageManagerInterface $storageManager, LoggerInterface $logger)
    {
        $this->storageManager = $storageManager;
        $this->logger         = $logger;
    }

    private function findLayoutFile(string $path): string
    {
        foreach (self::LAYOUT_FILE_NAME as $layoutFileName) {
            if ($this->storageManager->getFilesystem()->isFile($path . '/' . $layoutFileName)) {
                return $path . '/' . $layoutFileName;
            }
        }

        return '';
    }

    /**
     * @param FilePostCompileEvent $event
     */
    public function onPostCompileFile(FilePostCompileEvent $event)
    {
        $directory = dirname($event->getContext()->getPath());
        $layoutFilePath = $this->findLayoutFile($directory);

        if (!empty($layoutFilePath)) {
            $this->logger->debug('Preparing layout for "' . $event->getContext()->getPath() . '"');

            $layoutContext = clone $event->getContext();
            $layoutContext->setPath($layoutFilePath);
            $layoutContext->setTriggerEvents(false);
            $layoutContext->setForceCompile(true);
            $layoutContext->setVariables([
                self::LAYOUT_CONTENT_VAR_NAME => $event->getContext()->getCompiledContent(),
            ]);

            $compiledLayout = $event->getProcessor()->processFile($layoutContext);

            // for plain versions replace the tag in output result
            $compiledLayout = str_replace(self::LAYOUT_CONTENT_TAG_NAME, $event->getContext()->getCompiledContent(), $compiledLayout);

            // send event result (rendered layout with content inside)
            $event->getContext()->setCompiledContent($compiledLayout);
        }
    }
}
