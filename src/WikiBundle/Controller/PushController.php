<?php declare(strict_types=1);

namespace WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Serve pages to the browser
 */
class PushController extends Controller
{
    public function indexAction(Request $request, $fetcherName)
    {
        ignore_user_abort(true);

        $fetcher = $this->get('wolnosciowiec.wiki.fetcher');
        $payload = $this->get('wolnosciowiec.wiki.factory.payload')->create($request->getContent());
        $storageManager = $this->get('wolnosciowiec.wiki.manager.storage');
        $processor = $this->get('wolnosciowiec.wiki.processor');

        try {
            $repositoryName = $storageManager->getRepositoryName($payload->getUrl(), $payload->getBranch());
            $path = $fetcher->cloneRepository($fetcherName, $payload->getUrl(), $payload->getBranch());
            $processor->processDirectory($path, $repositoryName);

        } catch (\Exception $e) {
            $this->get('logger')->critical(
                'Push failed for ' . $payload->getUrl() . '@' . $payload->getBranch() . ': ' . $e->getMessage());

            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
