<?php

use yii\db\Migration;

/**
 * Class m201024_100000_add_user_role
 */
class m201024_100000_add_user_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(\common\models\User::tableName(), 'role', $this->string(16)->notNull()->after('email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(\common\models\User::tableName(), 'role');
    }
}
