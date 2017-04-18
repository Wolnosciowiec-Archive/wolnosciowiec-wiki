<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Collection;

use MetadataCollectionsBundle\Entity\CollectionDefinition;
use MetadataCollectionsBundle\Entity\PaginatedResults;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MetadataBundle\Domain\Entity\MetadataInterface;

/**
 * A list of files eg. articles or texts, or menu entries
 * Only a meta data, without the content
 */
class FilesCollection
{
    /**
     * @var MetadataInterface[] $elements
     */
    protected $elements;

    /**
     * @var CollectionDefinition $definition
     */
    protected $definition;

    /**
     * @var ExpressionLanguage $expressionLanguage
     */
    protected $expressionLanguage;

    public function __construct(CollectionDefinition $definition, ExpressionLanguage $expressionLanguage)
    {
        $this->definition = $definition;
        $this->expressionLanguage = $expressionLanguage;
    }

    public function addElement(MetadataInterface $metadata): FilesCollection
    {
        // skip elements of other type than expected
        if ($this->definition->getType() !== $metadata->getType()) {
            return $this;
        }

        $this->elements[$metadata->getFilePath()] = $metadata;
        return $this;
    }

    /**
     * @param bool $reversed
     * @return PaginatedResults<MetadataInterface>
     */
    public function getAll(bool $reversed = false): PaginatedResults
    {
        $results = $this->orderResults(array_values($this->elements), $reversed);

        return new PaginatedResults($results, 1, 1);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $reversed
     *
     * @return PaginatedResults<MetadataInterface>
     */
    public function getAllPaginated($page = 1, $limit = 20, bool $reversed = false): PaginatedResults
    {
        $all        = $this->getAll()->getResults();
        $offset     = (($page - 1) * $limit);

        $totalPages = PaginatedResults::getPagesCount(count($all), $limit);

        if ($limit === 0) {
            $offset = 0;
            $limit = count($all);
            $totalPages = 1;
        }

        $results = $this->orderResults(array_slice($all, $offset, $limit), $reversed);
        return new PaginatedResults($results, $totalPages, $page);
    }

    /**
     * Filter elements by expression
     *
     * @param string $expression
     * @param int    $page
     * @param int    $limit
     * @param bool   $reversed
     *
     * @return PaginatedResults
     */
    public function getMultipleBy(string $expression, $page = 1, $limit = 20, bool $reversed = false): PaginatedResults
    {
        $offset   = (($page - 1) * $limit);
        $filtered = [];

        foreach ($this->elements as $element) {
            if ($this->expressionLanguage->evaluate($expression, ['element' => $element])) {
                $filtered[] = $element;
            }
        }

        $totalPages = PaginatedResults::getPagesCount(count($filtered), $limit);

        if ($limit === 0) {
            $offset = 0;
            $limit = count($filtered);
            $totalPages = 1;
        }

        $results = $this->orderResults(array_slice($filtered, $offset, $limit), $reversed);
        return new PaginatedResults($results, $totalPages, $page);
    }

    /**
     * Get results basing on collection definition
     *
     * @param int $page
     * @param null|int $limit
     *
     * @return PaginatedResults
     */
    public function getByDefinition($page = 1, $limit = null)
    {
        // services exposed to view cannot have strict typing
        $page   = (int) $page;
        $limit  = (int) ($limit ?? $this->definition->getQuery()->getPaginationMaxResults());

        $filter = $this->definition->getQuery()->getFilter();
        $reverseOrder = $this->definition->getQuery()->shouldReverseResults();

        if ($filter) {
            $results = $this->getMultipleBy($filter, $page, $limit, $reverseOrder);

        } else {
            $results = $this->getAllPaginated($page, $limit, $reverseOrder);
        }

        $results->generatePaginationUrls($this->definition->getQuery()->getPaginationLinkTemplate(), $results->getMaxPages());
        return $results;
    }

    /**
     * Returns a Metadata for given absolute path
     *
     * @param string $filePath
     * @return null|MetadataInterface
     */
    public function getForFilePath(string $filePath)
    {
        return $this->elements[$filePath] ?? null;
    }

    /**
     * @param MetadataInterface[] $results
     * @param bool                $reversed
     *
     * @return MetadataInterface[]
     */
    private function orderResults(array $results, bool $reversed = false)
    {
        if ($reversed === true) {
            usort($results, function (MetadataInterface $a, MetadataInterface $b) {
                return $b->getOrder() <=> $a->getOrder();
            });

            return $results;
        }

        usort($results, function (MetadataInterface $a, MetadataInterface $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        return $results;
    }
}
