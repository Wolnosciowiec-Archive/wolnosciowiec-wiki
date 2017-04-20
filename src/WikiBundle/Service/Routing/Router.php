<?php declare(strict_types=1);

namespace WikiBundle\Service\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;
use WikiBundle\Domain\Context\RoutingPostCollectionContext;
use WikiBundle\Domain\Entity\RepositoryDefinition;
use WikiBundle\Domain\Entity\RouterMatch;
use WikiBundle\Domain\Event\RouterPostCollectEvent;
use WikiBundle\Domain\Service\Cache\CacheInterface;
use WikiBundle\Domain\Service\RepositoryProvider\RepositoryProviderInterface;
use WikiBundle\Domain\Service\Routing\RouterInterface;
use WikiBundle\Domain\Service\StorageManager\FileSystemAccessInterface;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;
use WikiBundle\Exception\Router\RuleParserException;

class Router implements RouterInterface
{
    /** @var array $routes */
    private $routes = [];

    /** @var CacheInterface $cache */
    protected $cache;

    /** @var FileSystemAccessInterface $filesystem */
    protected $filesystem;

    /** @var RepositoryProviderInterface $repositories */
    protected $repositories;

    /** @var StorageManagerInterface $storageManager */
    protected $storageManager;

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    public function __construct(
        CacheInterface $cache,
        FileSystemAccessInterface $filesystem,
        RepositoryProviderInterface $repositories,
        StorageManagerInterface $storageManager,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->cache = $cache;
        $this->filesystem   = $filesystem;
        $this->repositories = $repositories;
        $this->storageManager = $storageManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function collectRoutes()
    {
        // @codeCoverageIgnoreStart
        if ($this->cache->exists('', 'globalRoutes')) {
            $this->routes = json_decode($this->cache->get('', 'globalRoutes'), true);
            return;
        }
        // @codeCoverageIgnoreEnd

        foreach ($this->repositories->getAll() as $repositoryDefinition) {
            $this->addRepositoryRouting($repositoryDefinition);
        }

        $this->triggerPostCollectEvent();
        $this->cache->set('', 'globalRoutes', json_encode($this->routes));
    }

    /**
     * @inheritdoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    protected function triggerPostCollectEvent()
    {
        $context = new RoutingPostCollectionContext([
            'routingTable' => $this->routes,
            'repositories' => $this->repositories->getAll(),
            'router'       => $this,
        ]);
        $this->eventDispatcher->dispatch(self::EVENT_POST_COLLECT, new RouterPostCollectEvent($context));

        $this->routes = $context->getRoutingTable();
    }

    protected function addRepositoryRouting(RepositoryDefinition $definition)
    {
        $path = $definition->getAddress() . '/routing.yml';

        if ($this->filesystem->isFile($path)) {
            $routingTable = Yaml::parse($this->filesystem->readFile($path))['routing'];
            $this->routes = array_merge($this->routes, $routingTable);
        }
    }

    public function addRoute(string $regexp, string $alias) : RouterInterface
    {
        $this->routes[$regexp] = $alias;
        return $this;
    }

    public function match(string $url): RouterMatch
    {
        foreach ($this->routes as $regexp => $alias) {
            try {
                if (preg_match($regexp, $url, $matches)) {
                    foreach ($matches as $match => $value) {
                        $alias = str_replace('$' . $match, $value, $alias);
                    }

                    return new RouterMatch($alias, $matches);
                }
            } catch (\Throwable $e) {
                throw new RuleParserException('Rule "' . $regexp . '" cannot be parsed, reason: ' . $e->getMessage());
            }
        }

        return new RouterMatch('', []);
    }
}
