Laravel Admin Panel generator
=============

### Install in Laravel 5.3

1. Config.
Replace into `config/database.php`:
`'strict' => true`
to
`'strict' => false`

(https://github.com/laravel/framework/issues/14997#issuecomment-242129087)



2. Add to `config/auth.php`:
    ```php
    'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ```
        
    ```php
    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => Wbe\Crud\Models\ContentTypes\User::class,
        ],
    ```
        
    ```php
    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ```
        
3. Add to config/app.php:
```php
'providers' => [
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        Laravel\Socialite\SocialiteServiceProvider::class,
        //Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        Zofe\Rapyd\RapydServiceProvider::class,
        //Laravel\Cashier\CashierServiceProvider::class,
        Barryvdh\Elfinder\ElfinderServiceProvider::class,
        
        Wbe\Crud\Models\hrbac\HierarchicalRBAC\HRBACServiceProvider::class,
        Wbe\Crud\CrudServiceProvider::class
```

```php
'aliases' => [
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,
        //'DataEdit' => Zofe\Rapyd\DataEdit\DataEdit::class,
```

4. Add helper.php to composer autoload section:
```json
  "files": [
    "vendor/wbe/crud/src/helpers.php"
  ]
```


5. Add to app\Http\Kernel.php:
```php
protected $middleware = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Wbe\Crud\Middleware\Locale::class,
        \Wbe\Crud\Middleware\LangMiddleware::class,
        \Wbe\Crud\Middleware\AdminAccessMiddleware::class,
```

```php
protected $routeMiddleware = [
        'customer' => \Wbe\Crud\Middleware\RedirectIfNotCustomer::class,
        'customer.guest' => \Wbe\Crud\Middleware\RedirectIfCustomer::class,
        'admin' => \Wbe\Crud\Middleware\RedirectIfNotAdmin::class,
        'admin.guest' => \Wbe\Crud\Middleware\RedirectIfAdmin::class,
```

```php
 protected $middlewareGroups = [
         'admin'=>[
                     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                 ],
```
     
6. Run into console:
```ssh
php artisan vendor:publish --provider="Zofe\Rapyd\RapydServiceProvider"
php artisan vendor:publish --provider="Wbe\Crud\CrudServiceProvider"
php artisan elfinder:publish
php artisan migrate
php artisan db:seed --class=\Wbe\Crud\seeds\DatabaseSeeder
composer dump-autoload
mkdir public/files/
```
Migrations will create tables and fill it with default data.
It also contains data insertions into database (like seeding). 

---------

Finally, admin url:

http://yourproject.dev/admin/

Login: admin@gmail.com

Pass: 111111