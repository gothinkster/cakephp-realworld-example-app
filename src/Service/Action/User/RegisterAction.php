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

namespace App\Service\Action\User;

use CakeDC\Api\Exception\ValidationException;
use CakeDC\Api\Service\Action\CrudAction;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class RegisterAction extends CrudAction
{

    /**
     * Action constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->Auth->allow($this->getName());
        $this->setTable(TableRegistry::getTableLocator()->get('Users'));
    }

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $validator = $this->getTable()->getValidator('register');
        $data = $this->getData();
        if (!array_key_exists('user', $data)) {
            throw new ValidationException(__('Validation failed'), 0, null, ['user root does not exists']);
        }
        $data = Hash::get($this->getData(), 'user');
        $errors = $validator->errors($data);
        if (!empty($errors)) {
            throw new ValidationException(__('Validation failed'), 0, null, $errors);
        }

        return true;
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $entity = $this->_newEntity();
        $data = Hash::get($this->getData(), 'user');
        $entity = $this->_patchEntity($entity, $data, [
            'validate' => 'register'
        ]);

        $record = $this->_save($entity);
        if ($record) {
            $user = $this->getTable()->loginFormat($record['id']);

            return ['user' => $user];
        }
    }

    /**
     * Returns action input params
     *
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();
        $data['user']['active'] = true;
        $data['user']['is_superuser'] = false;
        $data['user']['role'] = 'user';

        return $data;
    }
}
