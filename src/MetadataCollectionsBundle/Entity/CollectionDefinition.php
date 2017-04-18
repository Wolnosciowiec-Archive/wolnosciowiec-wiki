<?php declare(strict_types=1);

namespace MetadataCollectionsBundle\Entity;

/**
 * Definition of every entry from the "collections.yml" that
 * would be placed in project's repository root directory
 */
class CollectionDefinition
{
    /**
     * @var bool $reference
     */
    protected $reference = false;

    /**
     * Project absolute path
     *
     * @var string $projectPath
     */
    protected $projectPath;

    /**
     * Where to look for a collection
     * Relative path.
     *
     * @var string $path
     */
    protected $path;

    /**
     * Type name of elements in the collection
     *
     * @var string $type
     */
    protected $type;

    /**
     * Regexp that will extract information from file name
     * it's result will be available in $alias
     *
     * @see $alias
     * @var string $expression
     */
    protected $expression = '';

    /**
     * @var string $alias
     */
    protected $alias = '';

    /**
     * @var Query $query
     */
    protected $query;

    public function getAbsolutePath()
    {
        return $this->projectPath . '/src/' . $this->getPath();
    }

    public function getProjectPath(): string
    {
        return $this->projectPath;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function isReference(): bool
    {
        return $this->reference;
    }
}
