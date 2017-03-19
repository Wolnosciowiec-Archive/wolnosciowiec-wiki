<?php declare(strict_types=1);

namespace Tests\WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('message":"Hello"', $client->getResponse()->getContent());
    }

    public function testAccessByDomain()
    {
        $client = static::createClient();
        $client->request('GET', 'http://faq.dev/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Anarchizm', $client->getResponse()->getContent());
    }
}
