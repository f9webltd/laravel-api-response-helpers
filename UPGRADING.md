# Upgrading

## From 3.x to 4.x

- Run the following command to fetch the latest package version: `composer require f9webltd/laravel-api-response-helpers:^4.0`
- **Breaking change**: dropped support for Laravel 11. Laravel 11 reached end-of-life on 12/03/2026. This package now requires Laravel 12 or 13. See the [Laravel support policy](https://laravel.com/docs/master/releases#support-policy).
- **Breaking change**: To comply with [RFC 9110](https://httpwg.org/specs/rfc9110.html#status.204), `respondNoContent()` no longer accepts arguments and no longer returns a `JsonResponse`. It now returns a `Symfony\Component\HttpFoundation\Response` with an empty body and a `204` status code. If `respondNoContent()` is not used in your codebase, no changes are needed. Otherwise:
    - Remove any arguments passed to `$this->respondNoContent(...)`
    - Update any `JsonResponse` return type hints to `Symfony\Component\HttpFoundation\Response`
- No further changes are required, the API remains the same
  
## From 2.x to 3.x

- Run the following command to fetch the latest package version: `composer require f9webltd/laravel-api-response-helpers:^3.0`
- The package now requires PHP `^8.2` and Laravel `^11.0` / `^12.0` or `^13.0`. Going forwards this package will actively track supported Laravel / PHP versions as per [Laravel's official support policy](https://laravel.com/docs/master/releases#support-policy)
- No further changes are required, the API remains the same

## From 1.x to 2.x

- Run the following command to fetch the latest package version: `composer require f9webltd/laravel-api-response-helpers:^2.0`
- The package now requires PHP `^8.0` and Laravel `^8.12` / `^9.0`, `^10.0` or `^11.0`
- No further changes are required, the API remains the same
