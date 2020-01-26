<?php

use yii\db\Migration;

/**
 * Class m200122_184048_file
 */
class m200122_184048_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('file', [
            'id'  => $this->primaryKey(),
            'model_name' => $this->string()->notNull(),
            'model_id' => $this->integer()->notNull(),
            'checksum' => $this->string(),
            'mime' => $this->string(),
            'name' => $this->string()->notNull(),
            'path' => $this->string()->notNull()
            ]
        );

        $this->createIndex(
            'idx_file_model',
            'file',
            ['model_name', 'model_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('file');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_184048_file cannot be reverted.\n";

        return false;
    }
    */
}
