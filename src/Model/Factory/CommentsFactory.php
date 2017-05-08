<?php

namespace App\Model\Factory;

use Cake\ORM\TableRegistry;
use CakephpFactoryMuffin\Model\Factory\AbstractFactory;
use League\FactoryMuffin\Faker\Facade as Faker;

class CommentsFactory extends AbstractFactory {

    public function definition()
    {
        return [
			'body_length' => Faker::numberBetween(2, 5),
			'body' => function ($item) {
                $paragraphs = Faker::paragraphs($item['body_length'], true);
                return $paragraphs();
            },
            'author_id' => function ($item) {
                $users = TableRegistry::get('Users')->find()->select(['id'])->all()->toArray();
                return $users[rand(0, count($users) - 1)]->id;
            }
        ];
    }
}
