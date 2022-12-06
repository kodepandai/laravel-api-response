# Changelog

All notable changes to `laravel-api-response` will be documented in this file.

## [Unreleased](https://github.com/kodepandai/laravel-api-response/compare/v1.2.0...main) - TBD

### Fixed

- Return 404 `NOT_FOUND` for laravel `ModelNotFoundException`

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
