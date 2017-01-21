<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.03.2016
 * Time: 22:00
 */

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;

/**
 * This is the model class for users
 **/

class AbstractUser extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = 0;///!!!!!1!
    const STATUS_BLOCKED = 1;
    const STATUS_WAIT = 2;

    public $surname;
    public $name;
    public $date_regSt;

    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => self::className(), 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 60],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(), 'message' => 'This email address has already been taken.'],
            ['email', 'string', 'max' => 255],

            ['name', 'required'],
            ['name', 'name'],
            ['name', 'string', 'max' => 100],

            ['surname', 'required'],
            ['surname', 'surname'],
            ['surname', 'string', 'max' => 100],

            ['date_regSt', 'string'],

        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['idUsers' => $id]);
    }

    public function attributeLabels()
    {
        return [
            'idUser' => 'id',
            'username' => 'username',
            'name'=>'name',
            'surname'=>'surname',
            'date_regSt' => 'date_regSt',

        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     *  identity class
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->one();
    }

    /**
     * Finds user by primary key
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }


    public function validatePassword($password, $username)
    {
        $hash = (new \yii\db\Query())
            ->from('users')
            ->where('username=:username', [':username' => $username])
            ->one();

        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash['password'])){
            return static::findOne(['username'=>$username, 'password'=>$password]);
        }
        else
            return Yii::$app->security->validatePassword($password, $hash['password']);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    //insert new user by his type
    public  function insertDataOnType($table, $surname, $name, $type, $date = null)
    {
        $db1 = Yii::$app->db->createCommand();

        if (is_null($date) && $table === 'professor')
            $db1->insert($table , [
            'surname' => $surname,
            'name' => $name,
            'type_user' => $type,
            ])->execute();
        else
            $db1->insert($table, [
                'surname' =>$surname,
                'name' => $name,
                'type_user' => $type,
                'FK_Group' => 1,
                'registration_date' => $date,
                'status' => 'new',
            ])->execute();
        return true;
    }

    //insert data on parent table for users
    public function insertData()
    {
        $db = Yii::$app->db->createCommand();
        //insert to Users
        $result =  $db->insert('users', [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'type' => $this->type,
        ])->execute();
        //insert by type Professor or Student
        if ($result != null)
        {
            $hash = (new \yii\db\Query())
                ->select('idUsers')
                ->from('users')
                ->where('username=:username', [':username' => $this->username])
                ->one();
            if ($this->type == 1)
            {
                $this->insertDataOnType($table = 'professor', $this->surname, $this->name, $hash['idUsers']);
                return true;
            }
            if($this->type == 2)
            {
                $this->insertDataOnType($table ='student', $this->surname, $this->name, $hash['idUsers'], $this->date_regSt);
                return true;
            }
        }
    }

    public function  changePass()
    {
        $db = Yii::$app->db->createCommand();
        $db->update('users', [
            'password' => $this->password,

        ], 'idUsers=:id', [':id' => $this->id])->execute();
        return true;
    }

    public function updateData()
    {
        $dbUser = Yii::$app->db->createCommand();
        if($this->password !== null)
            $this->changePass();
        $dbUser->update('users', [
            'username' => $this->username,
            'email' => $this->email,
        ], 'idUsers=:id', [':id' => $this->id])->execute();
        return true;
    }
}