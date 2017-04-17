<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Entity;

/**
 * Definition of query that filters and limits the collection results
 * Represents the "query" sub-section in collections.yml
 */
class Query
{
    /**
     * @var string $filter
     */
    protected $filter = '';

    /**
     * @var int $paginationMaxResults
     */
    protected $paginationMaxResults = 20;

    /**
     * eg. /events/$1 or /something-$1, /$1,something
     *
     * @var string $paginationLinkTemplate
     */
    protected $paginationLinkTemplate = '';

    /**
     * @var string $paginationSourceFile
     */
    protected $paginationSourceFile = '';

    /**
     * @var bool $shouldReverseResults
     */
    protected $reverseResults = false;

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function getPaginationMaxResults(): int
    {
        return $this->paginationMaxResults;
    }

    public function getPaginationLinkTemplate(): string
    {
        return $this->paginationLinkTemplate;
    }

    /**
     * @deprecated
     */
    public function getPaginationSourceFile(): string
    {
        return $this->paginationSourceFile;
    }

    public function shouldReverseResults(): bool
    {
        return $this->reverseResults;
    }
}
