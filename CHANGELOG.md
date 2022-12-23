# Changelog

All notable changes to `laravel-api-response` will be documented in this file.

## [Unreleased](https://github.com/kodepandai/laravel-api-response/compare/v1.3.1...main) - TBD

## [v1.3.1](https://github.com/kodepandai/laravel-api-response/compare/v1.3.0...v1.3.1) - 23 Dec 2022

### Fixed

- Exception Handler fails to convert ApiResponse to JsonResponse ([#16](https://github.com/kodepandai/laravel-api-response/pull/16))

## [v1.3.0](https://github.com/kodepandai/laravel-api-response/compare/v1.2.0...v1.3.0) - 14 Dec 2022

### Fixed

- Laravel exception handler does not recognize Responsable trait ([#11](https://github.com/kodepandai/laravel-api-response/pull/11))
- Return 404 `NOT_FOUND` for laravel `ModelNotFoundException` ([#10](https://github.com/kodepandai/laravel-api-response/pull/10))

### Added

- Support `Arrayable` interface for response data ([#13](https://github.com/kodepandai/laravel-api-response/pull/13))

## [v1.2.0](https://github.com/kodepandai/laravel-api-response/compare/v1.1.0...v1.2.0) - 24 Jul 2022

### Fixed

- Add try/catch handler when parsing exception to response
- Return 401 `HTTP_UNAUTHORIZED` for laravel `AuthenticationException`
- Get data directly from `ResourceCollection` data type
- Add more tests to improve coverage

## [v1.1.0](https://github.com/kodepandai/laravel-api-response/compare/v1.0.0...v1.1.0) - 28 Jun 2022

### Fixed

- Missing return type for `ApiException` ([#4](https://github.com/kodepandai/laravel-api-response/pull/4))

### Added

- Return type for `validateOrFail`
- `ExceptionHandler::renderAsApiResponse` helper

## [v1.0.0](https://github.com/kodepandai/laravel-api-response/compare/b5f439...v1.0.0) - 13 Jun 2022

### Added

- ApiResponse `ApiResponse::success` and `ApiResponse::error`
- Exception `ApiException` and `ApiValidationException`
