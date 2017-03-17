<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\Fetcher;

/**
 * Fetches a remote repository
 */
interface FetcherInterface
{
    /**
     * @param string $groupName
     * @param string $url
     * @param string $branch
     *
     * @return string Absolute path to the downloaded repository
     */
    public function fetch(string $groupName, string $url, string $branch): string;

    /**
     * Returns fetcher name that should be used in eg. config
     *
     * @return string
     */
    public function getName(): string;
}
