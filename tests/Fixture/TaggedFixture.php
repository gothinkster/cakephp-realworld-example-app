<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TaggedFixture extends TestFixture
{
    public $table = 'tags_tagged';

    public $fields = [
        'id' => ['type' => 'integer'],
        'tag_id' => ['type' => 'integer', 'null' => false],
        'fk_id' => ['type' => 'integer', 'null' => false],
        'fk_table' => ['type' => 'string', 'limit' => 255, 'null' => false],
        'created' => ['type' => 'datetime', 'null' => true],
        'modified' => ['type' => 'datetime', 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [];
}
