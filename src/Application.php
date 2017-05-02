<?php
/**
 * Copyright 2017, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2017, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace App;

use App\PasswordHasher\PlainPasswordHasher;
use Authentication\AuthenticationService;
use Authentication\Middleware\AuthenticationMiddleware;
use CakeDC\Api\Middleware\ApiMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use CakeDC\Api\Middleware\RequestHandlerMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * Setup the middleware your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware.
     */
    public function middleware($middleware)
    {
        $service = new AuthenticationService();

        $service->loadIdentifier('Authentication.JwtSubject', [
            'dataField' => 'id',
        ]);
        $service->loadAuthenticator('Authentication.Jwt', [
            'tokenPrefix' => 'Token',
            'returnPayload' => false,
        ]);

        $service->loadAuthenticator('AppForm', [
            'baseModel' => 'user',
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
        ]);
        $service->loadIdentifier('Authentication.Orm', [
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
            'passwordHasher' => PlainPasswordHasher::class
        ]);

        // Add it to the authentication middleware
        $authentication = new AuthenticationMiddleware($service);

        $middleware
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(ErrorHandlerMiddleware::class)

            ->add(new RequestHandlerMiddleware())
            ->add($authentication)
            ->add(new ApiMiddleware())

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)

            // Apply routing
            ->add(RoutingMiddleware::class);

        return $middleware;
    }
}
