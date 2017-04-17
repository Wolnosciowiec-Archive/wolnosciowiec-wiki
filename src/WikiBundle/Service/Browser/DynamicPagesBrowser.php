<?php declare(strict_types=1);

namespace WikiBundle\Service\Browser;

use WikiBundle\Domain\Context\FileProcessContext;
use WikiBundle\Domain\Service\Browser\PagesBrowserInterface;
use WikiBundle\Domain\Service\RepositoryProvider\RepositoryProviderInterface;
use WikiBundle\Domain\Service\Routing\RouterInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Browser\PageNotFoundException;
use WikiBundle\Exception\Processor\FileNotFoundException;
use WikiBundle\Service\Processor\ToStaticFileProcessor;

/**
 * @inheritdoc
 */
class DynamicPagesBrowser implements PagesBrowserInterface
{
    /** @var ToStaticFileProcessor $processor */
    protected $processor;

    /** @var RepositoryProviderInterface $provider */
    protected $provider;

    /** @var StorageManagerInterface $storage */
    protected $storage;

    /** @var RouterInterface $router */
    protected $router;

    /** @var FileSystemAccessInterface $filesystem */
    protected $filesystem;

    public function __construct(
        ToStaticFileProcessor $processor,
        RepositoryProviderInterface $provider,
        StorageManagerInterface $storage,
        RouterInterface $router,
        FileSystemAccessInterface $filesystem
    ) {
        $this->processor = $processor;
        $this->provider  = $provider;
        $this->storage   = $storage;
        $this->router    = $router;
        $this->filesystem = $filesystem;

        $router->collectRoutes();
    }

    /**
     * @inheritdoc
     */
    public function getPageContent(string $repositoryName, string $url)
    {
        $repositoryPath = $this->getRepositoryPath($repositoryName);
        $routerMatch = $this->router->match('/' . $url);

        if ($routerMatch->isFound()) {
            $url = $routerMatch->getUrl();
        }

        $context = new FileProcessContext([
            'path' => $repositoryPath . '/src/' . $url,
            'repositoryName' => $repositoryName,
            'repositoryPath' => $repositoryPath,
            'variables' => [
                'routing' => $routerMatch,
            ]
        ]);

        try {
            return $this->processor->processFile($context);
        }
        catch (FileNotFoundException $e) {
            throw new PageNotFoundException('Page ' . $url . ' not found');
        }
    }

    /**
     * @inheritdoc
     */
    public function isAsset(string $repositoryName, string $url): bool
    {
        $repositoryPath = $this->getRepositoryPath($repositoryName);

        return substr($url, 0, 7) === 'assets/' && $this->filesystem->isFile($repositoryPath . '/src/' . $url);
    }

    public function getAssetStream(string $repositoryName, string $url): array
    {
        if (!$this->isAsset($repositoryName, $url)) {
            return [];
        }

        $repositoryPath = $this->getRepositoryPath($repositoryName);
        $filePath = $repositoryPath . '/src/' . $url;

        return [
            'stream' => $this->filesystem->readFileAsStream($filePath),
            'mime' => $this->filesystem->guessMimeType($filePath),
            'length' => $this->filesystem->getFileSize($filePath),
        ];
    }

    private function getRepositoryPath(string $repositoryName)
    {
        $repository = $this->provider->getOneByName($repositoryName);
        return $repository->getAddress();
    }

    /**
     * @inheritdoc
     */
    public function hashContent(string $content)
    {
        return hash('sha256', $content);
    }
}
