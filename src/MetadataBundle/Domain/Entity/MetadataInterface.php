<?php declare(strict_types=1);

namespace MetadataBundle\Domain\Entity;

interface MetadataInterface
{
    /**
     * Metadata type eg. "article"
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Absolute path to file
     *
     * @return string
     */
    public function getFilePath(): string;

    /**
     * Tells if the metadata exists and is correct
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Returns order number
     *
     * @return int
     */
    public function getOrder(): int;

    /**
     * URL Rewriting (alias)
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Allow to set an URL (by the factory for example)
     *
     * @param string $url
     * @return MetadataInterface
     */
    public function setUrl($url): MetadataInterface;

    /**
     * Complete URL address that could be put into a <a> tags for example
     *
     * @return string
     */
    public function getUrl(): string;
}
