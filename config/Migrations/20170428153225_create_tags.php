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
// @codingStandardsIgnoreStart

use Phinx\Migration\AbstractMigration;

class CreateTags extends AbstractMigration
{

    /**
     * Migration change method.
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('tags_tags', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', [
            'null' => false,
        ]);
        $table->addColumn('namespace', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('slug', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('label', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('counter', 'integer', [
            'default' => 0,
            'length' => 11,
            'null' => false,
            'signed' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();

        $table = $this->table('tags_tagged', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', [
            'null' => false,
        ]);
        $table->addColumn('tag_id', 'uuid', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('fk_id', 'uuid', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('fk_table', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->create();

        $table = $this->table('tags_tags');
        $table->addColumn('tag_key', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addIndex(['tag_key', 'label', 'namespace'], ['unique' => true]);
        $table->update();
        $table = $this->table('tags_tagged');
        $table->addIndex(['tag_id', 'fk_id', 'fk_table'], ['unique' => true]);
        $table->update();
    }
}
// @codingStandardsIgnoreEnd
