<?php
namespace App\Controller;

use App\Service\PropertyDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    #[Route('/properties', name: 'app_properties', methods: ['GET'])]
    public function index(Request $request, PropertyDataService $service): JsonResponse
    {
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));

        try {
            $allData   = $service->getMergedProperties();
            $offset    = ($page - 1) * $limit;
            $pagedData = array_slice($allData, $offset, $limit);

            return $this->json([
                'page'  => $page,
                'limit' => $limit,
                'total' => count($allData),
                'data'  => $pagedData,
            ]);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }
}
