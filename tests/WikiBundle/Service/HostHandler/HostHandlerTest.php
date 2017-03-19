<?php declare(strict_types=1);

namespace WikiBundle\Service\HostHandler;

use Tests\TestCase;

/**
 * @see HostHandler
 */
class HostHandlerTest extends TestCase
{
    /**
     * @see HostHandler::getRepositoryForDomain()
     */
    public function testValidGetRepositoryForDomain()
    {
        $repository = $this->container->get('wolnosciowiec.wiki.handler.host')
            ->getRepositoryForDomain('faq.dev');

        $this->assertSame('anarchifaq', $repository->getName());
        $this->assertSame(['faq.wolnosciowiec.net', 'faq.dev'], $repository->getDomains());
        $this->assertSame('master', $repository->getBranch());
        $this->assertTrue($repository->isPublic());
        $this->assertTrue($repository->isValid());
    }

    /**
     * @see HostHandler::getRepositoryForDomain()
     */
    public function testMissingGetRepositoryForDomain()
    {
        $repository = $this->container->get('wolnosciowiec.wiki.handler.host')
            ->getRepositoryForDomain('non-existing-repository');

        $this->assertFalse($repository->isValid());
        $this->assertEmpty($repository->getName());
        $this->assertEmpty($repository->getBranch());
    }

    /**
     * @return array
     */
    public function provideNames()
    {
        return [
            [
                'www.wolnosciowiec.net',
                'wolnosciowiec.net',
            ],

            [
                'wolnywroclaw.pl',
                'wolnywroclaw.pl',
            ],

            [
                '.libcom.org',
                'libcom.org',
            ]
        ];
    }

    /**
     * @dataProvider provideNames()
     *
     * @param string $input
     * @param string $expected
     */
    public function testNormalization(string $input, string $expected)
    {
        $hostHandler = $this->container->get('wolnosciowiec.wiki.handler.host');
        $this->assertSame($expected, $hostHandler::normalizeDomainName($input));
    }
}
