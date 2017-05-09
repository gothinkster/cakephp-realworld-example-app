<?php

namespace App\Model\Factory;

use CakephpFactoryMuffin\Model\Factory\AbstractFactory;
use League\FactoryMuffin\Faker\Facade as Faker;

class UsersFactory extends AbstractFactory
{

    /**
     * Returns factory definition.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => Faker::firstName(),
            'last_name' => Faker::lastName(),
            'username' => Faker::unique()->firstName(),
            'active' => true,
            'password' => 'passwd',
            'email' => Faker::unique()->safeEmail(),
            'bio' => Faker::sentence(),
            'image' => Faker::imageUrl(100, 100, 'people'),
        ];
    }
}
