<?php declare(strict_types=1);

namespace WikiBundle\Domain\Entity;

/**
 * Github or other service response converted to the internal "payload"
 */
class Payload
{
    /**
     * @var string $url
     */
    private $url;

    /**
     * @var string $branch
     */
    private $branch;

    public function __construct(string $url = '', string $branch = '')
    {
        $this->url = $url;
        $this->branch = $branch;
    }

    public function isValid()
    {
        return !empty($this->getUrl())
            && !empty($this->getBranch());
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }
}
