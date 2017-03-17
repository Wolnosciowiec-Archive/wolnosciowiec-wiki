<?php declare(strict_types=1);

namespace WikiBundle\Service\Browser;

use WikiBundle\Domain\Service\Browser\PagesBrowserInterface;
use WikiBundle\Exception\Browser\PageNotFoundException;

/**
 * @inheritdoc
 */
class StaticPagesBrowser implements PagesBrowserInterface
{
    /** @var string $directory */
    private $directory;

    public function __construct(string $groupsDirectory)
    {
        $this->directory = realpath($groupsDirectory);
    }

    /**
     * @inheritdoc
     */
    public function getPageContent(string $group, string $url)
    {
        $path = $this->getPagePath($group, $url);

        if ($path) {
            return file_get_contents($path);
        }
    }

    /**
     * @inheritdoc
     */
    public function hashContent(string $content)
    {
        return hash('sha256', $content);
    }

    private function getPagePath(string $group, string $url)
    {
        $path = $this->directory . '/' .
            $group . '/' . '/src/' .
            '/' . pathinfo($url, PATHINFO_DIRNAME) .
            '/' . pathinfo($url, PATHINFO_BASENAME);

        $path = realpath($path);

        if (!$path) {
            throw new PageNotFoundException($url . ' was not found');
        }

        if (strpos($path, $this->directory . '/') === false) {
            throw new PageNotFoundException('Page not found, attempt to browse a non-page file');
        }

        return $path;
    }
}
