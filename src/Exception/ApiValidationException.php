<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiValidationException extends VerboseException implements HttpExceptionInterface
{
    /**
     * @param string $message
     * @param array  $extraData
     *
     * @return VerboseExceptionInterface
     */
    public static function create($message = '', array $extraData = []): VerboseExceptionInterface
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, null, $extraData);
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return [];
    }
}
