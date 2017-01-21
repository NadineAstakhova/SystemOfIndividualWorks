<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.04.2016
 * Time: 13:29
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "student".
 **/

class Task extends \yii\db\ActiveRecord
{
    private  $db;

    public static function tableName()
    {
        return '{{%list_of_task}}';
    }

    public function rules()
    {
        return [
            [['Name_of_task'], 'required'],
            [['Name_of_task'], 'string', 'max' => 100],
            [['Date'], 'string'],
            [['FK_Subject'],'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Name_of_task' => 'Name_of_task',
            'Date' => 'Date',
            'FK_Subject' => 'FK_Subject',
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
        return static::findOne(['idList_of_task' => $id]);
    }

    public static function findByTaskName($indname)
    {
        return static::find()->where(['Name_of_task' => $indname])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function insertTask($name, $date, $Fk_Subject)
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('list_of_task', [
            'Name_of_task' => $name,
            'Date'=> $date,
            'FK_Subject' => $Fk_Subject,
        ])->execute();
        return true;
    }

    //update deadline for task
    public function updateDate($date, $id)
    {
        $db = Yii::$app->db->createCommand();
        $db->update('list_of_task', [
            'Date' => $date,
        ], 'idList_of_task=:id', [':id' =>$id])->execute();
        return true;
    }

    //deadline is mandatory or not
    public static function getDate($id)
    {
        $query =  new Query;
        $query -> select(['list_of_task.Date'])
            ->from('list_of_task')
            ->where(['list_of_task.idList_of_task'=>$id]);
        $command = $query->createCommand();
        $date = $command->QueryOne();
        return $date['Date'];
    }
}