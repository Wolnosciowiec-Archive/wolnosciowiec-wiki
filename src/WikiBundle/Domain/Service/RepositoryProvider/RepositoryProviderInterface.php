<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\RepositoryProvider;

use WikiBundle\Domain\Entity\RepositoryDefinition;

interface RepositoryProviderInterface
{
    /**
     * @param string $domainName
     * @return string
     */
    public static function normalizeDomainName(string $domainName): string;

    /**
     * Return the configuration name of a repository for given domain
     *
     * @param string $domainName
     * @return RepositoryDefinition
     */
    public function getRepositoryForDomain(string $domainName): RepositoryDefinition;

    /**
     * @return RepositoryDefinition[]
     */
    public function getAll(): array;

    /**
     * @return RepositoryDefinition[]
     */
    public function getIndexedByAddress(): array;

    /**
     * @param string $repositoryName
     * @return RepositoryDefinition
     */
    public function getOneByName(string $repositoryName): RepositoryDefinition;
}