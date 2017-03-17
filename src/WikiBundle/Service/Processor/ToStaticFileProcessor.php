<?php declare(strict_types=1);

namespace WikiBundle\Service\Processor;

use ContentCompilerBundle\Factory\CompilerFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Compiles to static files
 */
class ToStaticFileProcessor
{
    /**
     * @var Finder $finder
     */
    private $finder;

    /**
     * @var CompilerFactory $compilerFactory
     */
    private $compilerFactory;

    /**
     * @var StorageManagerInterface $storageManager
     */
    private $storageManager;

    public function __construct(
        CompilerFactory $compilerFactory,
        StorageManagerInterface $storageManager
    ) {
        $this->finder = new Finder();
        $this->compilerFactory = $compilerFactory;
        $this->storageManager = $storageManager;
    }

    /**
     * Process whole directory
     *
     * @param string $repositoryPath
     * @param string $repositoryName
     */
    public function processDirectory(string $repositoryPath, string $repositoryName)
    {
        $files = $this->finder->files()->in($repositoryPath);

        foreach ($files as $file) {
            $this->processFile($file, $repositoryName);
        }
    }

    /**
     * Process single file
     *
     * @param SplFileInfo $file
     * @param string $repositoryName
     *
     * @return string Target path
     */
    public function processFile(SplFileInfo $file, string $repositoryName): string
    {
        if ($file->isDir()) {
            return '';
        }

        $mime      = MimeTypeGuesser::getInstance()->guess($file->getRealPath());
        $extension = $file->getExtension();
        $compiler   = $this->compilerFactory->getCompilerThatHandles($extension, $mime);
        $targetPath = $this->storageManager->findCompiledPathFor($repositoryName, $file->getRealPath());

        file_put_contents(
            $targetPath,
            $compiler->compileFromString(file_get_contents($file->getRealPath()))
        );

        return $targetPath;
    }
}
