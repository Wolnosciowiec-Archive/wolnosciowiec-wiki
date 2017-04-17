<?php declare(strict_types=1);

namespace WikiBundle\Domain\Entity;

class RouterMatch
{
    /** @var string $url */
    protected $url;

    /** @var array $variables */
    protected $variables = [];

    public function __construct(string $url, array $variables)
    {
        $this->url       = $url;
        $this->variables = $variables;
    }

    /**
     * List of variables collected from the url by regexp
     * eg. "page" => 2 when "/?P<page>([0-9]+)/i"
     *
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Allows to get matches variables from the url
     *
     * @param string $variableName
     * @return string
     */
    public function get(string $variableName): string
    {
        return $this->variables[$variableName] ?? '';
    }

    /**
     * Final internal url constructed from rewrited url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Tells if the route as found, or is it a 404
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return strlen($this->url) > 0;
    }
}
