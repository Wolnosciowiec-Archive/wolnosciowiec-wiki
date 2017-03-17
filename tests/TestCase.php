<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Util\ErrorHandler;use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TestCase extends WebTestCase
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * Set up a kernel
     */
    protected function setUp()
    {
        @class_alias(ErrorHandler::class, 'PHPUnit_Util_ErrorHandler');

        $client = static::createClient([
            'env' => 'test',
        ]);

        $this->container = $client->getContainer();
        parent::setUp();
    }
}
