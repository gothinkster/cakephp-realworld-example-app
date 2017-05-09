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

class InitialSchema extends AbstractMigration
{

    /**
     * Migration change method.
     *
     * @return void
     */
    public function change()
    {
        $this->table('articles', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'null' => true,
            ])
            ->addColumn('body', 'text', [
                'null' => true,
            ])
            ->addColumn('author_id', 'uuid', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('favorites_count', 'integer', [
                'default' => 0,
                'null' => false,
                'limit' => 11,
            ])
            ->addColumn('tag_count', 'integer', [
                'default' => 0,
                'null' => false,
                'limit' => 11,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['author_id'])
            ->addIndex(['slug'], [
                'unique' => true,
            ])
            ->create();

        $this->table('comments', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('body', 'text', [
                'null' => true,
            ])
            ->addColumn('article_id', 'uuid', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('author_id', 'uuid', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['article_id'])
            ->create();

        $this->table('follows', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('followable_id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('follower_id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('blocked', 'boolean', [
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['followable_id'])
            ->addIndex(['follower_id', 'followable_id'])
            ->create();

        $this->table('favorites', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('user_id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('article_id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['article_id', 'user_id'])
            ->addIndex(['user_id'])
            ->create();
    }
}
// @codingStandardsIgnoreEnd
