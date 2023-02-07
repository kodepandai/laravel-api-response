# Laravel API Response (Next version)

> This version is still WORK IN PROGRESS.

This package aims to help you standardize all your API responses in
a simple and structured way.

By default, the structure of the API response will look like this:

```jsonc
{
  "success": true, // it was successfull or not
  "title": "Users", // the title/headline/section 
  "message": "Active users only", // the message/description/hightlight
  "data": [ // if it was successfull
    // profile..
    // users..
    // products..
    // etc..
  ],
  "errors": [ // if it was not successfull
    // validation errors..
    // any other errors..
  ]
}
```

## Install

```sh
$ composer require kodepandai/laravel-api-response:^2
```

## Usage

TODO: complete this documentation, simplify the wordings.

## Develop

- To test run `composer test`.
