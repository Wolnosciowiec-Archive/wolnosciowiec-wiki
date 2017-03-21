<?php declare(strict_types=1);

namespace WikiBundle\Service\RepositoryProvider;

use JMS\Serializer\SerializerInterface;
use WikiBundle\Domain\Entity\RepositoryDefinition;
use WikiBundle\Domain\Service\RepositoryProvider\RepositoryProviderInterface;

class RepositoryProvider implements RepositoryProviderInterface
{
    /**
     * @var array $repositoryByHost Repositories indexed by host names
     */
    private $repositoryByHost = [];

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $repositories
     * @return $this
     */
    public function setRepositories(array $repositories)
    {
        $repositories = array_map(function (array $repository) {
            return $this->serializer->deserialize(json_encode($repository), RepositoryDefinition::class, 'json');
        }, $repositories);

        $this->repositoryByHost = $repositories;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function normalizeDomainName(string $domainName): string
    {
        if (substr($domainName, 0, 4) === 'www.') {
            return substr($domainName, 4);
        }

        return ltrim($domainName, '.');
    }

    /**
     * @inheritdoc
     */
    public function getRepositoryForDomain(string $domainName): RepositoryDefinition
    {
        $domainName = $this->normalizeDomainName($domainName);

        return $this->repositoryByHost[$domainName] ?? new RepositoryDefinition();
    }

    /**
     * @inheritdoc
     */
    public function getAll(): array
    {
        return $this->repositoryByHost;
    }

    /**
     * @inheritdoc
     */
    public function getIndexedByAddress(): array
    {
        $results = [];

        foreach ($this->getAll() as $repositoryDefinition) {
            $results[$repositoryDefinition->getAddress() . '@' . $repositoryDefinition->getBranch()] = $repositoryDefinition;
        }

        return $results;
    }
}
