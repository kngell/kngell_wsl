<?php

declare(strict_types=1);

final class AppHelper
{
    public static function singleton(Application $app)
    {
        return [
                'RooterFactory'=>fn () => new RooterFactory($app->make(Rooter::class)),
                'ProductsManager'=>fn () => new ProductsManager(),
                'CartManager' =>fn () =>  new CartManager(),
                'Token' =>fn () =>   new Token(),
                'Request'=>fn () => new Request($app->make(Sanitizer::class)),
                'Response'=>fn () => new Response(),
                'MoneyManager' => fn () => new MoneyManager(),
                'PaymentGateway'=>fn () => new PaymentGateway(),
                'ProductsManager'=>fn () => new ProductsManager(),
                'SlidersManager' =>fn () => new SlidersManager(),
                'GeneralSettingsManager'=>fn () =>   new GeneralSettingsManager(),
                'CartManager' =>fn () => new CartManager(),
                'ImageManager'=>fn () => new ImageManager(),
                'View'=>fn () => new View(),
                'Files' =>fn () =>   new Files(),
                'Cors'=>fn () =>   new Cors(),
                'ControllerHelper'=>fn () => new ControllerHelper(),
                'Requirevalidator'=>fn () => new Requirevalidator(),
                'Minvalidator'=>fn () => new Minvalidator(),
                'Maxvalidator'=>fn () => new Maxvalidator(),
                'ValidEmailvalidator'=>fn () => new ValidEmailvalidator(),
                'Numericvalidator'=>fn () => new Numericvalidator(),
                'MatchesValidator'=>fn () => new MatchesValidator(),
                'UniqueValidator'=>fn () => new UniqueValidator(),
                'SessionStorageInterface'=> fn () => new NativeSessionStorage(),
                'SessionInterface'=> fn () => new Session($app->make(SessionStorageInterface::class)),
                'UsersRequestsManager'=> fn () => new UsersRequestsManager(),
                'UserSessionsManager'=> fn () => new UserSessionsManager(),
                'AuthManager'=> fn () => new AuthManager(),
                'UploadHelper'=>fn () =>new UploadHelper($app->make(ImageManager::class)),
                'PostFileUrlManager'=>fn () =>new PostFileUrlManager(),
            ];
    }
}