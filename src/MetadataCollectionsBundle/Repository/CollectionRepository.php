<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Repository;

use MetadataCollectionsBundle\Collection\FilesCollection;
use MetadataCollectionsBundle\Domain\Service\CollectionDataProviderInterface;
use MetadataCollectionsBundle\Entity\CollectionDefinition;
use MetadataCollectionsBundle\Factory\CollectionFactory;
use MetadataCollectionsBundle\Factory\DefinitionFactory;
use WikiBundle\Domain\Service\StorageManager\StorageManagerInterface;

/**
 * Finds collections for all projects
 */
class CollectionRepository
{
    /**
     * @var DefinitionFactory $definitionFactory
     */
    protected $definitionFactory;

    /**
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CollectionDefinition[][] $definitions
     */
    protected $definitions = [];

    /**
     * @var CollectionDataProviderInterface $dataProvider
     */
    protected $dataProvider;

    /**
     * @var StorageManagerInterface $storageManager
     */
    protected $storageManager;

    /**
     * Cached collections list
     *
     * @var array[] $collections
     */
    protected $collections = [];

    public function __construct(
        DefinitionFactory $definitionFactory,
        CollectionFactory $collectionFactory,
        CollectionDataProviderInterface $dataProvider,
        StorageManagerInterface $storageManager
    ) {
        $this->definitionFactory = $definitionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->dataProvider = $dataProvider;
        $this->storageManager = $storageManager;
    }

    /**
     * Load definitions of collections for a given project
     *
     * @param string $projectName Project name from wiki.yml eg. 'anarchifaq'
     * @return integer
     */
    public function loadDefinitions(string $projectName): int
    {
        $projectPath = $this->storageManager->findPathFor($projectName);
        $collections = $this->dataProvider->provideCollectionsConfigurationFor(
            $projectPath . '/src'
        );

        foreach ($collections as $collectionName => $collectionData) {
            $collectionData['project_path'] = $projectPath;

            $definition = $this->definitionFactory->createDefinitionFrom($collectionData);
            $this->definitions[$projectName][$collectionName] = $definition;
        }

        // if nothing was loaded, just create the structure
        if (!isset($this->definitions[$projectName])) {
            $this->definitions[$projectName] = [];
        }

        return isset($this->definitions[$projectName]) ? count($this->definitions[$projectName]) : 0;
    }

    /**
     * @param string $projectName
     * @param string $collectionName
     *
     * @return FilesCollection
     */
    public function getCollection(string $projectName, string $collectionName): FilesCollection
    {
        if (!isset($this->collections[$projectName][$collectionName])) {
            $this->collections[$projectName][$collectionName] =
                $this->collectionFactory->createByDefinition($projectName, $this->definitions[$projectName][$collectionName]);
        }

        return $this->collections[$projectName][$collectionName];
    }

    /**
     * @return CollectionDefinition[]
     */
    public function getDefinitions(string $projectName): array
    {
        return $this->definitions[$projectName] ?? [];
    }

    /**
     * Warm up data for usage with eg. getAllCollections()
     */
    public function warmUpCollections()
    {
        foreach ($this->definitions as $projectName => $definitions) {
            foreach ($definitions as $definitionName => $definition) {
                $this->getCollection($projectName, $definitionName);
            }
        }
    }

    /**
     * @return array
     */
    public function getAllCollections(): array
    {
        return $this->collections;
    }
}
