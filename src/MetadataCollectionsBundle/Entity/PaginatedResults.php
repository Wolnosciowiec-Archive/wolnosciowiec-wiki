<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Entity;

class PaginatedResults extends \PaginatorBundle\Repository\PaginatedResults
{
    /**
     * @var string[] $paginationUrls
     */
    protected $paginationUrls = [];

    /**
     * @param string $templateUrl Relative url eg. /events/$1 where $1 is the page number
     * @param int    $maxPages
     *
     * @return string[]
     */
    public function generatePaginationUrls(string $templateUrl, int $maxPages): array
    {
        $this->paginationUrls = [];

        for ($i = 1; $i <= $maxPages; $i++) {
            $this->paginationUrls[$i] = str_replace('$1', $i, $templateUrl);
        }

        return $this->paginationUrls;
    }

    public function getPaginationUrls(): array
    {
        return $this->paginationUrls;
    }
}
