<?php

declare(strict_types=1);

namespace F9Web\ApiResponseHelpers\Tests;

use DomainException;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseTest extends TestCase
{
    protected object $service;

    public function setUp(): void
    {
        $this->service = $this->getObjectForTrait(ApiResponseHelpers::class);
        parent::setUp();
    }

    public function testRespondNotFound(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondNotFound('Ouch!');
		$expected_response['status_code'] = Response::HTTP_NOT_FOUND;
		$expected_response['json'] = ['error' => 'Ouch!'];
		$this->testResponse($expected_response, $response);

        $response = $this->service->respondNotFound(new DomainException('Unknown model'));
		$expected_response['status_code'] = null;
		$expected_response['json'] = ['error' => 'Unknown model'];
		$this->testResponse($expected_response, $response);

        $response = $this->service->respondNotFound('Ouch!', 'nope');
		$expected_response['json'] = ['nope' => 'Ouch!'];
		$this->testResponse($expected_response, $response);
    }

    public function testRespondWithSuccess(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondWithSuccess();
		$expected_response['status_code'] = Response::HTTP_OK;
		$expected_response['json'] = ['success' => true];
		$this->testResponse($expected_response, $response);

        $response = $this->service->respondWithSuccess(['super' => 'response', 'yes' => 123]);
		$expected_response['status_code'] = null;
		$expected_response['json'] = ['super' => 'response', 'yes' => 123];
		$this->testResponse($expected_response, $response);
    }

    public function testRespondOk(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondOk('Record updated');
		$expected_response['status_code'] = Response::HTTP_OK;
		$expected_response['json'] = ['success' => 'Record updated'];
		$this->testResponse($expected_response, $response);
    }

    public function testRespondUnAuthenticated(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondUnAuthenticated();
		$expected_response['status_code'] = Response::HTTP_UNAUTHORIZED;
		$expected_response['json'] = ['error' => 'Unauthenticated'];
		$this->testResponse($expected_response, $response);
		

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondUnAuthenticated('Not allowed');
        self::assertEquals(['error' => 'Not allowed'], $response->getData(true));
    }

    public function testRespondForbidden(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondForbidden();
		$expected_response['status_code'] = Response::HTTP_FORBIDDEN;
		$expected_response['json'] = ['error' => 'Forbidden'];
		$this->testResponse($expected_response, $response);

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondForbidden('No way');
        self::assertEquals(['error' => 'No way'], $response->getData(true));
    }

    public function testRespondError(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondError();
		$expected_response['status_code'] = Response::HTTP_BAD_REQUEST;
		$expected_response['json'] = ['error' => 'Error'];
		$this->testResponse($expected_response, $response);

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondError('Error ...');
        self::assertEquals(['error' => 'Error ...'], $response->getData(true));
    }

    public function testRespondCreated(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondCreated();
		$expected_response['status_code'] = Response::HTTP_CREATED;
		$expected_response['json'] = [];
		$this->testResponse($expected_response, $response);
    }

    public function testRespondFailedValidation(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondFailedValidation('Password is required');
		$expected_response['status_code'] = Response::HTTP_UNPROCESSABLE_ENTITY;
		$expected_response['json'] = ['message' => 'Password is required'];
		$this->testResponse($expected_response, $response);

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondFailedValidation('Password is required', 'erm');
		$expected_response['status_code'] = null;
		$expected_response['json'] = ['erm' => 'Password is required'];
		$this->testResponse($expected_response, $response);
    }

    public function testRespondTeapot(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondTeapot();
		$expected_response['status_code'] = Response::HTTP_I_AM_A_TEAPOT;
		$expected_response['json'] = ['message' => 'I\'m a teapot'];
		$this->testResponse($expected_response, $response);
    }

	public function testResponse($expected_response, $response): void
	{
		self::assertInstanceOf(JsonResponse::class, $response);
		if ($expected_response['status_code'] !== null) {
			self::assertEquals($expected_response['status_code'], $response->getStatusCode());
		}
		if ($expected_response['json'] !== null) {
			self::assertJsonStringEqualsJsonString(json_encode($expected_response['json']), $response->getContent());
			self::assertEquals($expected_response['json'], $response->getData(true));
		}
	}
}
