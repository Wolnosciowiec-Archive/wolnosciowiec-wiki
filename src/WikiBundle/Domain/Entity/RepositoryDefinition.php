<?php declare(strict_types=1);

namespace WikiBundle\Domain\Entity;

/**
 * Represents an entry under "repositories" from wiki.yml
 */
class RepositoryDefinition
{
    /**
     * @var string $address
     */
    private $address = '';

    /**
     * @var string $branch
     */
    private $branch = '';

    /**
     * @var string $fetcher
     */
    private $fetcher = '';

    /**
     * @var array $domains
     */
    private $domains = [];

    /**
     * Path to default file which to load when on "/"
     *
     * @var string $indexPath
     */
    private $indexPath = '';

    /**
     * This defines if token is required to enter this page
     *
     * @var bool $public
     */
    private $public = true;

    /**
     * The index name
     *
     * @var string $name
     */
    private $name = '';

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getBranch(): string
    {
        return $this->branch;
    }

    /**
     * @return string
     */
    public function getFetcher(): string
    {
        return $this->fetcher;
    }

    /**
     * List of domains the site will be available on
     * Should not include the "www.", it would be ignored
     *
     * @return array
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /**
     * @return string
     */
    public function getIndexPath(): string
    {
        return $this->indexPath ?? '/index.md';
    }

    /**
     * @return boolean
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (empty($this->address) || empty($this->branch)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Payload
     */
    public function createPayload()
    {
        return new Payload($this->getAddress(), $this->getBranch());
    }
}
