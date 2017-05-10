<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

// @codingStandardsIgnoreStart
class TagsFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var array
     */
    public $table = 'tags_tags';

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
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

    /**
     * Records
     *
     * @var array
     */
    public $records = [];
}
// @codingStandardsIgnoreEnd
