<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;


/**
 * LoginForm is the model behind the login form.
 */

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $professor;
    public $you;
    static public $checkStudent;

    private $_user = false;

    /**
     * @return array the validation rules.
     * */
    public function rules()
    {
        return [
            ['you', 'integer'],
            [['username', 'password', 'you'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
            ['professor', 'boolean'],
        ];
    }

    /**
     * Validates the username and password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors())
        {
            $user = $this->getUser();
            \Yii::info('validate password','user');
            Yii::getLogger()->flush(true);
            if (!$user || (($user->findByUsername($this->username))===NULL))
            {
                \Yii::trace( "username was not founded");
                $this->addError('username', $this->username);
            }
            elseif (!$user || !$user->validatePassword($this->password, $this->username))
            {
                \Yii::trace( "password was not founded");
                $this->addError('password', $this->password);
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate())
        {
            \Yii::info('login in loginForm','user');
            \Yii::trace( "username - ".$this->username.";");
            Yii::getLogger()->flush(true);
            Yii::$app->session->set('isStudent', self::$checkStudent);
            Yii::$app->session->set('userPr', $this->getUser());
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->you == 1)
        {
            // Search for a model of common table
            $this->_user = AbstractUser::findByUsername($this->username);
            // Get the request for a user connection table
            $query =  new Query;
            $query -> select(['users.username AS login', 'student.name AS name', 'student.surname AS surname'])
                   ->from('access')
                   ->join('LEFT OUTER JOIN', 'users', 'users.type = access.idAccess')
                   ->join('LEFT OUTER JOIN', 'student', 'users.idUsers=student.type_user')
                   ->where(['users.username'=>$this->_user->username]);
            $command = $query->createCommand();
            $student = $command->queryOne();
            // Sending the model from the connection table
            Yii::$app->session->set('stud', $student );
            \Yii::info('user is Student','user');
            Student::$isStudent = true;
            // Additional check for the student
            self::$checkStudent = true;
            Yii::$app->session->set('AccessST',  self::$checkStudent);
            \Yii::trace( Student::$isStudent, "isStudent in LF Student");
            Yii::getLogger()->flush(true);
        }
        if ($this->you == 0)
        {
            \Yii::info('user is Prof','user');
            $this->_user = AbstractUser::findByUsername($this->username);
            Student::$isStudent = false;
            self::$checkStudent = false;
            Yii::$app->session->set('AccessST',  self::$checkStudent);
            $query =  new Query;
            $query -> select(['users.username AS login', 'users.password','professor.name AS name', 'professor.surname AS surname', 'professor.type_user' ])
                   ->from('access')
                   ->join('LEFT OUTER JOIN', 'users', 'users.type = access.idAccess')
                   ->join('LEFT OUTER JOIN', 'professor', 'users.idUsers=professor.type_user')
                   ->where(['users.username'=>$this->_user->username]);
            $command = $query->createCommand();
            $prof = $command->queryOne();
            \Yii::trace( Student::$isStudent, "isStudent in LF Prof");
            Yii::$app->session->set('prof', $prof );
            Yii::getLogger()->flush(true);
        }
        return $this->_user;
    }
}
