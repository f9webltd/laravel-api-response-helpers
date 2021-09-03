![](https://banners.beyondco.de/API%20Response%20Helpers.png?theme=light&packageManager=composer+require&packageName=f9webltd%2Flaravel-api-response-helpers&pattern=brickWall&style=style_1&description=Some+simple+API+respons+ehelpers+for+your+Laravel+application&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

[![Packagist Version](https://img.shields.io/packagist/v/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)
[![Packagist PHP Version](
https://img.shields.io/packagist/php-v/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)
[![Packagist License](https://img.shields.io/packagist/l/f9webltd/laravel-api-response-helpers?style=flat-square)](https://packagist.org/packages/f9webltd/laravel-api-response-helpers)


# Laravel API Response Helpers

An insanely simple package allowing for consistent API responses throughout your Laravel application.

## Purpose

Whilst simple, this package has two aims:

- allow for consistent API responses throughout the application
- allow for more verbose / readable code

## Requirements

- PHP `^8.0`
- Laravel 8 or above

## Installation / Usage

`composer require f9webltd/laravel-api-response-helpers`


Simply reference the required trait in your controller. Optionally, use the trait on a base controller:

```
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


## Available methods


#### `respondNotFound(string|Exception $message, ?string $key = 'error')`

Returns a `404` HTTP status code, an excpetion object can optionally be passed.

#### `respondWithSuccess(?array $contents = [])`

Returns a `200` HTTP status code

#### `respondOk(string $message)`

Returns a `404` HTTP status code

#### `respondUnAuthenticated(?string $message = null)`

Returns a `401` HTTP status code

#### `respondForbidden(?string $message = null)`

Returns a `403` HTTP status code

#### `respondError(?string $message = null)`

Returns a `400` HTTP status code

#### `respondCreated()`

Returns a `201` HTTP status code

## Contribution

Any ideas are welcome. Feel free to submit any issues or pull requests.

## Testing

_Tests to follow..._

## Security

If you discover any security related issues, please email rob@f9web.co.uk instead of using the issue tracker.

## Credits

- [Rob Allport](https://github.com/ultrono) for [F9 Web Ltd.](https://www.f9web.co.uk)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

