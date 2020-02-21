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

use App\Authentication\Authenticator\AppFormAuthenticator;
use App\Authentication\Identifier\PasswordIdentifier;
use Authentication\AuthenticationService;
use Authentication\Authenticator\FormAuthenticator;
use Authentication\Middleware\AuthenticationMiddleware;
use CakeDC\Api\Middleware\ApiMiddleware;
use CakeDC\Api\Middleware\RequestHandlerMiddleware;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Core\Configure;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{

    public function bootstrap()
    {
        parent::bootstrap();
        if (PHP_SAPI === 'cli') {
            try {
                $this->addPlugin('Bake');
            } catch (MissingPluginException $e) {
                // Do not halt if the plugin is missing
            }
        }

        $this->addPlugin('Migrations');


        $this->addPlugin('Muffin/Slug');
        $this->addPlugin('Muffin/Tags');

        if (Configure::read('debug')) {
            $this->addPlugin('DebugKit', ['bootstrap' => true]);
        }

        $this->addPlugin('CakeDC/Api', ['bootstrap' => false, 'routes' => true]);
    }

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
            'dataField' => 'sub',
        ]);
        $service->loadAuthenticator('Authentication.Jwt', [
            'tokenPrefix' => 'Token',
            'returnPayload' => false,
        ]);

        $service->loadAuthenticator(AppFormAuthenticator::class, [
            'baseModel' => 'user',
            'fields' => [
                'username' => 'email',
                'password' => 'password'
            ],
        ]);
        $service->loadIdentifier(\Authentication\Identifier\PasswordIdentifier::class, [
            'fields' => [
                'username' => 'email',
                'password' => 'password',
            ],
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
