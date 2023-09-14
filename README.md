# Polybind

Polymorphic route-model binding for Laravel.

## Pre-requisites

This package requires the following:

* PHP 8.2
* Laravel 10 or higher

## Installation

To install the package, you simply need to run the following command:

```bash
composer require kayrunm/polybind
```

The service provider for the package will automatically be registered, but if you wish, you can optionally add
the following in your `config/app.php` file:

```php
    'providers' => [
        // ...
        Kayrunm\Polybind\PolybindServiceProvider::class,
    ],
```

## Configuration

This package allows for you to configure the default route parameters and the resolution logic. To start with this, you
need to publish the configuration file:

```bash
php artisan vendor:publish --provider="Kayrunm\Polybind\PolybindServiceProvider"
```

You can now edit the `config/polybind.php` file with your chosen defaults.

## Usage

Polybind works via a middleware that you add to your polymorphic routes. The simplest way to get started is to add the
middleware to your route definition, like so:
```php
// routes/web.php

Route::get('/{model_type}/{model_id}', [MyController::class, 'show'])->middleware('polybind');
```

Polybind will then do its magic when you access this route, by automatically resolving the model and allowing you to
access the model in your controller method via the `$model` parameter:
```php
// MyController.php

public function show($model)
{
    return response()->json($model);
}
```

**Note:** Polybind requires that your models are registered in `Relation::morphMap()`.

### Type hinting

Polybind allows you to hint the types of model that a route accepts, either with union/intersection types or even with
interfaces. If Polybind resolves a model that doesn't match the type that you have type hinted, it will throw a
`TBD` exception. If you don't use any type hinting in your controller method, Polybind will allow any Model to be
resolved.

Here's an example of type hinting with an interface:
```php
// MyController.php

public function show(HasAuthor $model)
{
    return response()->json($model);
}
```

And here's an example of type hinting using a union type:
```php
// MyController.php

public function show(Post|Comment $model)
{
    return response()->json($model);
}
```
