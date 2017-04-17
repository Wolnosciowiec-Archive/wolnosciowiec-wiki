<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Event;

use MetadataCollectionsBundle\Collection\FilesCollection;
use MetadataCollectionsBundle\Repository\CollectionRepository;
use MetadataBundle\Domain\Entity\MetadataInterface;
use WikiBundle\Domain\Entity\RepositoryDefinition;
use WikiBundle\Domain\Event\RouterPostCollectEvent;

class RouterRegistrationListener
{
    /**
     * @var CollectionRepository $repository
     */
    protected $repository;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function loadCollectionDefinitions(RouterPostCollectEvent $event)
    {
        $projectNames = array_map(function (RepositoryDefinition $definition) {
            return $definition->getName();
        }, $event->getContext()->getRepositories());

        foreach ($projectNames as $projectName) {
            $this->repository->loadDefinitions($projectName);
        }
    }

    /**
     * Iterate over all Repositories->Collections->Elements to get
     * routes to all elements from all collections
     *
     * @param RouterPostCollectEvent $event
     */
    public function onRouterPostCollect(RouterPostCollectEvent $event)
    {
        $this->loadCollectionDefinitions($event);
        $this->repository->warmUpCollections();
        $customRoutes = [];

        // Repositories->Collections->Elements
        foreach ($this->repository->getAllCollections() as $collectionsPerProject) {

            /** @var FilesCollection $collection */
            foreach ($collectionsPerProject as $collection) {
                $allMetadata = $collection->getAll()->getResults();

                /** @var MetadataInterface $metadata */
                foreach ($allMetadata as $metadata) {
                    $customRoutes['/' . $this->escapeRegexp($metadata->getUrl()) . '/i'] = $metadata->getRelativePath();
                }
            }
        }

        // append routes to the routing table
        $routingTable = $event->getContext()->getRoutingTable();
        $event->getContext()->setRoutingTable(array_merge($routingTable, $customRoutes));
    }

    protected function escapeRegexp(string $string)
    {
        $string = preg_quote($string);
        $string = str_replace('/', '\/', $string);

        return $string;
    }
}
