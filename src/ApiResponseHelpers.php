<?php

declare(strict_types=1);

namespace F9Web;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;

use function response;

trait ApiResponseHelpers
{
    private ?array $_api_helpers_defaultSuccessData = ['success' => true];

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound(
        $message,
        ?string $key = 'error'
    ): JsonResponse {
        return $this->apiResponse(
            [$key => $this->morphMessage($message)],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $contents
     */
    public function respondWithSuccess($contents = null): JsonResponse
    {
        $contents = $this->morphToArray($contents) ?? [];

        $data = [] === $contents
            ? $this->_api_helpers_defaultSuccessData
            : $contents;

        return $this->apiResponse($data);
    }

    public function setDefaultSuccessResponse(?array $content = null): self
    {
        $this->_api_helpers_defaultSuccessData = $content ?? [];
        return $this;
    }

    public function respondOk(string $message): JsonResponse
    {
        return $this->respondWithSuccess(['success' => $message]);
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

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondCreated($data = null): JsonResponse
    {
        $data ??= [];
        return $this->apiResponse(
          $this->morphToArray($data),
          Response::HTTP_CREATED
        );
    }
    
    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondFailedValidation(
        $message,
        ?string $key = 'message'
    ): JsonResponse {
        return $this->apiResponse(
            [$key => $this->morphMessage($message)],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function respondTeapot(): JsonResponse
    {
        return $this->apiResponse(
          ['message' => 'I\'m a teapot'],
          Response::HTTP_I_AM_A_TEAPOT
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondNoContent($data = null): JsonResponse
    {
        $data ??= [];
        $data = $this->morphToArray($data);

        return $this->apiResponse($data, Response::HTTP_NO_CONTENT);
    }

    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     * @return array|null
     */
    private function morphToArray($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param string|\Exception $message
     * @return string
     */
    private function morphMessage($message): string
    {
        return $message instanceof Exception
          ? $message->getMessage()
          : $message;
    }
}
