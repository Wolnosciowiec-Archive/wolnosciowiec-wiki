<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Event;

use MetadataCollectionsBundle\Repository\SharedPublic\CollectionRepository as PublicCollectionRepository;
use MetadataCollectionsBundle\Service\CollectionDataProvider;
use Psr\Log\LoggerInterface;
use WikiBundle\Domain\Event\FilePreCompileEvent;
use MetadataCollectionsBundle\Repository\CollectionRepository;

/**
 * Push collections manager to the template, so it could be used to extract
 * a list of articles/menus/texts and iterate over them
 */
class CollectionsToTemplateListener
{
    protected $collectionsRepository;

    protected $dataProvider;

    public function __construct(CollectionRepository $collectionsManager, CollectionDataProvider $dataProvider)
    {
        $this->collectionsRepository = $collectionsManager;
        $this->dataProvider = $dataProvider;
    }

    public function onPreCompileFile(FilePreCompileEvent $event)
    {
        $page = $event->getContext()->getPage() ?? 1;
        $repositoryName = $event->getContext()->getRepositoryName();

        // warm up
        $this->collectionsRepository->loadDefinitions($repositoryName);

        if (isset($event->getContext()->getVariables()['routing'])) {
            $page = $event->getContext()->getVariables()['routing']->get('page');
        }

        $event->getContext()->addVariables([
            'collections' => [
                'repository'  => new PublicCollectionRepository($repositoryName, $this->collectionsRepository),
                'definitions' => $this->collectionsRepository->getDefinitions($repositoryName),
            ],

            'currentPage' => $page,
        ]);
    }
}
