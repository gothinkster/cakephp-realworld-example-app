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

namespace App\Authentication\Authenticator;

use Authentication\Authenticator\FormAuthenticator;
use Authentication\Authenticator\Result;
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
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @param \Psr\Http\Message\ResponseInterface $response Unused response object.
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!$this->_checkUrl($request)) {
            $errors = [
                sprintf(
                    'Login URL %s did not match %s',
                    $request->getUri()->getPath(),
                    $this->getConfig('loginUrl')
                )
            ];

            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, $errors);
        }

        $data = $this->_getData($request);
        if ($data === null) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING, [
                'Login credentials not found',
            ]);
        }

        $user = $this->getIdentifier()->identify($data);

        if (empty($user)) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING, $this->getIdentifier()->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request that contains login information.
     * @return array|null Username and password retrieved from a request body.
     */
    protected function _getData(ServerRequestInterface $request)
    {
        $fields = $this->_config['fields'];
        $body = Hash::get($request->getParsedBody(), $this->getConfig('baseModel'));

        $data = [];
        foreach ($fields as $key => $field) {
            if (!isset($body[$field])) {
                return null;
            }

            $value = $body[$field];
            if (!is_string($value) || !strlen($value)) {
                return null;
            }

            $data[$key] = $value;
        }

        return $data;
    }

}
