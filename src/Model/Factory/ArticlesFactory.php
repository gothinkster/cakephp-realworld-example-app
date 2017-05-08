<?php

namespace App\Model\Factory;

use Cake\ORM\TableRegistry;
use CakephpFactoryMuffin\Model\Factory\AbstractFactory;
use League\FactoryMuffin\Faker\Facade as Faker;

class ArticlesFactory extends AbstractFactory {

    public function definition()
    {
        return [
            '_recreate' => true,
            'title' => Faker::sentence(),
            'description_length' => Faker::numberBetween(3, 7),
            'description' => function ($item) {
                return Faker::sentence($item['description_length'])();
            },
			'body_length' => Faker::numberBetween(2, 5),
			'body' => function ($item) {
                return Faker::paragraphs($item['body_length'], true)();
            },
            'tagList' => function ($item) {
                $tags = TableRegistry::get('Tags')->find()->select(['label'])
                    ->all()
                    ->shuffle()
                    ->take(5)
                    ->map(function ($i) {
                        return $i['label'];
                    })
                    ->reduce(function ($accum, $item) {
                        return ($accum ? $item . ' ' . $accum : $item);
                    });
                return $tags;
            }
        ];
    }
}
