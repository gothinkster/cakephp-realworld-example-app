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

namespace App\Authenticator;

use Authentication\Authenticator\FormAuthenticator;
use Authentication\Result;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Form Authenticator
 *
 * Authenticates an identity based on the POST data of the request.
 */
class AppFormAuthenticator extends FormAuthenticator
{

    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @param array $fields The fields to be checked.
     * @return bool False if the fields have not been supplied. True if they exist.
     */
    protected function _checkBody(ServerRequestInterface $request, array $fields)
    {
		$body = Hash::get($request->getParsedBody(), $this->getConfig('baseModel'));

        foreach ([$fields['username'], $fields['password']] as $field) {
            if (!isset($body[$field])) {
                return false;
            }

            $value = $body[$field];
            if (empty($value) || !is_string($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @param \Psr\Http\Message\ResponseInterface $response Unused response object.
     * @return \Authentication\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!$this->_checkLoginUrl($request)) {
            $errors = [
                sprintf(
                    'Login URL %s did not match %s',
                    $request->getUri()->getPath(),
                    $this->getConfig('loginUrl')
                )
            ];

            return new Result(null, Result::FAILURE_OTHER, $errors);
        }

        $fields = $this->_config['fields'];
        if (!$this->_checkBody($request, $fields)) {
            return new Result(null, Result::FAILURE_CREDENTIALS_NOT_FOUND, [
                'Login credentials not found'
            ]);
        }

		$body = Hash::get($request->getParsedBody(), $this->getConfig('baseModel'));
        $user = $this->identifiers()->identify($body);

        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->identifiers()->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }
}
