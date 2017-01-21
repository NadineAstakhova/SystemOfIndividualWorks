<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.03.2016
 * Time: 21:59
 */

namespace app\models;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table 'Professor'
 **/

class Professor extends AbstractUser implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_WAIT = 2;
    const SCENARIO_PROFILE = 'profile';

    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => self::className(), 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(), 'message' => 'This email address has already been taken.'],
            ['email', 'string', 'max' => 255],

            ['name', 'required'],
            ['name', 'name'],
            ['name', 'string', 'max' => 255],

            ['surname', 'required'],
            ['surname', 'surname'],
            ['surname', 'string', 'max' => 255],

            ['skype', 'required'],
            ['skype', 'skype'],
            ['skype', 'string', 'max' => 100],

            ['phone', 'required'],
            ['phone', 'phone'],
            ['phone', 'string', 'max' => 20],

            ['status', 'integer'],
            ['status', 'default', 'value' =>self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],

            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],

            ['student', 'required'],
            ['student', 'boolean'],
            ['professor', 'required'],
            ['professor', 'boolean'],

            [['filename', 'photo'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'username',
            'email' => 'email',
            'status' => 'status',
            'name'=>'name',
            'surname'=>'surname',
            'phone'=>'phone',
            'skype'=>'skype',
            'image'=>'image',
            'photo'=>'photo',
            'filename'=>'filename',
            'student' => 'student',
            'professor' => 'professor',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_PROFILE => ['username', 'email', 'surname', 'name', 'phone', 'skype', 'image', 'photo', 'filename'],
        ];
    }

    public static function tableName()
    {
        return '{{%professor}}';
    }

    public static function findByUsername($username)
    {
        $us = AbstractUser::findByUsername($username);
        return static::find()->where(['type_user' => $us['idUsers']])->one();
    }

    public static function findByUser($id)
    {
        $query =  new Query;
        $query -> select(['users.username AS login','users.email', 'users.password','professor.name AS name', 'professor.surname AS surname',
        'professor.phone', 'professor.skype', 'users.idUsers'])
            ->from('access')
            ->join('LEFT OUTER JOIN', 'users', 'users.type = access.idAccess')
            ->join('LEFT OUTER JOIN', 'professor', 'users.idUsers=professor.type_user')
            ->where(['users.idUsers'=>$id]);
        $command = $query->createCommand();
        $prof = $command->queryOne();
        return $prof;
    }

    public function getStatusName()
    {
        return ArrayHelper::getValue(self::getStatusesArray(), $this->status);
    }

    public static function getStatusesArray()
    {
        return [
            self::STATUS_BLOCKED => 'lock',
            self::STATUS_ACTIVE => 'active',
            self::STATUS_WAIT => 'new',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function getName($username)
    {
        return self::findByUser($username)['name'];
    }

    public static function getSurname($username)
    {
        return self::findByUser($username)['surname'];
    }

    public static function getPhone($username)
    {
        return self::findByUser($username)['phone'];
    }

    public static function getSkype($username)
    {
        return self::findByUser($username)['skype'];
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password, $username)
    {
        $hash = (new \yii\db\Query())
            ->from('professor')
            ->where('username=:username', [':username' => $username])
            ->one();
        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash['password']) ){
            return static::findOne(['username'=>$username, 'password'=>$password]);
        }
        else
            return Yii::$app->security->validatePassword($password, $hash['password']);
    }

    public function  changePass()
    {
        $db = Yii::$app->db->createCommand();
        $db->update('professor', [
            'password' => $this->password,
        ], 'id=:id', [':id' => $this->id])->execute();
        return true;
    }

    public function  insertData()
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('professor', [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'status' => $this->status,
            'surname' => $this->surname,
            'name' => $this->name,
        ])->execute();
        return true;
    }

    public function  updateData()
    {
        $dbProf = Yii::$app->db->createCommand();
        $dbProf->update('professor', [
                'name' => $this->name,
        ], 'id=:id', [':id' => $this->id])->execute();

        $dbProf->update('professor', [
                'surname' => $this->surname,
        ], 'id=:id', [':id' => $this->id])->execute();

        if($this->skype !== null)
            $dbProf->update('professor', [
                'skype' => $this->skype,
            ], 'id=:id', [':id' => $this->id])->execute();
        if($this->phone !== null)
            $dbProf->update('professor', [
                'phone' => $this->phone,
            ], 'id=:id', [':id' => $this->id])->execute();
        return true;
    }

    public function deleteImage($path,$filename)
    {
        $file =array();
        $file[] = $path.$filename;
        $file[] = $path.'sqr_'.$filename;
        $file[] = $path.'sm_'.$filename;
        foreach ($file as $f) {
            // check if file exists on server
            if (!empty($f) && file_exists($f)) {
                // delete file
                unlink($f);
            }
        }

    }

    public static  function getIdProfessor($user)
    {
        $hash = (new \yii\db\Query())
            ->select('idUsers')
            ->from('users')
            ->where('username=:username', [':username' => $user])
            ->one();

        $query = (new \yii\db\Query())
            ->select('id')
            ->from('professor')
            ->where('type_user=:user', [':user' => $hash['idUsers']])
            ->one();
        \Yii::trace(  $query, 'query Prof');
        return $query['id'];
    }

    public  static function selectSubject($name, $user)
    {
        $queryIdProf = self::getIdProfessor($user);
        $subQuery = (new \yii\db\Query())
            ->select('idSubject')
            ->from('subject')
            ->where('Name=:name', [':name' => $name])
            ->andWhere('FK_Professor=:prof', [':prof' =>$queryIdProf])
            ->one();
        return $subQuery;
    }

    //getting all professor subjects
    public static  function getAllSubject($user)
    {
        $queryIdProf = self::getIdProfessor($user);
        $query =  new Query;
        $query -> select(['subject.idSubject', 'subject.Name'])
               ->from('subject')
               ->join('LEFT OUTER JOIN', 'professor', 'subject.FK_Professor=professor.id')
               ->where(['professor.id'=>$queryIdProf]);
        $command = $query->createCommand();
        $subjects = $command->QueryAll();
        return $subjects;
    }

    //getting all new works for professor subjects
    public static function getAllNewWork($login)
    {
        $queryIdProf = self::getIdProfessor($login);
        $query =  new Query;
        $str = 'new';
        $query -> select(['individual_works.File', 'individual_works.idInd_work', 'subject.idSubject', 'subject.Name'])
               ->from('individual_works')
               ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
               ->join('LEFT OUTER JOIN', 'subject', 'list_of_task.FK_Subject = subject.idSubject')
               ->where(['individual_works.Status'=>$str])
               ->andWhere(['subject.FK_Professor'=>$queryIdProf]);
        $command = $query->createCommand();
        $newWorks = $command->QueryAll();
        return $newWorks;
    }
}