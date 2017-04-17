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
use WikiBundle\Domain\Context\RepositoryProcessContext;
use WikiBundle\Domain\Event\FilePostCompileEvent;
use WikiBundle\Domain\Event\FilePreCompileEvent;
use WikiBundle\Domain\Event\RepositoryPreProcessEvent;
use WikiBundle\Domain\Processor\FileProcessorInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Processor\FileNotFoundException;

/**
 * Compiles to static files
 */
class ToStaticFileProcessor implements FileProcessorInterface
{
    const EVENT_POST_COMPILE = 'postCompileFile';
    const EVENT_PRE_COMPILE  = 'preCompileFile';
    const EVENT_PRE_PROCESS_REPOSITORY = 'preProcessRepository';

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
        $processContext = new RepositoryProcessContext([
            'repositoryPath' => $repositoryPath . '/src',
            'files'          => $this->filesystem->findFiles($repositoryPath . '/src'),
            'repositoryName' => $repositoryName,
        ]);

        $this->dispatchPreRepositoryProcessEvent($processContext);

        foreach ($processContext->getFiles() as $path) {
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
        elseif (!$this->filesystem->isFile($context->getPath())) {
            throw new FileNotFoundException('File "' . basename($context->getPath()) . '" cannot be found');
        }

        $mime      = $this->filesystem->guessMimeType($context->getPath());
        $extension = $this->filesystem->guessExtension($context->getPath());
        $compiler   = $this->compilerFactory->getCompilerThatHandles($extension, $mime);

        $targetPath = $this->storageManager->findCompiledPathFor(
            $context->getRepositoryName(),
            $context->getTargetPath() ? $context->getTargetPath() : $context->getPath()
        );

        $this->logger->info('Processing "' . $context->getPath() . '"');
        $this->logger->info(' > "' . $targetPath . '"');

        if ($context->willTriggerEvents() === true) {
            $this->dispatchPreCompileEvent($context);
        }

        // allow events to modify the compilation behavior
        $fileContents = $this->filesystem->readFile($context->getPath());
        $variables    = $this->getVariables(
            array_merge(
                [
                    'selfPath' => $context->getPath(),
                    'lastUpdatedAt'  => $this->filesystem->getLastModTime($context->getPath()),
                    'repositoryName' => $context->getRepositoryName(),
                    'repositoryPath' => $context->getRepositoryPath(),
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

    protected function dispatchPreRepositoryProcessEvent(RepositoryProcessContext $context)
    {
        $event = new RepositoryPreProcessEvent($context, $this);
        $this->dispatcher->dispatch(self::EVENT_PRE_PROCESS_REPOSITORY, $event);
    }

    protected function dispatchPreCompileEvent(FileProcessContext $context)
    {
        $event = new FilePreCompileEvent($context, $this);
        $this->dispatcher->dispatch(self::EVENT_PRE_COMPILE, $event);
    }

    protected function dispatchPostCompileEvent(FileProcessContext $context)
    {
        $event = new FilePostCompileEvent($context, $this);
        $this->dispatcher->dispatch(self::EVENT_POST_COMPILE, $event);
    }

    protected function getVariables(array $context)
    {
        $context['compileDateTime'] = new \DateTime();
        return $context;
    }

    public function getFilesystem(): FileSystemAccessInterface
    {
        return $this->filesystem;
    }
}
