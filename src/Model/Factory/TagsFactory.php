<?php

namespace App\Model\Factory;

use CakephpFactoryMuffin\Model\Factory\AbstractFactory;
use League\FactoryMuffin\Faker\Facade as Faker;

class TagsFactory extends AbstractFactory
{

    /**
     * Returns factory definition.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'label' => Faker::unique()->word(),
            'tag_key' => function ($item) {
                return $item['label'];
            }
        ];
    }
}
