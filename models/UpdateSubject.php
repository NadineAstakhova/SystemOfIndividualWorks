<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 10.04.2016
 * Time: 16:33
 */

namespace app\models;

use yii\base\Model;
use Yii;

/**
 * This is the model class for modal update windows
 **/

class UpdateSubject extends Model
{
    public $Name;

    public $idSubject;
    public  $Mark;
    public $Status;
    public  $idWork;
    public  $student;
    public $Date;
    public $idTask;

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
            [['Name'], 'string', 'max' => 255],
            [['Mark'], 'integer'],
            [['Status'], 'string'],
            ['student', 'integer'],
            [['Date'], 'string', 'max' => 13],
            ['Date', 'match', 'pattern' => '#^(20)\d\d-((0[1-9]|1[012])-(0[1-9]|[12]\d)|(0[13-9]|1[012])-30|(0[13578]|1[02])-31)$#i'],
            ['idTask', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idSubject' => 'idSubject',
            'Name' => 'name',
            'student' => 'student',
            'Date' => 'Date',
            'idTask'=>'idTask',

        ];
    }

    public function init()
    {
        $this->Name = $this->_subject->Name;
        $this->idSubject = $this->_subject->idSubject;
        parent::init();
    }


    public static function outputUpdateTable($groups, $id)
    {
        foreach ($groups as $group)
        {
            echo "<tr>";
            echo "<td colspan=4 align='center'>" . $group['name']."</td>";
            $studentInGroup = Subject::getStudentsInGroup($id, $group['idgroup']);
            foreach ($studentInGroup as $students) {
                echo "<tr>";
                echo "<td>";
                echo "" . $students['name']." ".$students['surname'];
                echo "</td>";
                echo "<td>";
                echo "" . $students['File'];
                echo "</td>";
                echo "<td>";
                echo "<select name='Status'/>";
                foreach(\app\models\Individual_Work::$arrStatus as $status){
                    if ($status == $students['Status'])
                        echo "<option selected value='".$status."'>".$status."</option>";
                    else
                        echo "<option value='".$status."'>".$status."</option>";
                }
                echo "</select></td>";
                echo "</td>";
                echo "<td>";
                echo "<input type='number'  min='0' max='100' name='Mark' value='".$students['Mark']."'/>";
                echo "</td>";
                echo "</tr>";
            }
        }
    }

    //update subject name
    public function updateName()
    {
        if ($this->validate())
        {
            $subject = $this->_subject;
            $subject->Name = $this->Name;
            \Yii::trace( true, "update");
            return $subject->myUpdateSubjectName();
        }
        else
        {
            \Yii::trace( false, "no update");
            echo "no update";
            return false;
        }
    }

    //update date of subject task
    public function updateDate()
    {
        $this->idTask = Yii::$app->session->get('getIdTask');
        if ($this->validate())
        {
            $subject = $this->_subject;
            $subject->DateTask = $this->Date;
            $subject->idWork = $this->idTask;
            \Yii::trace( true, "update");
            return $subject->myUpdateTaskDate();
        }
        else
        {
            \Yii::trace( false, "no update");
            return false;
        }
    }

    //update information about student work
    public function update()
    {
        $mark = array(Yii::$app->session->get('mark'));
        $this->idWork = Yii::$app->session->get('getTaskId');
        echo $mark['1'];
        \Yii::trace( $mark, "mark");
        if ($this->validate())
        {
            $subject = $this->_subject;
            $subject->Name = $this->Name;
            $subject->Status = Individual_Work::$arrStatus[$this->Status];
            $subject->Mark = $this->Mark;
            $subject->idWork = $this->idWork;
            \Yii::trace( true, "update");
            return $subject->updateTask();
        }
        else
        {
            \Yii::trace( false, "no update");
            echo "no update";
            return false;
        }
    }
}