<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "subject".
 * This is the model class for modal window
 * @property integer $id
 * @property string $Name
 * @property string $Program
 * @property string $Color
 * @property string $Picture
 * @property string $file
 * @property string $FK_Professor
 */
class UploadSubject extends \yii\db\ActiveRecord
{
    public $file;
    public $task;
    public $fileDB;
    public $filename;
    public  $student;
    public $date;
    public $idSub;
    public $arrTasks;

    private $_subject;

    public function __construct(Subject $subject, $config = [])
    {
        $this->_subject = $subject;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['FK_Professor'], 'integer'],
            [['Name'], 'string', 'max' => 255],
            [['task'], 'string'],
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'txt, doc, pdf, docx'],
            ['filename', 'string'],
            ['student', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idSubject' => 'IDSubject',
            'Name' => 'Name',
            'file' => 'File',
            'student' => 'student',
            'task' => 'Task',
            'FK_Professor' => 'Fk  Professor',
        ];
    }

    public function init()
    {
        $this->Name = $this->_subject->Name;
        $this->FK_Professor = $this->_subject->FK_Professor;
        parent::init();
    }

    public function validateDate()
    {
        $idtask = Subject::idTask($this->arrTasks[$this->task], $this->idSub);
        $d = Task::getDate( $idtask);
        $deadline = strtotime($d);
        $today =  strtotime(date("Y-m-d H:i:s"));
        \Yii::trace( $idtask, "nameTask");
        if( $deadline < $today) {
                $this->addError('task', 'Sorry, deadline has already passed. Please contact your teacher. Be punctual next time');
        }
        return !$this->hasErrors();
    }

    public function upload()
    {
       //get link for student
       $this->student = Yii::$app->session->get('getStudent');
       //get name and surname for file nomination
       $studentName = Yii::$app->session->get('getStName');
       //get tasks array
       $this->arrTasks = Yii::$app->session->get('arrayTasksNames');
       //get link for subject
       $this->idSub = Yii::$app->session->get('idSubjectForTask');
       $nameSub = Yii::$app->session->get('nameSubjectForTask');
       \Yii::trace( $this->idSub, "subject");
       if($this->file !== null)
       {
           //getting a file
           $file = UploadedFile::getInstance($this, 'file');
           //for local server in this version
           $dir = Yii::getAlias('C:/WebServers/home/localhost/www/basic0/uploads/');
           $dir_del = 'C:/WebServers/home/localhost/www';
           //directory for server
           $dirWeb = '/basic0/uploads';
           //create file title by template  Task_Number_Name_Surname
           $fileName = $nameSub.'_'.$this->arrTasks[$this->task] .'_'.$studentName.'.'.$file->extension;
           //Save file
           $file->saveAs($dir . $fileName);
           $file = $fileName; // без этого ошибка
           $fileDB = $dirWeb . '/' . $fileName;
           $this->file = $file;
           $this->fileDB = $fileDB;

           if ($this->validate())
           {
               $subject = $this->_subject;
               $subject->File =  $this->fileDB;
               $subject->Student = $this->student;
               $subject->Task = $this->arrTasks[$this->task];
               $subject->idSubjectTask = $this->idSub;
               $subject->studentName = $studentName;
               $subject->idSubForMes = $this->idSub;
               $subject->subjectName = $nameSub;
               return $subject->myUploadSubject();
           } else
               return false;
       }
       else
           return false;
    }
}
