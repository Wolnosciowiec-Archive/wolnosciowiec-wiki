<?php

namespace WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Hello',
            'details' => 'If you see this message instead of a proper page then probably the domain was not configured properly',
        ]);
    }

    public function routeByDomainAction(Request $request, string $url)
    {
        $repository = $this->get('wolnosciowiec.wiki.handler.host')->getRepositoryForDomain($request->getHost());

        if ($repository->isValid()) {

            if (!$url) {
                $url = $repository->getIndexPath();
            }

            $controller = new BrowserController();
            $controller->setContainer($this->get('service_container'));
            return $controller->indexAction($request, $repository->getName(), $url);
        }

        return $this->indexAction($request);
    }
}
