<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 16.04.2016
 * Time: 19:41
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table 'Group'
 **/

class Group extends \yii\db\ActiveRecord
{
    private  $db;

    public static function tableName()
    {
        return '{{%group}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['status_group'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'name',
            'status_group' => 'status_group',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['idgroup' => $id]);
    }

    public static function findByGroupName($indname)
    {
        return static::find()->where(['name' => $indname])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getIdGroup($groupname)
    {
        return self::findByGroupName($groupname)['idgroup'];
    }


    public function insertGroup($name)
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('group', [
            'name' => $name,
            'status_group' => 'unlock',
        ])->execute();
        return true;
    }

    public static function uniqueNameGroup($name)
    {
        $groups = self::getGroups();
        $flag = 0;
        foreach ($groups as $groupName)
        {
            if (strcmp($groupName['name'],$name)== 0){
                $flag++;
            }
        }
        return $flag;
    }

    public static function group_subjectTable($idSub)
    {
        $query =  new Query;
        $query -> select(['group_subject.FK_Group', 'group_subject.idGroup_subject'])
               ->from('group_subject')
               ->where(['group_subject.FK_Subject'=>$idSub]);
        $command = $query->createCommand();
        $groups = $command->QueryAll();
        \Yii::trace( $groups , "Group");
        return $groups;
    }

    public  static  function unigueGroupsAndSubject($idGroup, $idSub)
    {
        $groups = self::group_subjectTable($idSub);
        $flag = 0;
        foreach ($groups as $group)
        {
            if($group['FK_Group'] == $idGroup){
                $flag++;
            }
        }
        return $flag;
    }

    public function addGroup($fk_Subject,$fk_Group )
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('group_subject', [
            'FK_Subject' => $fk_Subject,
            'FK_Group' => $fk_Group,
        ])->execute();
        return true;
    }

    public static function getGroups()
    {
        $query =  new Query;
        $query -> select(['group.name', 'group.idgroup'])
               ->from('group');
        $command = $query->createCommand();
        $group = $command->QueryAll();
        return $group;
    }

    public static function getStudentsInGroup($name)
    {
        $query =  new Query;
        $query -> select(['student.name', 'student.surname', 'student.id', 'student.last_visit'])
               ->from('student')
               ->join('LEFT OUTER JOIN', 'group', 'student.FK_Group = group.idgroup')
               ->where(['group.name'=>$name]);
        $command = $query->createCommand();
        $StudentInGroup= $command->QueryAll();
        Yii::$app->session->set('studentsGroup',  $StudentInGroup);
        return $StudentInGroup;
    }

    public function deleteStudentFromGroup($idStudent)
    {
        $db = Yii::$app->db->createCommand();
        $db->update('student', [
            'FK_Group' => 1,
        ], 'id=:id', [':id' => $idStudent])->execute();
        return true;
    }

    public function deleteGroup($name)
    {
        $id = self::getIdGroup($name);
        $db = Yii::$app->db->createCommand();
        $db->update('student', [
            'FK_Group' => 1,
        ], 'FK_Group=:id', [':id' => $id])->execute();
        $query = new Query;
        $query ->createCommand()
            ->delete('group', ['idgroup' => $id])
            ->execute();
        return true;
    }
}