<?php declare(strict_types=1);

namespace WikiBundle\Factory\Payload;

use Tests\TestCase;

/**
 * @see GithubPayloadFactory
 */
class GithubPayloadFactoryTest extends TestCase
{
    public function testCreate()
    {
        $textPayload = file_get_contents(__DIR__ . '/../../Resources/TestGithubPayload.json');
        $payload = $this->container->get('wolnosciowiec.wiki.factory.payload.github')
            ->create($textPayload);

        $this->assertSame('https://github.com/Wolnosciowiec/anarchi-faq-pl', $payload->getUrl());
        $this->assertSame('master', $payload->getBranch());
        $this->assertTrue($payload->isValid());
    }
}
