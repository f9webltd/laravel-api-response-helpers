<?php

declare(strict_types=1);

namespace F9Web\ApiResponseHelpers\Tests;

use DomainException;
use F9Web\ApiResponseHelpers;
use F9Web\ApiResponseHelpers\Tests\Models\User;
use F9Web\ApiResponseHelpers\Tests\Resources\UserResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use JsonException;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;

use function json_encode;

class ApiResponseService {
    use \F9Web\ApiResponseHelpers;
}

class ResponseTest extends TestCase
{
    protected ApiResponseService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new ApiResponseService();
    }

    /*
     * @throws JsonException
     */
    #[DataProvider('basicResponsesDataProvider')]
    public function testResponses(string $method, array $args, int $code, array $data): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = call_user_func_array([$this->service, $method], $args);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($data, $response->getData(true));
        self::assertJsonStringEqualsJsonString(json_encode($data, JSON_THROW_ON_ERROR), $response->getContent());
    }

    /**
     * @throws JsonException
     */
    #[DataProvider('successDefaultsDataProvider')]
    public function testSuccessResponseDefaults(?array $default, $args, int $code, array $data): void
    {
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $this->service->setDefaultSuccessResponse($default)->respondWithSuccess($args);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($data, $response->getData(true));
        self::assertJsonStringEqualsJsonString(json_encode($data, JSON_THROW_ON_ERROR), $response->getContent());
    }

    public static function basicResponsesDataProvider(): iterable
    {
		yield 'respondNotFound()' => [
			'respondNotFound',
			['Ouch'],
			Response::HTTP_NOT_FOUND,
			['error' => 'Ouch'],
		];
		
		yield 'respondNotFound() with custom key' => [
			'respondNotFound',
			['Ouch', 'message'],
			Response::HTTP_NOT_FOUND,
			['message' => 'Ouch'],
		];
		
		yield 'respondNotFound() with exception and custom key' => [
			'respondNotFound',
			[new DomainException('Unknown model'), 'message'],
			Response::HTTP_NOT_FOUND,
			['message' => 'Unknown model'],
		];
		
		yield 'respondWithSuccess(), default response data' => [
			'respondWithSuccess',
			[],
			Response::HTTP_OK,
			['success' => true],
		];
		
		yield 'respondWithSuccess(), null response data' => [
			'respondWithSuccess',
			[null],
			Response::HTTP_OK,
			['success' => true],
		];
		
		yield 'respondWithSuccess(), custom response data' => [
			'respondWithSuccess',
			[['order' => 237]],
			Response::HTTP_OK,
			['order' => 237],
		];
		
		yield 'respondWithSuccess(), nested custom response data' => [
			'respondWithSuccess',
			[['order' => 237, 'customer' => ['name' => 'Jason Bourne']]],
			Response::HTTP_OK,
			['order' => 237, 'customer' => ['name' => 'Jason Bourne']],
		];
		
		yield 'respondWithSuccess(), collection' => [
			'respondWithSuccess',
			[new Collection(['invoice' => 23, 'status' => 'pending'])],
			Response::HTTP_OK,
			['invoice' => 23, 'status' => 'pending'],
		];
		
		yield 'respondWithSuccess(), empty collection' => [
			'respondWithSuccess',
			[new Collection()],
			Response::HTTP_OK,
			['success' => true],
		],
		
		yield 'respondWithSuccess(), Arrayable' => [
			'respondWithSuccess',
			[
				new class implements Arrayable {
					public function toArray() { return ['id' => 1, 'name' => 'John']; }
				},
			],
			Response::HTTP_OK,
			['id' => 1, 'name' => 'John']
		];
		
		yield 'respondWithSuccess(), empty Arrayable' => [
			'respondWithSuccess',
			[
				new class implements Arrayable {
					public function toArray() { return []; }
				},
			],
			Response::HTTP_OK,
			['success' => true]
		];
		
		yield 'respondWithSuccess(), JsonSerializable' => [
		'respondWithSuccess',
			[
				new class implements \JsonSerializable {
					public function jsonSerialize() { return ['id' => 1, 'name' => 'John']; }
				},
			],
			Response::HTTP_OK,
			['id' => 1, 'name' => 'John']
		];
		
		yield 'respondWithSuccess(), empty JsonSerializable' => [
			'respondWithSuccess',
			[
				new class implements \JsonSerializable {
					public function jsonSerialize() { return []; }
				},
			],
			Response::HTTP_OK,
			['success' => true]
		];
		
		yield 'respondOk()' => [
			'respondOk',
			['Order accepted'],
			Response::HTTP_OK,
			['success' => 'Order accepted'],
		];
		
		yield 'respondUnAuthenticated(), default message' => [
			'respondUnAuthenticated',
			[],
			Response::HTTP_UNAUTHORIZED,
			['error' => 'Unauthenticated'],
		];
		
		yield 'respondUnAuthenticated(), custom message' => [
			'respondUnAuthenticated',
			['Denied'],
			Response::HTTP_UNAUTHORIZED,
			['error' => 'Denied'],
		];
		
		yield 'respondForbidden(), default message' => [
			'respondForbidden',
			[],
			Response::HTTP_FORBIDDEN,
			['error' => 'Forbidden'],
		];
		
		yield 'respondForbidden(), custom message' => [
			'respondForbidden',
			['Disavowed'],
			Response::HTTP_FORBIDDEN,
			['error' => 'Disavowed'],
		];
		
		yield 'respondError(), default message' => [
			'respondError',
			[],
			Response::HTTP_BAD_REQUEST,
			['error' => 'Error'],
		];
		
		yield 'respondError(), custom message' => [
			'respondError',
			['Order tampering detected'],
			Response::HTTP_BAD_REQUEST,
			['error' => 'Order tampering detected'],
		];
		
		yield 'respondCreated()' => [
			'respondCreated',
			[],
			Response::HTTP_CREATED,
			[],
		];
		
		yield 'respondCreated() with null' => [
			'respondCreated',
			[null],
			Response::HTTP_CREATED,
			[],
		];
		
		yield 'respondCreated() with response data' => [
			'respondCreated',
			[['user' => ['name' => 'Jet Li']]],
			Response::HTTP_CREATED,
			['user' => ['name' => 'Jet Li']],
		];
		
		yield 'respondCreated(), with collection as response' => [
			'respondCreated',
			[new Collection(['invoice' => 23, 'status' => 'pending'])],
			Response::HTTP_CREATED,
			['invoice' => 23, 'status' => 'pending'],
		];
		
		yield 'respondCreated(), with eloquent collection as response' => [
			'respondCreated',
			[
				new EloquentCollection([
					new User(['name' => 'Jet Li', 'age' => 58]),
					new User(['name' => 'Chow Yun-Fat', 'age' => 66]),
					new User(['name' => 'Donnie Yen', 'age' => 58])
				])
			],
			Response::HTTP_CREATED,
			[
				['name' => 'Jet Li', 'age' => 58],
				['name' => 'Chow Yun-Fat', 'age' => 66],
				['name' => 'Donnie Yen', 'age' => 58]
			],
		];
		
		yield 'respondCreated(), with json resource as response' => [
			'respondCreated',
			[
				new UserResource(new User(['name' => 'Jet Li', 'age' => 58]))
			],
			Response::HTTP_CREATED,
			['nameAndAge' => 'Jet Li & 58'],
		];
		
		yield 'respondCreated(), with resource collection as response' => [
			'respondCreated',
			[
				UserResource::collection(
					new EloquentCollection([
						new User(['name' => 'Jet Li', 'age' => 58]),
						new User(['name' => 'Chow Yun-Fat', 'age' => 66]),
						new User(['name' => 'Donnie Yen', 'age' => 58])
					])
				)
			],
			Response::HTTP_CREATED,
			[
				['nameAndAge' => 'Jet Li & 58'],
				['nameAndAge' => 'Chow Yun-Fat & 66'],
				['nameAndAge' => 'Donnie Yen & 58']
			],
		];
		
		yield 'respondFailedValidation()' => [
			'respondFailedValidation',
			['An invoice is required'],
			Response::HTTP_UNPROCESSABLE_ENTITY,
			['message' => 'An invoice is required'],
		];
		
		yield 'respondTeapot()' => [
			'respondTeapot',
			[],
			Response::HTTP_I_AM_A_TEAPOT,
			['message' => 'I\'m a teapot'],
		];
		
		yield 'respondNoContent()' => [
			'respondNoContent',
			[],
			Response::HTTP_NO_CONTENT,
			[],
		];
		
		yield 'respondNoContent() with null' => [
			'respondNoContent',
			[null],
			Response::HTTP_NO_CONTENT,
			[],
		];
		
		// @see https://github.com/f9webltd/laravel-api-response-helpers/issues/5#issuecomment-917418285
		yield 'respondNoContent() with data' => [
			'respondNoContent',
			[['role' => 'manager']],
			Response::HTTP_NO_CONTENT,
			['role' => 'manager'],
		];
		
		// @see https://github.com/f9webltd/laravel-api-response-helpers/issues/5#issuecomment-917418285
		yield 'respondNoContent(), with collection as response' => [
			'respondNoContent',
			[new Collection(['invoice' => 23, 'status' => 'pending'])],
			Response::HTTP_NO_CONTENT,
			['invoice' => 23, 'status' => 'pending'],
		];

		yield 'respondAccepted() with data' => [
			'respondAccepted',
			[['role' => 'manager']],
			Response::HTTP_ACCEPTED,
			[],
		];

		yield 'respondConflict(), no message' => [
			'respondConflict',
			[null],
			Response::HTTP_CONFLICT,
			['error' => 'Conflict'],
		];

		yield 'respondConflict(), custom message' => [
			'respondConflict',
			['Hmmm, conflicted ...'],
			Response::HTTP_CONFLICT,
			['error' => 'Hmmm, conflicted ...'],
		];

		yield 'respondTooManyRequests(), no message' => [
			'respondTooManyRequests',
			[null],
			Response::HTTP_TOO_MANY_REQUESTS,
			['error' => 'Too Many Requests'],
		];

		yield 'respondTooManyRequests(), custom message' => [
			'respondTooManyRequests',
			['Spamming ...'],
			Response::HTTP_TOO_MANY_REQUESTS,
			['error' => 'Spamming ...'],
		];
    }

    public static function successDefaultsDataProvider(): iterable
    {
        yield 'respondWithSuccess(), default empty array' => [
            'default' => [],
            'args' => [],
            'code' => Response::HTTP_OK,
            'data' => [],
        ];

        yield 'respondWithSuccess(), default null' => [
            'default' => null,
            'args' => [],
            'code' => Response::HTTP_OK,
            'data' => [],
        ];

        yield 'respondWithSuccess(), default null, null response' => [
            'default' => null,
            'args' => null,
            'code' => Response::HTTP_OK,
            'data' => [],
        ];

        yield 'respondWithSuccess(), default non-empty array' => [
            'default' => ['message' => 'Task successful!'],
            'args' => [],
            'code' => Response::HTTP_OK,
            'data' => ['message' => 'Task successful!'],
        ];

        yield 'respondWithSuccess(), default non-empty array, custom response data' => [
            'default' => ['message' => 'Task successful!'],
            'args' => ['numbers' => [1, 2, 3]],
            'code' => Response::HTTP_OK,
            'data' => ['numbers' => [1, 2, 3]],
        ];
    }
}
