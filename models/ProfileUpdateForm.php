<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.01.2016
 * Time: 19:35
 */

namespace app\models;
use yii\base\Model;
use Yii;

/**
 * ProfileUpdateForm is the model behind the profileUpdate form.
 */

class ProfileUpdateForm extends Model
{
    public $surname;
    public $name;
    public $phone;
    public $skype;
    public $fg;

    /**
     * @var User
     */
    private $_user;

    public function __construct(Professor $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function init()
    {
        $this->surname=$this->_user->surname;
        $this->name = $this->_user->name;
        $this->skype = $this->_user->skype;
        $this->phone = $this->_user->phone;
        $this->fg= $this->_user->name;
        parent::init();
    }

    public function rules()
    {
        return [
            ['surname', 'required'],
            ['surname', 'string'],
            ['name', 'required'],
            ['name', 'string'],
            ['skype', 'string'],
            ['phone', 'string'],
        ];
    }

    public function infoToView()
    {
        return $this->_user->name;
    }

    public function update()
    {
        \Yii::trace('update',  $this->_user->surname);
        if ($this->validate())
        {
            $user = $this->_user;
                $user->surname = $this->surname;
                $user->name = $this->name;
                $user->skype = $this->skype;
                $user->phone = $this->phone;
            return $user->updateData();
        } else {
            return false;
        }
    }
}