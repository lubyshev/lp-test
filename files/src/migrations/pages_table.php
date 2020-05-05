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

        $this->insert('{{%folders}}',['title' => 'folder_1']);
        $this->insert('{{%folders}}',['title' => 'folder_2']);
        $this->insert('{{%folders}}',['title' => 'folder_3']);
    }

    public function down()
    {
        $this->dropTable('{{%pages}}');
        $this->dropTable('{{%folders}}');
    }
}
