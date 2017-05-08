<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;


class TagsFixture extends TestFixture
{
    public $table = 'tags_tags';

    public $fields = [
        'id' => ['type' => 'integer'],
        'namespace' => ['type' => 'string', 'length' => 255, 'null' => true],
        'tag_key' => ['type' => 'string', 'length' => 255],
        'slug' => ['type' => 'string', 'length' => 255],
        'label' => ['type' => 'string', 'length' => 255],
        'counter' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'null' => true],
        'created' => ['type' => 'datetime', 'null' => true],
        'modified' => ['type' => 'datetime', 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [];
}
