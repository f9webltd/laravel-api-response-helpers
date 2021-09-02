<?php

declare(strict_types=1);

namespace F9Web;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function response;

trait ApiResponseHelpers
{
    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound(
        string|Exception $message,
        ?string $key = 'error'
    ): JsonResponse {
        $message = $message instanceof Exception
            ? $message->getMessage()
            : $message;

        return $this->apiResponse(
            [$key => $message],
            Response::HTTP_NOT_FOUND
        );
    }

    public function respondWithSuccess(?array $contents = []): JsonResponse
    {
        $data = [] === $contents
            ? ['success' => true]
            : $contents;

        return $this->apiResponse($data);
    }

    public function respondOk(string $message): JsonResponse
    {
        return $this->respondWithSuccess([$message]);
    }

    public function respondUnAuthenticated(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Unauthenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function respondForbidden(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Forbidden'],
            Response::HTTP_FORBIDDEN
        );
    }

    public function respondError(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            ['error' => $message ?? 'Error'],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function respondCreated(): JsonResponse
    {
        return $this->apiResponse([], Response::HTTP_CREATED);
    }

    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }
}
