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
            'title' => $this->char(['length' => 100])->notNull()->unique(),
        ], $tableOptions);

        $this->createTable('{{%pages}}', [
            'id'        => $this->primaryKey(['length' => 10])->unsigned(),
            'folder_id' => $this->integer(['length' => 10])->unsigned()->notNull(),
            'title'     => $this->char(['length' => 100])->notNull()->unique(),
            'state'     => $this->char(['length' => 10])->notNull()->defaultValue('empty'),
        ], $tableOptions);

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

    private function populateTables() {
        $this->insert('{{%folders}}',['title' => 'docs']);
        $this->insert('{{%folders}}',['title' => 'api']);
        $this->insert('{{%folders}}',['title' => 'articles']);
        $this->insert('{{%folders}}',['title' => 'fruits']);
        $this->insert('{{%folders}}',['title' => 'books']);
        $this->insert('{{%folders}}',['title' => 'gadgets']);
        $this->insert('{{%folders}}',['title' => 'utils']);
        $this->insert('{{%folders}}',['title' => 'downloads']);
        $this->insert('{{%folders}}',['title' => 'members']);
        $this->insert('{{%folders}}',['title' => 'authors']);

        $this->insert('{{%folders}}',['title' => 'dogs']);
        $this->insert('{{%folders}}',['title' => 'cats']);
        $this->insert('{{%folders}}',['title' => 'birds']);
        $this->insert('{{%folders}}',['title' => 'fishes']);
        $this->insert('{{%folders}}',['title' => 'crocodiles']);
        $this->insert('{{%folders}}',['title' => 'dinosaurs']);
        $this->insert('{{%folders}}',['title' => 'nature']);
        $this->insert('{{%folders}}',['title' => 'houses']);
        $this->insert('{{%folders}}',['title' => 'apartments']);
        $this->insert('{{%folders}}',['title' => 'girls']);

        $this->insert('{{%folders}}',['title' => 'films']);
        $this->insert('{{%folders}}',['title' => 'tapes']);
        $this->insert('{{%folders}}',['title' => 'files']);
        $this->insert('{{%folders}}',['title' => 'drugs']);
        $this->insert('{{%folders}}',['title' => 'politics']);
        $this->insert('{{%folders}}',['title' => 'blogs']);
        $this->insert('{{%folders}}',['title' => 'cars']);
        $this->insert('{{%folders}}',['title' => 'wives']);
        $this->insert('{{%folders}}',['title' => 'artists']);
        $this->insert('{{%folders}}',['title' => 'vacancies']);
    }
}
