![](https://banners.beyondco.de/Laravel%20API%20Response%20Helpers.png?theme=light&packageManager=composer+require&packageName=f9webltd%2Flaravel-api-response-helpers&pattern=brickWall&style=style_1&description=Generate+consistent+API+responses+for+your+Laravel+application&md=1&showWatermark=0&fontSize=100px&images=code)

[![run-tests](https://img.shields.io/github/workflow/status/f9webltd/laravel-api-response-helpers/run-tests?style=flat-square)](https://github.com/f9webltd/laravel-api-response-helpers/actions)
[![Packagist Version](https://img.shields.io/packagist/v/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)
[![Packagist PHP Version](
https://img.shields.io/packagist/php-v/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)
[![Packagist License](https://img.shields.io/packagist/l/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)


# Laravel API Response Helpers

A simple package allowing for consistent API responses throughout your Laravel application.

## Requirements

- PHP `^7.4 | ^8.0`
- Laravel 6, 7, 8, 0 and 10

## Installation / Usage

`composer require f9webltd/laravel-api-response-helpers`


Simply reference the required trait within your controller:

```php
<?php

namespace App\Http\Api\Controllers;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class OrdersController
{
    use ApiResponseHelpers;

    public function index(): JsonResponse
    {
        return $this->respondWithSuccess();
    }
}
```

Optionally, the trait could be imported within a base controller.

## Available methods

#### `respondNotFound(string|Exception $message, ?string $key = 'error')`

Returns a `404` HTTP status code, an exception object can optionally be passed.

#### `respondWithSuccess(array|Arrayable|JsonSerializable|null $contents = null)`

Returns a `200` HTTP status code, optionally `$contents` to return as json can be passed. By default returns `['success' => true]`.

#### `respondOk(string $message)`

Returns a `200` HTTP status code

#### `respondUnAuthenticated(?string $message = null)`

Returns a `401` HTTP status code

#### `respondForbidden(?string $message = null)`

Returns a `403` HTTP status code

#### `respondError(?string $message = null)`

Returns a `400` HTTP status code

#### `respondCreated(array|Arrayable|JsonSerializable|null $data = null)`

Returns a `201` HTTP status code, with response optional data

#### `respondNoContent(array|Arrayable|JsonSerializable|null $data = null)`

Returns a `204` HTTP status code, with optional response data. Strictly speaking, the response body should be empty. However, functionality to optionally return data was added to handle legacy projects. Within your own projects, you can simply call the method, omitting parameters, to generate a correct `204` response i.e. `return $this->respondNoContent()`

#### `setDefaultSuccessResponse(?array $content = null): self`

Optionally, replace the default `['success' => true]` response returned by `respondWithSuccess` with `$content`. This method can be called from the constructor (to change default for all calls), a base API controller or place when required. 

`setDefaultSuccessResponse` is a fluent method returning `$this` allows for chained methods calls:

```php
$users = collect([10, 20, 30, 40]);

return $this->setDefaultSuccessResponse([])->respondWithSuccess($users);
```

Or
```php
public function __construct()
{
    $this->setDefaultSuccessResponse([]);
}

...

$users = collect([10, 20, 30, 40]);

return $this->respondWithSuccess($users);
```


## Use with additional object types

In addition to a plain PHP `array`, the following data types can be passed to relevant methods:

- Objects implementing the Laravel `Illuminate\Contracts\Support\Arrayable` contract
- Objects implementing the native PHP `JsonSerializable` contract

This allows a variety of object types to be passed and converted automatically.

Below are a few common object types that can be passed.

#### Laravel Collections - `Illuminate\Support\Collection`

```php
$users = collect([10, 20, 30, 40]);

return $this->respondWithSuccess($users);
```

#### Laravel Eloquent Collections - `Illuminate\Database\Eloquent\Collection`

```php
$invoices = Invoice::pending()->get();

return $this->respondWithSuccess($invoices);
```

#### Laravel API Resources - `Illuminate\Http\Resources\Json\JsonResource`

This package is intended to be used **alongside** Laravel's  [API resources](https://laravel.com/docs/8.x/eloquent-resources) and in no way replaces them.

```php
$resource = PostResource::make($post);

return $this->respondCreated($resource);
```

## Motivation

Ensure consistent JSON API responses throughout an application. The motivation was primarily based on a very old inherited Laravel project. The project contained a plethora of methods/structures used to return an error:

- `response()->json(['error' => $error], 400)`
- `response()->json(['data' => ['error' => $error], 400)`
- `response()->json(['message' => $error], Response::HTTP_BAD_REQUEST)`
- `response()->json([$error], 400)`
- etc.

I wanted to add a simple trait that kept this consistent, in this case:

`$this->respondError('Ouch')`

## Contribution

Any ideas are welcome. Feel free to submit any issues or pull requests.

## Testing

`composer test`

## Security

If you discover any security related issues, please email rob@f9web.co.uk instead of using the issue tracker.

## Credits

- [Rob Allport](https://github.com/ultrono) for [F9 Web Ltd.](https://www.f9web.co.uk)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
