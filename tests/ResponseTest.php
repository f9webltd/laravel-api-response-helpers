<?php

declare(strict_types=1);

namespace F9Web\ApiResponseHelpers\Tests;

use F9Web\ApiResponseHelpers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JsonException;
use DomainException;
use Illuminate\Support\Collection;
use F9Web\ApiResponseHelpers\Tests\Models\User;
use F9Web\ApiResponseHelpers\Tests\Resources\UserResource;
use Illuminate\Database\Eloquent\Collection As EloquentCollection;

use function json_encode;

class ResponseTest extends TestCase
{
    protected object $service;

    public function setUp(): void
    {
        $this->service = $this->getObjectForTrait(ApiResponseHelpers::class);
        parent::setUp();
    }

    /**
     * @dataProvider basicResponsesDataProvider
     * @throws JsonException
     */
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
     * @dataProvider successDefaultsDataProvider
     * @throws JsonException
     */
    public function testSuccessResponseDefaults(?array $default, $args, int $code, array $data): void
    {
        $response = $this->service->setDefaultSuccessResponse($default)->respondWithSuccess($args);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($data, $response->getData(true));
        self::assertJsonStringEqualsJsonString(json_encode($data, JSON_THROW_ON_ERROR), $response->getContent());
    }

    public function basicResponsesDataProvider(): array
    {
        return [
          'respondNotFound()' => [
            'respondNotFound',
            ['Ouch'],
            Response::HTTP_NOT_FOUND,
            ['error' => 'Ouch'],
          ],

          'respondNotFound() with custom key' => [
            'respondNotFound',
            ['Ouch', 'message'],
            Response::HTTP_NOT_FOUND,
            ['message' => 'Ouch'],
          ],

          'respondNotFound() with exception and custom key' => [
            'respondNotFound',
            [
              new DomainException('Unknown model'),
              'message'
            ],
            Response::HTTP_NOT_FOUND,
            ['message' => 'Unknown model'],
          ],

          'respondWithSuccess(), default response data' => [
            'respondWithSuccess',
            [],
            Response::HTTP_OK,
            ['success' => true],
          ],

          'respondWithSuccess(), null response data' => [
            'respondWithSuccess',
            [null],
            Response::HTTP_OK,
            ['success' => true],
          ],

          'respondWithSuccess(), custom response data' => [
            'respondWithSuccess',
            [['order' => 237]],
            Response::HTTP_OK,
            ['order' => 237],
          ],

          'respondWithSuccess(), nested custom response data' => [
            'respondWithSuccess',
            [['order' => 237, 'customer' => ['name' => 'Jason Bourne']]],
            Response::HTTP_OK,
            ['order' => 237, 'customer' => ['name' => 'Jason Bourne']],
          ],

          'respondWithSuccess(), collection' => [
            'respondWithSuccess',
            [new Collection(['invoice' => 23, 'status' => 'pending'])],
            Response::HTTP_OK,
            ['invoice' => 23, 'status' => 'pending'],
          ],

          'respondWithSuccess(), empty collection' => [
            'respondWithSuccess',
            [new Collection()],
            Response::HTTP_OK,
            ['success' => true],
          ],

          'respondWithSuccess(), Arrayable' => [
            'respondWithSuccess',
            [
              new class implements Arrayable {
                public function toArray()
                {
                  return ['id' => 1, 'name' => 'John'];
                }
              },
            ],
            Response::HTTP_OK,
            ['id' => 1, 'name' => 'John']
          ],

          'respondWithSuccess(), empty Arrayable' => [
            'respondWithSuccess',
            [
              new class implements Arrayable {
                public function toArray()
                {
                  return [];
                }
              },
            ],
            Response::HTTP_OK,
            ['success' => true]
          ],

          'respondWithSuccess(), JsonSerializable' => [
            'respondWithSuccess',
            [
              new class implements \JsonSerializable {
                public function jsonSerialize()
                {
                  return ['id' => 1, 'name' => 'John'];
                }
              },
            ],
            Response::HTTP_OK,
            ['id' => 1, 'name' => 'John']
          ],

          'respondWithSuccess(), empty JsonSerializable' => [
            'respondWithSuccess',
            [
              new class implements \JsonSerializable {
                public function jsonSerialize()
                {
                  return [];
                }
              },
            ],
            Response::HTTP_OK,
            ['success' => true]
          ],

          'respondOk()' => [
            'respondOk',
            ['Order accepted'],
            Response::HTTP_OK,
            ['success' => 'Order accepted'],
          ],

          'respondUnAuthenticated(), default message' => [
            'respondUnAuthenticated',
            [],
            Response::HTTP_UNAUTHORIZED,
            ['error' => 'Unauthenticated'],
          ],

          'respondUnAuthenticated(), custom message' => [
            'respondUnAuthenticated',
            ['Denied'],
            Response::HTTP_UNAUTHORIZED,
            ['error' => 'Denied'],
          ],

          'respondForbidden(), default message' => [
            'respondForbidden',
            [],
            Response::HTTP_FORBIDDEN,
            ['error' => 'Forbidden'],
          ],

          'respondForbidden(), custom message' => [
            'respondForbidden',
            ['Disavowed'],
            Response::HTTP_FORBIDDEN,
            ['error' => 'Disavowed'],
          ],

          'respondError(), default message' => [
            'respondError',
            [],
            Response::HTTP_BAD_REQUEST,
            ['error' => 'Error'],
          ],

          'respondError(), custom message' => [
            'respondError',
            ['Order tampering detected'],
            Response::HTTP_BAD_REQUEST,
            ['error' => 'Order tampering detected'],
          ],

          'respondCreated()' => [
            'respondCreated',
            [],
            Response::HTTP_CREATED,
            [],
          ],

          'respondCreated() with response data' => [
            'respondCreated',
            [['user' => ['name' => 'Jet Li']]],
            Response::HTTP_CREATED,
            ['user' => ['name' => 'Jet Li']],
          ],

          'respondCreated(), with collection as response' => [
            'respondCreated',
            [new Collection(['invoice' => 23, 'status' => 'pending'])],
            Response::HTTP_CREATED,
            ['invoice' => 23, 'status' => 'pending'],
          ],

          'respondCreated(), with eloquent collection as response' => [
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
          ],

          'respondCreated(), with json resource as response' => [
            'respondCreated',
            [
              new UserResource(
                new User(['name' => 'Jet Li', 'age' => 58])
              )
            ],
            Response::HTTP_CREATED,
            ['nameAndAge' => 'Jet Li & 58'],
          ],

          'respondCreated(), with resource collection as response' => [
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
          ],

          'respondFailedValidation()' => [
            'respondFailedValidation',
            ['An invoice is required'],
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ['message' => 'An invoice is required'],
          ],

          'respondTeapot()' => [
            'respondTeapot',
            [],
            Response::HTTP_I_AM_A_TEAPOT,
            ['message' => 'I\'m a teapot'],
          ],

          'respondNoContent()' => [
            'respondNoContent',
            [],
            Response::HTTP_NO_CONTENT,
            [],
          ],

          // @see https://github.com/f9webltd/laravel-api-response-helpers/issues/5#issuecomment-917418285
          'respondNoContent() with data' => [
            'respondNoContent',
            [['role' => 'manager']],
            Response::HTTP_NO_CONTENT,
            ['role' => 'manager'],
          ],

          // @see https://github.com/f9webltd/laravel-api-response-helpers/issues/5#issuecomment-917418285
          'respondNoContent(), with collection as response' => [
            'respondNoContent',
            [new Collection(['invoice' => 23, 'status' => 'pending'])],
            Response::HTTP_NO_CONTENT,
            ['invoice' => 23, 'status' => 'pending'],
          ],
        ];
    }

    public function successDefaultsDataProvider(): array
    {
        return [
            'respondWithSuccess(), default empty array' => [
                'default' => [],
                'args' => [],
                'code' => Response::HTTP_OK,
                'data' => [],
            ],
            'respondWithSuccess(), default null' => [
                'default' => null,
                'args' => [],
                'code' => Response::HTTP_OK,
                'data' => [],
            ],
            'respondWithSuccess(), default null, null response' => [
                'default' => null,
                'args' => null,
                'code' => Response::HTTP_OK,
                'data' => [],
            ],
            'respondWithSuccess(), default non-empty array' => [
                'default' => ['message' => 'Task successful!'],
                'args' => [],
                'code' => Response::HTTP_OK,
                'data' => ['message' => 'Task successful!'],
            ],
            'respondWithSuccess(), default non-empty array, custom response data' => [
                'default' => ['message' => 'Task successful!'],
                'args' => ['numbers' => [1, 2, 3]],
                'code' => Response::HTTP_OK,
                'data' => ['numbers' => [1, 2, 3]],
            ]
        ];
    }
}
