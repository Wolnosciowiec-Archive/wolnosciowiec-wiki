<?php

namespace WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WikiBundle\Domain\Browser\PagesBrowserInterface;
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
            $content = $this->getBrowser()->getPageContent($groupName, $url);
        }
        catch (PageNotFoundException $e) {
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
        ]);
    }
}
