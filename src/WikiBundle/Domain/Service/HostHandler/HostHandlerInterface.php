<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\HostHandler;

use WikiBundle\Domain\Entity\RepositoryDefinition;

interface HostHandlerInterface
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
}