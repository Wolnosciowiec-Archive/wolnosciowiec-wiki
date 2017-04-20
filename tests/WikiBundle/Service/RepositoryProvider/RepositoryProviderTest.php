<?php declare(strict_types=1);

namespace Tests\WikiBundle\Service\RepositoryProvider;

use Tests\TestCase;

/**
 * @see RepositoryProvider
 */
class RepositoryProviderTest extends TestCase
{
    public function provideDomainNames()
    {
        return [
            ['www.wolnosciowiec.net', 'wolnosciowiec.net'],
            ['.wolnosciowiec.net', 'wolnosciowiec.net'],
        ];
    }

    /**
     * @dataProvider provideDomainNames
     * @see RepositoryProvider::normalizeDomainName()
     *
     * @param string $input
     * @param string $expected
     */
    public function testNormalizeDomainName(string $input, string $expected)
    {
        $normalized = $this->container->get('wolnosciowiec.wiki.provider.repository')
            ->normalizeDomainName($input);

        $this->assertSame($expected, $normalized);
    }
}