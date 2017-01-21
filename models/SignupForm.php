<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 14.01.2016
 * Time: 15:56
 */

namespace app\models;

use yii\base\Model;
use Yii;

/**
 * SignupForm is the model behind the signup form.
 */

class SignupForm  extends Model
{
    //data AbstractUser
    public $username;
    public $email;
    public $password;
    //data Professor and Student
    public $surname;
    public $name;
    //data Student
    public $date_reg;
    // Data define the role of the user
    public $user;
    public $you;

    public $status;

    public function rules()
    {
        return [
            ['you', 'required'],
            ['you', 'string'],

            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => AbstractUser::className(), 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['status', 'default', 'value' => AbstractUser::STATUS_ACTIVE],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::className(), 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['name', 'required'],
            ['name', 'string'],

            ['surname', 'required'],
            ['surname', 'string'],

            ['date_reg', 'string'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        $this->date_reg =  date("Y-m-d H:i:s");

        $user = new AbstractUser();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->type = $this->you;
        $user->email = $this->email;
        $user->surname = $this->surname;
        $user->name = $this->name;
        $user->date_regSt = $this->date_reg;

        if ($this->validate())
            return $user->insertData()? $user :  print_r($user->getErrors());
        else
            return NULL;
    }
}