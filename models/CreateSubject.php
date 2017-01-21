<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 15.04.2016
 * Time: 21:41
 */

namespace app\models;

use Yii;

/**
 * This is the model class for modal window
 **/

class CreateSubject extends \yii\db\ActiveRecord
{
    public $Name;
    public $FK_Professor;
    public $username;

    public function rules()
    {
        return [
            ['Name', 'required'],
            ['Name', 'string', 'min' => 2, 'max' => 100],

            ['FK_Professor', 'required'],
            ['FK_Professor', 'integer'],

            ['username', 'string'],
        ];
    }

    public static function tableName()
    {
        return 'subject';
    }

    public function attributeLabels()
    {
        return [
            'Name' => 'Name',
            'FK_Professor' => 'FK_Professor',
            'username' => 'username',
        ];
    }


    public function creatingSubject()
    {
        $subject = new Subject();
        $this->FK_Professor = Yii::$app->session->get('idProfForCreate');
        $subject->Name = $this->Name;
        $subject->FK_Professor = $this->FK_Professor;
        return $subject->createNewSubject()? $subject :  print_r($subject->getErrors());
    }
}