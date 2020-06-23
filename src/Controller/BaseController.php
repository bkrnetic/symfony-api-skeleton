<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    protected function serializeToJsonResponse($entity, $group = [], $status = 200, $headers = []): JsonResponse
    {
        return $this->json($entity, $status, $headers, ['groups' => $group]);
    }
}
