<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img 
            src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" 
            width="250"
        >
    </a><br>    
</p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Installation and configuration

## Open a Terminal and run the following commands:  

    1) composer install
    
    2) composer require laravel/ui

    3) php artisan ui vue --auth

    4) php artisan migrate:fresh --seed

    5) php artisan passport:install

        5.1 Add the passport key to your **.env** file.
            
            PASSPORT_CLIENT_ID=2
            PASSPORT_CLIENT_SECRET=viF8y13ajlDeigxcZi7kUqQjazseDT7lvI3xasSg
        
        5.2 Add HasApiTokens to your user Model.

            use Laravel\Passport\HasApiTokens;
            use Illuminate\Notifications\Notifiable;
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Laravel\Passport\HasApiTokens;
            
            class User extends Authenticatable
            {
                use HasApiTokens, Notifiable;
            }
        
        5.3 Modify the file App\Providers\AuthServiceProvider. <br>

            Add the Laravel Passport facade -> use Laravel\Passport\Passport;
            Uncomment the line -> 'App\Model' => 'App\Policies\ModelPolicy' <br>
            Add the method routes() in function Boot() ->  Passport::routes(); below $this->registerPolicies();
        
        5.4 Add in the file config/auth.php the api section with the following options:

            'api' => [
                'driver' => 'passport',
                'provider' => 'users',
            ],

    6) Add darkaonline/l5-swagger running the following commands:

       composer require "darkaonline/l5-swagger"

       php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

    7) Enjoy!

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
