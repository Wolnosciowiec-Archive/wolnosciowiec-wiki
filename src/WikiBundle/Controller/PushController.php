<?php declare(strict_types=1);

namespace WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WikiBundle\Exception\PushFailedException;

/**
 * Serve pages to the browser
 */
class PushController extends Controller
{
    public function __construct()
    {
        ignore_user_abort(true);
    }

    public function indexAction(Request $request, string $fetcherName)
    {
        $payload = $this->get('wolnosciowiec.wiki.factory.payload')->create($request->getContent());

        try {
            $this->get('wolnosciowiec.wiki.handler.payload')->handlePayload($payload, $fetcherName);

        } catch (PushFailedException $e) {
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
