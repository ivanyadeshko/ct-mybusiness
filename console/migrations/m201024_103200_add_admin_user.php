<?php

use common\models\User;
use yii\db\Migration;
use yii\helpers\BaseConsole;

/**
 * Class m201024_103200_add_admin_user
 */
class m201024_103200_add_admin_user extends Migration
{

    protected $username = 'admin';
    protected $email = 'admin@code.test';
    protected $password = '123';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $username = BaseConsole::input("Username [$this->username]: ");
        $username = empty($username) ? $this->username : $username;

        $password = BaseConsole::input("Password [$this->password]: ");
        $password = empty($password) ? $this->password : $password;

        $email = BaseConsole::input("Email [$this->email]: ");
        $email = empty($email) ? $this->email : $email;

        $user = new User([
            'username' => $username,
            'email' => $email,
            'role' => USER::ROLE_ADMIN,
            'status' => USER::STATUS_ACTIVE,
        ]);
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        return $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(User::tableName(), ['role' => USER::ROLE_ADMIN]);
    }
}
