<?php declare(strict_types=1);

namespace MetadataBundle\Domain\Entity;

use MetadataBundle\Domain\Entity\EmptyMetadata;

abstract class BaseMetadata implements MetadataInterface
{
    protected $filePath = '';

    protected $relativePath = '';

    protected $order = 0;

    protected $alias = '';

    protected $url = '';

    public function setRelativePath(string $relativePath): MetadataInterface
    {
        $this->relativePath = $relativePath;
        return $this;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setFilePath(string $filePath): MetadataInterface
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function exists(): bool
    {
        return !$this instanceof EmptyMetadata;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setUrl($url): MetadataInterface
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
