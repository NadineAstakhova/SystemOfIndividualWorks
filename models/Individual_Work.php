<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 12.04.2016
 * Time: 19:26
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table 'Individual_Work'
 **/

//status of work
class Enum_Status extends Enum
{
    const NEW_WORK = 'new';
    const ACCEPT = 'accept';
    const NOACCEPT = 'no accept';
}

class Individual_Work extends \yii\db\ActiveRecord
{
    private  $db;

    public static $arrStatus = array(
        Enum_Status::NEW_WORK,
        Enum_Status::ACCEPT,
        Enum_Status::NOACCEPT);

    public static function tableName()
    {
        return '{{%individual_works}}';
    }

    public function rules()
    {
        return [
            [['File'], 'string', 'max' => 100],
            [['Mark'], 'integer'],
            [['Status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'File' => 'File',
            'Mark' => 'Mark',
            'Status' => 'Status',
            'Groups'
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
        return static::findOne(['idInd_work' => $id]);
    }

    public static function findByIndFileName($indname)
    {
        return static::find()->where(['File' => $indname])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    //get name of file and id of work by task and student
    public function findByIdTaskStudent($idTask, $idStudent)
    {
        $subQuery = (new \yii\db\Query())
            ->select(['File', 'idInd_work'])
            ->from('individual_works')
            ->where('FK_Task=:task', [':task' => $idTask])
            ->andWhere('FK_Student=:st', [':st' =>$idStudent])
            ->one();
        return $subQuery;
    }

    public  static function updateIndWork($mark, $id, $status = 'accept')
    {
        $db = Yii::$app->db->createCommand();
        $db->update('individual_works', [
            'Status' => $status,
            'Mark' => $mark,
        ], 'idInd_work=:id', [':id' => $id])->execute();
        return true;
    }

    //create new work with default parameters
    public function createWork($file, $idtask, $student, $comp_date ){
        $db = Yii::$app->db->createCommand();
        $db->insert('individual_works', [
            'File' => $file,
            'FK_Task' => $idtask,
            'FK_Student' => $student,
            'Status' => 'new',
            'Completion_date' => $comp_date,
        ])->execute();
        return true;
    }

    //get id of new work from database
    public function getNewID($idtask, $student){
        $subQuery = (new \yii\db\Query())
            ->select('idInd_work')
            ->from('individual_works')
            ->where('FK_Task=:task', [':task' => $idtask])
            ->andWhere('FK_Student=:st', [':st' =>$student])
            ->one();
        return  $subQuery['idInd_work'];
    }

    /*
     * Create message in database about new work
     * This function is used in client app and api
     */
	public function createMessage($task, $author, $subject, $prof, $work){
		 $db = Yii::$app->db->createCommand();
         $db->insert('messages', [
                'new_task' => $task,
                'author' => $author,
                'subject' => $subject,
                'FK_Prof' => $prof,
                'id_work' => $work,
            ])->execute();
        return true;
	}
    /*
     * Delete message about new work when message is send
     * This function is used in client app and api
     */
    public function deleteMess($idWork){
        $query = new Query;
        $query ->createCommand()
            ->delete('messages', ['id_work' => $idWork])
            ->execute();
        return true;
    }

    //check if work already exists
    public function existThisWork($indname, $idTask, $idStudent)
    {
        $str = $this->findByIdTaskStudent($idTask, $idStudent);
        $s = pathinfo($str['File'], PATHINFO_FILENAME);
        $newWork = pathinfo($indname, PATHINFO_FILENAME);
        $dir_del = 'C:/WebServers/home/localhost/www'; //now for local server
        //if work exists
        if(strcasecmp($s, $newWork) == 0)
        {
            //delete row from database
            $this->deletePreviousVer($str['idInd_work']);
            $del_file=$str['File'];
            //delete file from server
            $this->deletePrevFile($dir_del,$del_file);
            return true;
        }
        else
            return false;
    }

    //delete row with previous information from database
    public function deletePreviousVer($id)
    {
        $query = new Query;
        $query ->createCommand()
               ->delete('individual_works', ['idInd_work' => $id])
               ->execute();
        return true;
    }

    //delete file with previous version from server
    public function deletePrevFile($path,$filename)
    {
        $file =array();
        $file[] = $path.$filename;
        $file[] = $path.'sqr_'.$filename;
        $file[] = $path.'sm_'.$filename;
        foreach ($file as $f)
        {
            if (!empty($f) && file_exists($f)){
                unlink($f);
            }
        }
    }
}