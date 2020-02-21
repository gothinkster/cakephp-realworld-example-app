<?php
/**
 * Copyright 2016 - 2017, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2016 - 2017, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Service\Renderer;

use CakeDC\Api\Exception\ValidationException;
use CakeDC\Api\Service\Action\Result;
use CakeDC\Api\Service\Renderer\JsonRenderer;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Exception;

/**
 * Class JsonRenderer
 * JSON content negotiation Renderer.
 *
 * @package CakeDC\Api\Service\Renderer
 */
class AppJsonRenderer extends JsonRenderer
{

    /**
     * Builds the HTTP response.
     *
     * @param Result $result The result object returned by the Service.
     * @return bool
     */
    public function response(Result $result = null)
    {
        $response = $this->_service->getResponse();
        $data = $result->getData();
        $this->_service->setResponse($response->withStringBody($this->_encode($data))->withStatus($result->getCode())
            ->withType('application/json'));

        return true;
    }

    /**
     * Processes an exception thrown while processing the request.
     *
     * @param Exception $exception The exception object.
     * @return void
     */
    public function error(Exception $exception)
    {
        $response = $this->_service->getResponse();
        $data = [
            'error' => [
                'code' => $exception->getCode(),
                'message' => $this->_buildMessage($exception)
            ]
        ];
        if ($exception instanceof ValidationException) {
            $data['errors'] = [];
            unset($data['error']);
            foreach ($exception->getValidationErrors() as $field => $errors) {
                if (is_array($errors)) {
                    $data['errors'][$field] = array_values($errors);
                } else {
                    $data['errors'][$field] = [$errors];
                }
            }
        }
        $this->_service->setResponse($response
            ->withStringBody($this->_encode($data))
            ->withType('application/json')
            ->withStatus($exception->getCode())
        );
    }
}
