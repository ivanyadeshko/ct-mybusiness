<?php

use yii\db\Migration;

/**
 * Class m201024_163907_apple
 */
class m201024_163907_apple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(32)->notNull(),
            'size' => $this->decimal(3,2)->notNull(),
            'fall_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }

}
