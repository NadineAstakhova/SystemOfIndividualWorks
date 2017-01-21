<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 16.04.2016
 * Time: 19:26
 */

namespace app\models;

use Yii;

/**
 * This is the model class for modal window
 **/

class AddGroup extends \yii\db\ActiveRecord
{
    private $_subject;

    public $nameGroup;
    public $namesGroups;
    public $FK_Subject;
    public  $nameNewGroup;

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
            [['nameGroup'], 'string', 'max' => 45],
            [['Name'], 'string', 'max' => 255],
            ['FK_Subject', 'integer'],
            ['namesGroups', 'string'],
            [['nameNewGroup'],'string','max' => 45],
            ['nameNewGroup', 'match', 'pattern' => '#^[A-z�-���]+_{1}[a-zA-Z0-9]+$#i'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nameGroup' => 'nameGroup',
            'Name' => 'Name',
            'FK_Professor' => 'Fk  Professor',
            'FK_Subject' => 'FK_Subject',
            'namesGroups' => 'namesGroups',
            'nameNewGroup' => 'nameNewGroup',
        ];
    }

    public function init()
    {
        $this->Name = $this->_subject->Name;
        $this->FK_Subject = $this->_subject->idSubject;
        parent::init();
    }


    public function add()
    {
        $arrGroups = Yii::$app->session->get('arrayGroupNames');
        if ($this->validate())
        {
            //if user created new group
            if (strlen($this->nameNewGroup) > 1)
            {
                $subject = $this->_subject;
                $subject->nameGroup = $this->nameNewGroup;
                return $subject->addNewGroupForSubject();
            }
            //if user choose old group
            else
            {
                $subject = $this->_subject;
                $subject->nameGroup = $arrGroups[$this->namesGroups];
                return $subject->addGroupForSubject();
            }

        } else
            return false;
    }
}