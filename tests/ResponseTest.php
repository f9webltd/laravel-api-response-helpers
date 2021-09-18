<?php

declare(strict_types=1);

namespace F9Web\ApiResponseHelpers\Tests;

use DomainException;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
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
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"error":"Ouch!"}', $response->getContent());
        self::assertEquals(['error' => 'Ouch!'], $response->getData(true));

        $response = $this->service->respondNotFound(new DomainException('Unknown model'));
        self::assertJsonStringEqualsJsonString('{"error":"Unknown model"}', $response->getContent());
        self::assertEquals(['error' => 'Unknown model'], $response->getData(true));

        $response = $this->service->respondNotFound('Ouch!', 'nope');
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertJsonStringEqualsJsonString('{"nope":"Ouch!"}', $response->getContent());
        self::assertEquals(['nope' => 'Ouch!'], $response->getData(true));
    }

    public function testRespondWithSuccess(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondWithSuccess();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"success":true}', $response->getContent());
        self::assertEquals(['success' => true], $response->getData(true));

        $response = $this->service->respondWithSuccess(['super' => 'response', 'yes' => 123]);
        self::assertJsonStringEqualsJsonString('{"super":"response","yes":123}', $response->getContent());
        self::assertEquals(['super' => 'response', 'yes' => 123], $response->getData(true));

        $response = $this->service->respondWithSuccess(new Collection(['super' => 'response', 'yes' => 123]));
        self::assertJsonStringEqualsJsonString('{"super":"response","yes":123}', $response->getContent());
        self::assertEquals(['super' => 'response', 'yes' => 123], $response->getData(true));
    }

    public function testRespondOk(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondOk('Record updated');
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"success":"Record updated"}', $response->getContent());
        self::assertEquals(['success' => 'Record updated'], $response->getData(true));
    }

    public function testRespondUnAuthenticated(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondUnAuthenticated();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"error":"Unauthenticated"}', $response->getContent());
        self::assertEquals(['error' => 'Unauthenticated'], $response->getData(true));

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondUnAuthenticated('Not allowed');
        self::assertEquals(['error' => 'Not allowed'], $response->getData(true));
    }

    public function testRespondForbidden(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondForbidden();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"error":"Forbidden"}', $response->getContent());
        self::assertEquals(['error' => 'Forbidden'], $response->getData(true));

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondForbidden('No way');
        self::assertEquals(['error' => 'No way'], $response->getData(true));
    }

    public function testRespondError(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondError();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"error":"Error"}', $response->getContent());
        self::assertEquals(['error' => 'Error'], $response->getData(true));

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondError('Error ...');
        self::assertEquals(['error' => 'Error ...'], $response->getData(true));
    }

    public function testRespondCreated(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondCreated();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('[]', $response->getContent());
        self::assertEquals([], $response->getData(true));

        $response = $this->service->respondCreated([ 'id' => 123, 'title' => 'ABC' ]);
        self::assertJsonStringEqualsJsonString('{"id":123,"title":"ABC"}', $response->getContent());
        self::assertEquals([ 'id' => 123, 'title' => 'ABC' ], $response->getData(true));

        $response = $this->service->respondCreated(new Collection([ 'id' => 123, 'title' => 'ABC' ]));
        self::assertJsonStringEqualsJsonString('{"id":123,"title":"ABC"}', $response->getContent());
        self::assertEquals([ 'id' => 123, 'title' => 'ABC' ], $response->getData(true));
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testRespondFailedValidation(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondFailedValidation('Password is required');
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"message":"Password is required"}', $response->getContent());
        self::assertEquals(['message' => 'Password is required'], $response->getData(true));

        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondFailedValidation('Password is required', 'erm');
        self::assertJsonStringEqualsJsonString('{"erm":"Password is required"}', $response->getContent());
        self::assertEquals(['erm' => 'Password is required'], $response->getData(true));
    }

    public function testRespondTeapot(): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->respondTeapot();
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(Response::HTTP_I_AM_A_TEAPOT, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString('{"message":"I\'m a teapot"}', $response->getContent());
        self::assertEquals(['message' => 'I\'m a teapot'], $response->getData(true));
    }
}