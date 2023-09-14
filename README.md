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

Finally, you just need to alias the middleware in your `$middlewareAliases` in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'polybind' => Kayrunm\Polybind\Polybind::class
];
```

You can skip aliasing the middleware if you have no intention of using [per-route parameters](#per-route-configuration),
as it just makes it easier to add the middleware parameters. If you do skip this step, you'll have to apply the 
middleware using the full Polybind classname.

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

### Type validation

Polybind allows you to hint the types of model that a route accepts, either with union/intersection types or even with
interfaces. If Polybind resolves a model that doesn't match the type that you have type hinted, it will throw a
`Kayrum\Polybind\Exceptions\InvalidModelType` exception. If you don't use any type hinting in your controller method,
Polybind will allow any Model to be resolved.

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

### Per-route configuration

Polybind also allows you to configure the route parameters for the model type and model identifier, as well as the name
you use for the parameter in your controller method, on a per-route basis. Here's an example of how to do that:

```php
// routes/web.php

Route::get('/{author_type}/{author_uuid}', function ($author) {
    return response()->json($author);
})->middleware('polybind:author_type,author_uuid,author');
```

### Adding to your entire application

If you make use of polymorphic route-model binding throughout your application, you may find it easier to simply apply
Polybind's functionality on all of your routes. Polybind will only run on routes where it finds a matching type _and_
identifier parameter.

To do this, simply add the Polybind middleware to the middleware groups you would like it to run on, for example in the
`web` and `api` groups in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Kayrunm\Polybind\Polybind::class,    
    ],
    
    'api' => [
        // ...
        \Kayrunm\Polybind\Polybind::class,    
    ],
];
```

**Note:** Make sure that the Polybind middleware is applied _after_ the `SubstituteBindings` middleware.
