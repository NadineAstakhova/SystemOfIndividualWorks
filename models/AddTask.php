<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.04.2016
 * Time: 13:00
 */

namespace app\models;

/**
 * This is the model class for modal window
 **/

class AddTask extends \yii\db\ActiveRecord
{
    private $_subject;
    public $Name_of_task;
    public $FK_Subject;
    public $date;

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

    public function rules()
    {
        return [
            [['Name_of_task'], 'string', 'max' => 100],
            //['Name_of_task', 'match', 'pattern' => '#^[A-z�-���]+_{1}[0-9]{1,2}$#i'],
            [['date'], 'string', 'max' => 13],
            ['date', 'match', 'pattern' => '#^(20)\d\d-((0[1-9]|1[012])-(0[1-9]|[12]\d)|(0[13-9]|1[012])-30|(0[13578]|1[02])-31)$#i'],
            ['FK_Subject', 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Name_of_task' => 'Name_of_task',
            'FK_Subject' => 'FK_Subject',
            'date' => 'date,'
        ];
    }

    public function init()
    {
        $this->FK_Subject = $this->_subject->idSubject;
        parent::init();
    }

    public function add()
    {
        //change title by template
        $str = preg_replace('/ {1,}/','_', $this->Name_of_task);;
        if ($this->validate()) {
            $subject = $this->_subject;
            $subject->Name_of_task = $str;
            $subject->DateTask = $this->date;
            return $subject->addTasks();
        } else
            return false;
    }
}