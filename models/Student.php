<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 19.03.2016
 * Time: 1:12
 */

namespace app\models;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "student".
**/

class Student extends AbstractUser
{
    const STATUS_ACTIVE = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_WAIT = 2;
    static public $isStudent;

    private static $Pr_Student;

    public $studentGroup;

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

            ['status', 'integer'],
            ['status', 'default', 'value' =>self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],

            ['student', 'required'],
            ['student', 'boolean'],
            ['professor', 'required'],
            ['professor', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idStudent' => 'id',
            'username' => 'username',
            'email' => 'email',
            'status' => 'status',
            'name'=>'name',
            'surname'=>'surname',
            'student' => 'student',
            'professor' => 'professor',
        ];
    }

    public static function tableName()
    {
        return '{{%student}}';
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

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password, $username)
    {

        $hash = (new \yii\db\Query())
            ->from('student')
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
        $db->update('student', [
            'password' => $this->password,
        ], 'idStudent=:id', [':id' => $this->id])->execute();
        return true;
    }

    public function  insertData()
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('student', [
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
        $db = Yii::$app->db->createCommand();
        $db->update('student', [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'surname' => $this->surname,
        ], 'id=:id', [':id' => $this->id])->execute();
        return true;
    }

    public static function getIdStudent($login)
    {
        $hash = (new \yii\db\Query())
            ->select('idUsers')
            ->from('users')
            ->where('username=:username', [':username' => $login])
            ->one();

        $hash1 = (new \yii\db\Query())
            ->select('id')
            ->from('student')
            ->where('type_user=:type', [':type' => $hash['idUsers'] ])
            ->one();
        self::$Pr_Student = $hash1['id'];
        return $hash1;
    }

    public static  function getGroup($login)
    {
        $hash1 = self::getIdStudent($login);
        $hash21 = (new \yii\db\Query())
            ->select('FK_Group')
            ->from('student')
            ->where('id=:idSt', [':idSt' => $hash1['id']])
            ->one();
        return $hash21;
    }

    public static function getNameGroup($login)
    {
        $hash21 = self::getGroup( $login);
        $query = (new \yii\db\Query())
            ->select('Name')
            ->from('group')
            ->where('idgroup=:idg', [':idg' => $hash21['FK_Group'] ])
            ->one();
        return ($query['Name'] === 'New')? 'No group':  $query['Name'];
    }

    //get subjects for student group
    public static function getSubjectGroups($login)
    {
        $hash21 = self::getGroup( $login);
        $query =  new Query;
        $query -> select(['subject.name', 'subject.idSubject'])
            ->from('subject')
            ->join('LEFT OUTER JOIN', 'group_subject', 'group_subject.FK_Subject = subject.idSubject')
            ->where(['group_subject.FK_Group'=>$hash21['FK_Group']]);
        $command = $query->createCommand();
        $group = $command->QueryAll();
        Yii::$app->session->set('group',  $group['name']);
        return $group;
    }

    //get tasks for student subjects
    public static function getMySubjectTasks($idSubject, $idSt = NULL)
    {
        $query =  new Query;
        $query -> select(['individual_works.File', 'individual_works.Status','individual_works.Mark', 'individual_works.Completion_date', 'individual_works.FK_Task', 'list_of_task.Name_of_task', 'list_of_task.Date'])
            ->from('individual_works')
            ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
            ->where(['individual_works.FK_Student'=>self::$Pr_Student])
            ->andWhere(['list_of_task.FK_Subject'=>$idSubject]);
        $command = $query->createCommand();
        $tasks = $command->QueryAll();
        return $tasks;
    }

    public static function dateTask($idTask)
    {
        $query =  new Query;
        $query -> select(['list_of_task.idList_of_task', 'list_of_task.Name_of_task', 'list_of_task.Date'])
            ->from('list_of_task')
            ->join('LEFT OUTER JOIN', 'individual_works', 'list_of_task.idList_of_task = individual_works.FK_Task')
            ->where(['individual_works.FK_Task'=>$idTask]);
        $command = $query->createCommand();
        $date = $command->QueryOne();
        if($date == false){
            return 1;
        }
        else
            return 0;
    }

    public static  function getRegistrationDate()
    {
        $query = (new \yii\db\Query())
            ->select('registration_date')
            ->from('student')
            ->where('id=:idSt', [':idSt' => self::$Pr_Student ])
            ->one();
        $date=date_create($query['registration_date']);
        return  date_format( $date,"d.m.y");
    }

    public static  function getLastVisit()
    {
        self::setLastVisit();
        $query = (new \yii\db\Query())
            ->select('last_visit')
            ->from('student')
            ->where('id=:idSt', [':idSt' => self::$Pr_Student ])
            ->one();
        $date=date_create($query['last_visit']);
        return  date_format( $date,"d.m.y H:i");
    }

    public static function setLastVisit()
    {
        $last_visit =  date("Y-m-d H:i:s");
        $db = Yii::$app->db->createCommand();
        $db->update('student', [
            'last_visit' => $last_visit,
        ], 'id=:id', [':id' => self::$Pr_Student])->execute();
        return true;
    }

    public static function getAllNewStudent()
    {
        $query =  new Query;
        $str = 'New';
        $query -> select(['student.Name', 'student.Surname', 'student.id', 'group.name AS groupName'])
               ->from('student')
               ->join('LEFT OUTER JOIN', 'group', 'student.FK_Group=group.idgroup')
               ->where(['group.Name'=>$str]);
        $command = $query->createCommand();
        $newStudents = $command->QueryAll();
        return $newStudents;
    }

    //set group for new student
    public function setGroup()
    {
        $db = Yii::$app->db->createCommand();
        $group = Group::getIdGroup($this->studentGroup);
        $db->update('student', [
            'FK_Group' => $group,
            'status' => 'active',
        ], 'id=:id', [':id' => $this->id])->execute();
        return true;
    }










}