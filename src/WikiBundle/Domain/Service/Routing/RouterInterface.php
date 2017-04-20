<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\Routing;

use WikiBundle\Domain\Entity\RouterMatch;

interface RouterInterface
{
    const EVENT_POST_COLLECT = 'routerPostCollect';

    /**
     * Collect routes from repositories
     *
     * Triggers an event: self::EVENT_POST_COLLECT
     *
     * @see self::EVENT_POST_COLLECT
     */
    public function collectRoutes();

    /**
     * Add a route to the collection
     *
     * @param string $regexp
     * @param string $alias
     *
     * @return RouterInterface
     */
    public function addRoute(string $regexp, string $alias) : RouterInterface;

    /**
     * Take an URL and try to find a match for any defined rule
     *
     * @param string $url
     * @return RouterMatch
     */
    public function match(string $url): RouterMatch;

    /**
     * Lists all routes
     *
     * @return array
     */
    public function getRoutes(): array;
}
