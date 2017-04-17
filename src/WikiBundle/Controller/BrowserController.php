<?php declare(strict_types=1);

namespace WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Stopwatch\Stopwatch;
use WikiBundle\Domain\Service\Browser\PagesBrowserInterface;
use WikiBundle\Exception\Browser\PageNotFoundException;

/**
 * Serve pages to the browser
 */
class BrowserController extends Controller
{
    /**
     * @return PagesBrowserInterface
     */
    private function getBrowser(): PagesBrowserInterface
    {
        return $this->get('wolnosciowiec.wiki.browser');
    }

    public function indexAction(Request $request, string $groupName, string $url)
    {
        try {
            $this->get('debug.stopwatch')->start('browser.render');

            if ($this->getBrowser()->isAsset($groupName, $url)) {
                $asset = $this->getBrowser()->getAssetStream($groupName, $url);

                if (empty($asset)) {
                    throw new AccessDeniedException($url);
                }

                return new StreamedResponse(function () use ($asset) {
                    fpassthru($asset['stream']);
                }, 200, [
                    'Content-Type' => $asset['mime'],
                    'Content-Length' => $asset['length'],
                ]);
            }

            $content = $this->getBrowser()->getPageContent($groupName, $url);

            $this->get('debug.stopwatch')->stop('browser.render');

        } catch (PageNotFoundException $e) {
            throw new NotFoundHttpException('Sorry, the "' . $url . '" page was not found in "' . $groupName . '" group');
        }

        if ($request->headers->get('Accept') == 'application/json') {
            return new JsonResponse([
                'data' => $content,
                'hash' => $this->getBrowser()->hashContent($content),
                'type' => 'text/html',
            ]);
        }

        return new Response($content, 200, [
            'ETag' => $this->getBrowser()->hashContent($content),
            'X-Powered-By' => 'Wolnosciowiec Wiki',
        ]);
    }
}
