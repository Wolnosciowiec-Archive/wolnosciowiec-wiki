<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\Routing;

use WikiBundle\Domain\Entity\RouterMatch;

interface RouterInterface
{
    public function collectRoutes();
    public function addRoute(string $regexp, string $alias) : RouterInterface;
    public function match(string $url): RouterMatch;
}
