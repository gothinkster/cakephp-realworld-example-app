<?php

namespace App\Model\Factory;

use CakephpFactoryMuffin\Model\Factory\AbstractFactory;
use Cake\ORM\TableRegistry;
use League\FactoryMuffin\Faker\Facade as Faker;

class CommentsFactory extends AbstractFactory
{

    /**
     * Returns factory definition.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body_length' => Faker::numberBetween(2, 5),
            'body' => function ($item) {
                $paragraphs = Faker::paragraphs($item['body_length'], true);

                return $paragraphs();
            },
            'created' => Faker::dateTimeBetween('-2 year', 'now'),
            'modified' => Faker::dateTimeBetween('-2 year', 'now'),
//            'author_id' => function ($item) {
//                $users = TableRegistry::get('Users')->find()->select(['id'])->all()->toArray();
//
//                return $users[rand(0, count($users) - 1)]->id;
//            }
            'author_id' => 'factory|' . UsersFactory::class
        ];
    }
}
