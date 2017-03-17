<?php declare(strict_types=1);

namespace WikiBundle\Factory\Payload;

use Tests\TestCase;

/**
 * @see GithubPayloadFactory
 */
class PayloadFactoryTest extends TestCase
{
    public function providePayloads()
    {
        return [
            'github' => [
                'textPayload' => file_get_contents(__DIR__ . '/../../Resources/TestGithubPayload.json'),
            ],
        ];
    }

    /**
     * @dataProvider providePayloads()
     * @param string $textPayload
     */
    public function testCreate(string $textPayload)
    {
        $payload = $this->container->get('wolnosciowiec.wiki.factory.payload')
            ->create($textPayload);

        $this->assertSame('https://github.com/Wolnosciowiec/anarchi-faq-pl', $payload->getUrl());
        $this->assertSame('master', $payload->getBranch());
        $this->assertTrue($payload->isValid());
    }
}
