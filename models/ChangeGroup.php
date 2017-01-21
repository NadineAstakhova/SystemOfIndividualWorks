<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 30.04.2016
 * Time: 22:38
 */

namespace app\models;

use Yii;

/**
 * This is the model class for modal window
 **/

class ChangeGroup extends \yii\db\ActiveRecord
{
    public $group;
    private $_student;

    public function __construct(Student $student, $config = [])
    {
        $this->_student = $student;
        parent::__construct($config);
    }

    public static function tableName()
    {
        return 'student';
    }

    public function rules()
    {
        return [
            [['group'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group' => 'group',
        ];
    }

    public function init()
    {
        parent::init();
    }

    //change group for student
    public function changeGroup()
    {
        $arrGroups = Yii::$app->session->get('arrayGroupNamesSt');
        if ($this->validate())
        {
            $student = $this->_student;
            $student->studentGroup =  $arrGroups[$this->group ];
            return $student->setGroup();
        }
        else
            return false;
    }
}