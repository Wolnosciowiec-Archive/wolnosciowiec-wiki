<?php declare(strict_types=1);

namespace WikiBundle\Domain\Browser;

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
     * Hash page content for comparison
     *
     * @param string $content
     * @return string
     */
    public function hashContent(string $content);
}
