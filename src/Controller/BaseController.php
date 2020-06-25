<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    /**
     * @param EntityInterface $entity
     * @param array $group
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function serializeToJsonResponse(EntityInterface $entity, array $group = [], int $status = 200, array $headers = []): JsonResponse
    {
        return $this->json($entity, $status, $headers, ['groups' => $group]);
    }
}
