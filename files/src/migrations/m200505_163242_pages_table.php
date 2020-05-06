<?php

use yii\db\Migration;

/**
 * Class m200505_163242_pages_table
 */
class m200505_163242_pages_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%folders}}', [
            'id'    => $this->primaryKey(['length' => 10])->unsigned(),
            'title' => $this->char(['length' => 100])->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx-folders-title',
            'folders',
            'title'
        );

        $this->createTable('{{%pages}}', [
            'id'        => $this->primaryKey(['length' => 10])->unsigned(),
            'folder_id' => $this->integer(['length' => 10])->unsigned()->notNull(),
            'title'     => $this->char(['length' => 100])->notNull(),
            'state'     => $this->char(['length' => 10])->notNull()->defaultValue('empty'),
        ], $tableOptions);

        $this->createIndex(
            'idx-pages-title',
            'pages',
            'title'
        );

        $this->createIndex(
            'idx-pages-folder_id',
            'pages',
            'folder_id'
        );

        $this->addForeignKey(
            'fk-pages-folder_id',
            'pages',
            'folder_id',
            'folders',
            'id',
            'CASCADE'
        );

        $this->populateTables();
    }

    public function down()
    {
        $this->dropTable('{{%pages}}');
        $this->dropTable('{{%folders}}');
    }

    private function populateTables()
    {
        $data = [
            'docs',
            'api',
            'articles',
            'fruits',
            'books',
            'gadgets',
            'utils',
            'downloads',
            'members',
            'authors',
            'dogs',
            'cats',
            'birds',
            'fishes',
            'crocodiles',
            'dinosaurs',
            'nature',
            'houses',
            'apartments',
            'girls',
            'films',
            'tapes',
            'files',
            'drugs',
            'politics',
            'blogs',
            'cars',
            'wives',
            'artists',
            'vacancies',
        ];
        foreach ($data as $title) {
            $this->insert('{{%folders}}', ['title' => $title]);
        }
    }
}
