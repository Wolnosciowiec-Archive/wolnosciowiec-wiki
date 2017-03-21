<?php declare(strict_types=1);

namespace WikiBundle\Service\Processor;

use ContentCompilerBundle\Factory\CompilerFactory;
use ContentCompilerBundle\Service\ContentCompiler\ContentCompilerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use WikiBundle\Domain\Context\FileProcessContext;
use WikiBundle\Domain\Event\FilePostCompileEvent;
use WikiBundle\Domain\Processor\FileProcessorInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Compiles to static files
 */
class ToStaticFileProcessor implements FileProcessorInterface
{
    const EVENT_POST_COMPILE = 'postCompileFile';

    /** @var CompilerFactory $compilerFactory */
    private $compilerFactory;

    /** @var StorageManagerInterface $storageManager */
    private $storageManager;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var FileSystemAccessInterface $filesystem */
    private $filesystem;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /** @var string $cacheDir */
    private $cacheDir;

    public function __construct(
        CompilerFactory $compilerFactory,
        StorageManagerInterface $storageManager,
        LoggerInterface $logger,
        FileSystemAccessInterface $filesystem,
        EventDispatcherInterface $dispatcher,
        string $cacheDir
    ) {
        $this->filesystem         = $filesystem;
        $this->compilerFactory    = $compilerFactory;
        $this->storageManager     = $storageManager;
        $this->logger             = $logger;
        $this->dispatcher         = $dispatcher;
        $this->cacheDir           = $cacheDir;
    }

    /**
     * @inheritdoc
     */
    public function processRepository(string $repositoryPath, string $repositoryName)
    {
        $repositoryPath = $repositoryPath . '/src';
        $files = $this->filesystem->findFiles($repositoryPath);

        foreach ($files as $path) {
            $this->processFile(
                new FileProcessContext([
                    'path' => $path,
                    'repositoryName' => $repositoryName,
                    'repositoryPath' => $repositoryPath,
                ])
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function processFile(FileProcessContext $context): string
    {
        if ($this->filesystem->isDir($context->getPath())) {
            $this->logger->debug('Skipping "' . $context->getPath() . '" (reason: directory)');
            return '';
        }
        elseif ($this->filesystem->isHidden($context->getPath()) && !$context->isCompilationForced()) {
            $this->logger->debug('Skipping "' . $context->getPath() . '" (reason: hidden file)');
            return '';
        }

        $this->logger->info('Processing "' . $context->getPath() . '"');

        $mime      = $this->filesystem->guessMimeType($context->getPath());
        $extension = $this->filesystem->guessExtension($context->getPath());
        $compiler   = $this->compilerFactory->getCompilerThatHandles($extension, $mime);
        $targetPath = $this->storageManager->findCompiledPathFor($context->getRepositoryName(), $context->getPath());

        // allow events to modify the compilation behavior
        $fileContents = $this->filesystem->readFile($context->getPath());
        $variables    = $this->getVariables(
            array_merge(
                [
                    'self_path' => $context->getPath(),
                    'repository_name' => $context->getRepositoryName(),
                    'repository_path' => $context->getRepositoryPath(),
                ],
                $context->getVariables()
            )
        );

        $context->setCompiledContent($compiler->compileFromString($fileContents, false, array_merge([
            ContentCompilerInterface::ESCAPE_LINKS => false,
            ContentCompilerInterface::VARIABLES    => $variables,
            ContentCompilerInterface::INCLUDE_PATH => $context->getRepositoryPath(),
            ContentCompilerInterface::CACHE_DIR    => $this->cacheDir,
        ], $context->getCompilationOptions())));

        if ($context->willTriggerEvents() === true) {
            $this->dispatchPostCompileEvent($context);
        }

        $this->filesystem->write(
            $targetPath,
            $context->getCompiledContent()
        );

        return $context->getCompiledContent();
    }

    protected function dispatchPostCompileEvent(FileProcessContext $context)
    {
        $event = new FilePostCompileEvent($context, $this);
        $this->dispatcher->dispatch(self::EVENT_POST_COMPILE, $event);
    }

    protected function getVariables(array $context)
    {
        $context['compile_date_time'] = new \DateTime();
        return $context;
    }
}
