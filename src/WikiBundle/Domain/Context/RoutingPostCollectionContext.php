<?php declare(strict_types=1);

namespace WikiBundle\Domain\Context;

use WikiBundle\Domain\Entity\RepositoryDefinition;
use WikiBundle\Domain\Service\Routing\RouterInterface;

class RoutingPostCollectionContext extends AbstractContext
{
    /** @var array $routingTable */
    protected $routingTable = [];

    /** @var RepositoryDefinition[] $repositories */
    protected $repositories = [];

    /** @var RouterInterface $router */
    protected $router;

    public function setRoutingTable(array $routingTable): RoutingPostCollectionContext
    {
        $this->routingTable = $routingTable;
        return $this;
    }

    public function getRoutingTable(): array
    {
        return $this->routingTable;
    }

    /**
     * @param RepositoryDefinition[] $repositories
     * @return RoutingPostCollectionContext
     */
    public function setRepositories(array $repositories): RoutingPostCollectionContext
    {
        $this->repositories = $repositories;
        return $this;
    }

    /**
     * @return RepositoryDefinition[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function setRouter(RouterInterface $router): RoutingPostCollectionContext
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter(): RouterInterface
    {
        return $this->router;
    }
}
