# Laravel Api Response

Pustaka laravel untuk standarisasi struktur Api Response.

## Instalasi
<!-- TODO -->
## Penggunaan

### Respon api sukses

```php
ApiResponse::success(['version' => 'v1.0.0']);
ApiResponse::success(['name' => 'Taylor Otwell'])->title('User')->message('User Data');
ApiResponse::success()->status(201)->message('Sucessfully create a user.');
```

### Respon api gagal

```php
ApiResponse::error(['email' => ['The email is required.']])->status(422);
ApiResponse::error()->title('Failed')->message('Cannot delete user data.');
ApiResponse::error()->status(400)->message('Please check your request.');
```

### Validasi request

Digunakan untuk memvalidasi inputan, jika data tidak valid maka akan
melempar `ApiValidationException`.

```php
ApiResponse::validateOrFail([
    'email' => 'required|email',
    'username' => 'required|unique:users,username',
]);
```

### Lempar Exception

Anda bisa langsung melempar exception `ApiException` atau `ApiValidationException`
untuk memberikan response `ApiResponse::error`.

```php
if (! DB::hasDriver('mysql')) {
  throw new ApiException('Mysql driver not found!', Response::HTTP_INTERNAL_SERVER_ERROR);
}

if ($user->balance <= 100_000) {
  throw new ApiValidationException(['balance' => ['Balance must be greater than 100K']]);
}
```

## Pengembang

* Untuk tes jalankan `composer test`
