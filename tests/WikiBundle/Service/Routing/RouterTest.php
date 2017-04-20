<?php declare(strict_types=1);

namespace Tests\WikiBundle\Service\Routing;

use Tests\TestCase;

// used for PhpDoc to not confuse with Symfony class that is named the same
use WikiBundle\Exception\Router\RuleParserException;
use WikiBundle\Service\Routing\Router as WikiRouter;

/**
 * @see WikiRouter
 */
class RouterTest extends TestCase
{
    /**
     * @see WikiRouter::match()
     */
    public function testMatch()
    {
        $router = $this->container->get('wolnosciowiec.wiki.services.router');
        $router->addRoute('/\/articles\/(?P<articleName>[A-Z0-9\-]+)/i', '/articles/src/$articleName.md');
        $match = $router->match('/articles/news-slave-wages-in-bangladesh-kids-employeed');

        $this->assertSame('news-slave-wages-in-bangladesh-kids-employeed', $match->get('articleName'));
        $this->assertSame('/articles/src/news-slave-wages-in-bangladesh-kids-employeed.md', $match->getUrl());
        $this->assertTrue($match->isFound());
    }

    /**
     * Tests invalid route definition
     *
     * @see WikiRouter::match()
     */
    public function testFailureMatch()
    {
        $this->expectException(RuleParserException::class);

        $router = $this->container->get('wolnosciowiec.wiki.services.router');
        $router->addRoute('This regexp is not valid/i', '/articles/src/$articleName.md');

        $router->match('/something');
    }

    /**
     * Case when route was not found
     *
     * @see WikiRouter::match()
     */
    public function testNotFoundMatch()
    {
        $router = $this->container->get('wolnosciowiec.wiki.services.router');
        $match = $router->match('/articles/news-slave-wages-in-bangladesh-kids-employeed');

        $this->assertEmpty($match->getUrl());
        $this->assertEmpty($match->get('articleName')); // this cannot throw an error as its used in templates
        $this->assertFalse($match->isFound());
    }

    /**
     * @see WikiRouter::collectRoutes()
     */
    public function testCollectRoutes()
    {
        $router = $this->container->get('wolnosciowiec.wiki.services.router');
        $router->collectRoutes();

        $this->assertArrayHasKey('/\/articles\/page\/(?P<page>[0-9]+)/i', $router->getRoutes());
    }
}
