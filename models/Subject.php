<?php

namespace app\models;

use Yii;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "subject".
 *
 * @property integer $id
 * @property string $Name
 * @property string $Program
 * @property string $Color
 * @property string $Picture
 * @property string $file
 * @property string $FK_Professor
 */
class Subject extends \yii\db\ActiveRecord
{
    private  $db;
    public $Groups;
    public $Mark;
    public $Status;
    public $File;
    public $Student;
    public $studentName;
    public $idSubForMes;
    public $subjectName;
    public $Task;
    public $idSubjectTask;
    public $nameGroup;
    public $Name_of_task;
    public $DateTask;
    public $idWork;
    public $statusName;

    private static $arrT;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subject}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['FK_Professor'], 'integer'],
            [['Name'], 'string', 'max' => 100],
            [['Mark'], 'integer'],
            [['Status'], 'string'],
            ['File', 'string'],
            [['Task'], 'string'],
            ['Student', 'integer'],
            ['idSubjectTask', 'integer'],
            [['nameGroup'], 'string', 'max' => 45],
            [['Name_of_task'], 'string', 'max' => 100],
            [['DateTask'], 'string', 'max' => 13],
            ['idWork', 'integer'],
            ['statusName', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idSubject' => 'idSubject',
            'Name' => 'Name',
            'FK_Professor' => 'Fk  Professor',
            'File' => 'File',
            'Groups' => 'Groups',
            'Student' => 'Student',
            'Task' => 'Task',
            'idSubjectTask' => 'idSubjectTask',
            'nameGroup' => 'nameGroup',
            'Name_of_task' => 'Name_of_task',
            'DateTask' => 'DateTask',
            'statusName' => 'statusName',
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
        return static::findOne(['idSubject' => $id]);
    }

    public static function findBySubjectName($subjectname)
    {
        return static::find()->where(['Name' => $subjectname])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }
    //get groups for subject
    public static function getGroups($idSub)
    {
        $query =  new Query;
        $query -> select(['group.name', 'group.idgroup'])
            ->from('group')
            ->join('LEFT OUTER JOIN', 'group_subject', 'group_subject.FK_group = group.idgroup')
            ->where(['group_subject.FK_subject'=>$idSub]);
        $command = $query->createCommand();
        $group = $command->QueryAll();
        Yii::$app->session->set('group',  $group['name']);
        return $group;
    }

    //get student's files for this subject from this group
    public static function getStudentsInGroup($idSub, $group)
    {
        $query =  new Query;
       $query -> select(['student.name', 'student.surname', 'student.id', 'student.last_visit', 'individual_works.idInd_work', 'individual_works.Completion_date', 'individual_works.File', 'individual_works.Status','individual_works.Mark'])
            ->from('individual_works')
            ->join('LEFT OUTER JOIN', 'student', 'individual_works.FK_Student = student.id')
            ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
            ->where(['list_of_task.FK_Subject'=>$idSub])
            ->andWhere(['student.FK_Group'=>$group]);
        $command = $query->createCommand();
        $StudentInGroup= $command->QueryAll();
        Yii::$app->session->set('studentsGroup',  $StudentInGroup);
        return $StudentInGroup;
    }

    public static function outputTable($groups, $id, $Name)
    {
        foreach ($groups as $group) {
            echo "<tr class='tableTrNew' id='tr_sub_Group'>";
            echo "<td colspan=6 align='center'>" . $group['name'];
            echo Html::a(Yii::t('app', Html::img('../views/images/delete.png', ['alt'=>'delete', 'class'=>'icons_delete',
                'data-toggle'=>'tooltip','data-placement'=>'auto bottom', 'title'=>'Delete subject for this group'])),
                ['deletegroup', 'idDeleteGroup'=>$group['idgroup'], 'idSubject'=>$id]);
            echo "</td>";
            $studentInGroup = Subject::getStudentsInGroup($id, $group['idgroup']);
            foreach ($studentInGroup as $students) {
                echo "<tr class='tableTrNew' id='sub_tr'>";
                echo "<td>";
                echo Html::a(Yii::t('app', $students['name'].' '.$students['surname']), ['index', 'Name' => $Name, 'idSubject'=>$id, 'sortBy' => $students['name'].' '.$students['surname']]);
                echo '<br> <font size="1%" color="#828282"> Last visit: '. date_format(date_create($students['last_visit']),"d.m.y H:i").'</font>';
                echo "</td>";
                echo "<td>";
                $str=strpos($students['File'], "_");
                $row=substr($students['File'],  $str+1);
                echo Html::a(Yii::t('app', $row), ['download', 'name' => $students['File'] ]);
                echo "</td>";
                echo "<td>";
                echo Html::a(Yii::t('app', $students['Status']), ['index', 'Name' => $Name, 'idSubject'=>$id, 'sortBy' => $students['Status'] ]);
                echo "</td>";
                echo "<td>";
                $date = date_create($students['Completion_date']);
                echo date_format( $date,"d.m.y");
                echo "</td>";
                echo "<td>";
                echo "" . $students['Mark'];
                echo "</td>";
                echo "<td>";
                echo "<a value=".Url::to('index.php?r=subject%2Fupdaterow')."&id=".$id."&idTask=".$students['idInd_work'].
                    " class = 'modalUpdateStudent' >".
                    Html::img('../views/images/edit.png', ['alt'=>'change', 'class'=>'icons_update'])."</a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        Modal::begin([
            'header' => "<h4>Update mark and status</h4>",
            'id' => 'modalUpdateStudent',
            'size' => 'modal-lg'
        ]);
        echo"<div id='modalContent'></div>";
        Modal::end();
    }

    //sort students in group by filter
    public static function sortStudentsInGroup($idSub, $group, $sort)
    {
        $query =  new Query;
        $arr = \app\models\Individual_Work::$arrStatus;
        for ($i = 0; $i < count($arr); $i++ )
            if ($sort === $arr[$i]){
                $query -> select(['student.name', 'student.surname', 'student.id', 'individual_works.idInd_work', 'individual_works.Completion_date', 'individual_works.File', 'individual_works.Status','individual_works.Mark'])
                    ->from('individual_works')
                    ->join('LEFT OUTER JOIN', 'student', 'individual_works.FK_Student = student.id')
                    ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
                    ->where(['list_of_task.FK_Subject'=>$idSub])
                    ->andWhere(['student.FK_Group'=>$group])
                    ->andWhere(['individual_works.Status' => $sort]);
                $command = $query->createCommand();
                $StudentInGroupWithSort= $command->QueryAll();
                return $StudentInGroupWithSort;
            }
            else
                continue;
        $arrayT = self::$arrT;
        for ($i = 0; $i < count($arrayT); $i++)
            if($sort === $arrayT[$i]){
                $query -> select(['student.name', 'student.surname', 'student.id', 'individual_works.Completion_date',  'individual_works.idInd_work','individual_works.File', 'individual_works.Status','individual_works.Mark'])
                    ->from('individual_works')
                    ->join('LEFT OUTER JOIN', 'student', 'individual_works.FK_Student = student.id')
                    ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
                    ->where(['list_of_task.FK_Subject'=>$idSub])
                    ->andWhere(['student.FK_Group'=>$group])
                    ->andWhere(['list_of_task.Name_of_task' => $sort]);
                $command = $query->createCommand();
                $StudentInGroupWithSort= $command->QueryAll();
                return $StudentInGroupWithSort;
            }
            else
                continue;

        $result = explode(' ', $sort);
        $query -> select(['student.name', 'student.surname', 'student.id', 'individual_works.Completion_date',  'individual_works.idInd_work','individual_works.File', 'individual_works.Status','individual_works.Mark'])
            ->from('individual_works')
            ->join('LEFT OUTER JOIN', 'student', 'individual_works.FK_Student = student.id')
            ->join('LEFT OUTER JOIN', 'list_of_task', 'individual_works.FK_Task=list_of_task.idList_of_task')
            ->where(['list_of_task.FK_Subject'=>$idSub])
            ->andWhere(['student.FK_Group'=>$group])
            ->andWhere(['student.name' => $result[0]])
            ->andWhere(['student.surname' => $result[1]]);
        $command = $query->createCommand();
        $StudentInGroupWithSort= $command->QueryAll();
        return $StudentInGroupWithSort;
    }

    public static function outputSortTable($groups, $id, $Name, $sort)
    {
        foreach ($groups as $group) {
            $studentInGroup = Subject::sortStudentsInGroup($id, $group['idgroup'], $sort);
            echo "<tr  class='tableTrNew' id='tr_sub_Group'>";
            if(count($studentInGroup) > 0)
                echo "<td colspan=6 align='center'>" . $group['name']."</td>";
            foreach ($studentInGroup as $students) {
                echo "<tr class='tableTrNew' id='sub_tr'>";
                echo "<td>";
                echo Html::a(Yii::t('app', $students['name'].' '.$students['surname']), ['index', 'Name' => $Name, 'idSubject'=>$id, 'sortBy' => $students['name'].' '.$students['surname']]);
                echo "</td>";
                echo "<td>";
                $str=strpos($students['File'], "_");
                $row=substr($students['File'],  $str+1);
                echo Html::a(Yii::t('app', $row), ['download', 'name' => $students['File'] ]);
                echo "</td>";
                echo "<td>";
                echo Html::a(Yii::t('app', $students['Status']), ['index', 'Name' => $Name, 'idSubject'=>$id, 'sortBy' => $students['Status'] ]);
                echo "</td>";
                echo "<td>";
                $date = date_create($students['Completion_date']);
                echo date_format( $date,"d.m.y");
                echo "</td>";
                echo "<td>";
                echo "" . $students['Mark'];
                echo "</td>";
                echo "<td>";
                echo Html::a(Yii::t('app',  Html::img('../views/images/edit.png',
                    ['alt'=>'change', 'class'=>'icons_update'])), ['updaterow', 'id' => "".$id, 'idTask' =>$students['idInd_work']]);
                echo "</td>";
                echo "</tr>";
            }
        }
    }
    //get all task for subject
    public static function getAllTasks($idSub)
    {
        $query =  new Query;
        $query -> select(['list_of_task.Name_of_task', 'list_of_task.Date', 'list_of_task.idList_of_task'])
               ->from('list_of_task')
               ->join('LEFT OUTER JOIN', 'subject', 'list_of_task.FK_Subject=subject.idSubject')
               ->where(['list_of_task.FK_Subject'=>$idSub]);
        $command = $query->createCommand();
        $task = $command->QueryAll();
        Yii::$app->session->set('group',  $task['name']);
        self::$arrT = array();
        foreach($task as $g){
            array_push(self::$arrT,$g['Name_of_task']);
        }
        return $task;
    }

    /**
     * Get all subject tasks for Student which doesn't have status 'accept'
     * @return array
     */
    public static function getAllNoAcceptTasks($idSub, $idStudent)
    {
        $alltasks = self::getAllTasks($idSub);
        $allAcceptTask = Student::getMySubjectTasks($idSub, $idStudent);
        $tasks = array(); //array for all tasks in subject
        foreach($alltasks as $allT){
            array_push($tasks, $allT['Name_of_task']);
        }
        foreach ($tasks as $i => $value) {
            //check what task student already passed and delete it from array of tasks
            foreach($allAcceptTask as $allMy) {
                if ($tasks[$i] === $allMy['Name_of_task'] && $allMy['Status'] === 'accept') {
                    unset($tasks[$i]);
                }
            }
        }
        return $tasks;
    }

    /**
     * Upload file with work  for student
     * @return bool
     * @throws \yii\db\Exception
     */
    public function  myUploadSubject()
    {
        //get task from list_of_task
        $idtask = $this->idTask($this->Task, $this->idSubjectTask);
        $today =  strtotime(date("Y-m-d H:i:s"));
        $d = Task::getDate( $idtask);
        $deadline = strtotime($d);
        $comp_date =  date("Y-m-d") ;
        //check if student can upload work after deadline
        if($deadline < $today)
            throw new UserException ('Sorry, deadline has already passed. Please contact your teacher. Be punctual next time');
        else
        {
            $ind = new Individual_Work();
            $ind->existThisWork($this->File, $idtask ,$this->Student);
            $r = $ind->createWork($this->File, $idtask ,$this->Student, $comp_date);
            if($r) {
                $newId = $ind->getNewID($idtask, $this->Student);
                $idProfForMes = $this->findIdentity($this->idSubForMes);
                //This function is necessary in client app and api
                $ind->createMessage($this->Task, $this->studentName, $this->subjectName, $idProfForMes['FK_Professor'], $newId);
            }
        return true;
        }
    }

    public function  myUpdateSubjectName()
    {
        $db = Yii::$app->db->createCommand();
        \Yii::trace( $this->Name, "New Subject Name");
        $db->update('subject', [
            'Name' => $this->Name,
        ], 'idSubject=:id', [':id' => $this->id])->execute();
        return true;
    }

    public function myUpdateTaskDate()
    {
        $task = new Task();
        $task->updateDate($this->DateTask, $this->idWork);
        return true;
    }

    public function updateTask()
    {
        $ind = new Individual_Work;
        if ($this->Status !== null) {
            $ind->updateIndWork($this->Mark, $this->idWork, $this->Status);
            if($this->Status !== 'new')
                $ind->deleteMess($this->idWork);
        }
        else {
            $ind->updateIndWork($this->Mark, $this->idWork);
            $ind->deleteMess($this->idWork);
        }
        return true;
    }
    //get id of task by name and subject
    public static function  idTask($name, $subject)
    {
        $query =  new Query;
        $query -> select(['list_of_task.idList_of_task'])
            ->from('list_of_task')
            ->where(['list_of_task.Name_of_task'=>$name])
            ->andWhere(['list_of_task.FK_Subject'=>$subject]);
        $command = $query->createCommand();
        $nameTask = $command->QueryOne();
        return  $nameTask['idList_of_task'];
    }

    public function createNewSubject()
    {
        $db = Yii::$app->db->createCommand();
        $db->insert('subject', [
            'Name' => $this->Name,
            'FK_Professor' => $this->FK_Professor,
        ])->execute();
        return true;
    }

    public function addNewGroupForSubject()
    {
        $group = new Group();
        if(Group::uniqueNameGroup($this->nameGroup) == 0)
        {
            echo $group->findByGroupName($this->nameGroup)['idgroup'];
            $group->insertGroup($this->nameGroup);
            $group->addGroup($this->idSubject, $group->findByGroupName($this->nameGroup)['idgroup']);
            return true;
        }
        else
        {
            throw new UserException('This name already exist');
        }
    }

    public function addGroupForSubject()
    {
        $group = new Group();
        //check unique name
        if (Group::unigueGroupsAndSubject($group->findByGroupName($this->nameGroup)['idgroup'], $this->idSubject) == 0)
        {
            $group->addGroup($this->idSubject, $group->findByGroupName($this->nameGroup)['idgroup']);
            return true;
        }
       else
        {
            throw new UserException('This Subject_Group  already exist');
        }
    }

    public function addTasks()
    {
        $task = new Task();
        $task->insertTask($this->Name_of_task, $this->DateTask, $this->idSubject);
        return true;
    }

    public static function getStatistics($idSub)
    {
        $tasks = self::getAllTasks($idSub);
        for ($i=0; $i < count($tasks); $i++) {
            $query = new Query;
            $query->select(['count(*) as Count'])
                ->from('individual_works')
                ->where(['individual_works.FK_Task' => $tasks[$i]['idList_of_task']]);
            $command = $query->createCommand();
            $count = $command->QueryOne();
            echo $tasks[$i]['Name_of_task'].' '.$count['Count'];
            echo "</br>";
        }
    }


    public function deleteGroupFromSubject($id, $idSubject)
    {
        $query = new Query;
        $query ->createCommand()
            ->delete('group_subject', ['FK_Group' => $id, 'FK_Subject' => $idSubject])
            ->execute();
        return true;
    }
}
