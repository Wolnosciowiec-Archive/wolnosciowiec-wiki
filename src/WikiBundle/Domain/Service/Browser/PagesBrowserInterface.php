<?php declare(strict_types=1);

namespace WikiBundle\Domain\Service\Browser;

/**
 * Allows browsing the HTML pages
 *
 * Takes care of:
 *   - Finding and reading those docs
 *   - Validation if page exists
 */
interface PagesBrowserInterface
{
    /**
     * @param string $group Name of the group (directory)
     * @param string $url Relative path to the file
     *
     * @return string
     */
    public function getPageContent(string $group, string $url);

    /**
     * Tells if the resource should be streamed without processing
     *
     * @param string $repositoryName
     * @param string $url
     *
     * @return bool
     */
    public function isAsset(string $repositoryName, string $url): bool;

    /**
     * @param string $repositoryName
     * @param string $url
     *
     * @return array
     */
    public function getAssetStream(string $repositoryName, string $url): array;

    /**
     * Hash page content for comparison
     *
     * @param string $content
     * @return string
     */
    public function hashContent(string $content);
}
