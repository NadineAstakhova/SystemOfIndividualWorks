<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 04.02.2016
 * Time: 21:03
 */

namespace app\models;
use yii\base\Model;
use Yii;
use yii\db\ActiveQuery;

/**
 * PasswordChangeForm is the model behind the passwordChange form.
 */

class PasswordChangeForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @var User
     */
    private $_user;
    public $username;
    public $email;

    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(AbstractUser $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function init()
    {
        $this->username = $this->_user->username;
        $this->email = $this->_user->email;
        parent::init();
    }

    public function rules()
    {
        return [
            ['email', 'email'],
            [
                'email',
                'unique',
                'targetClass' => AbstractUser::className(),
                'message' => Yii::t('app', 'ERROR_EMAIL_EXISTS'),
                'filter' => function (ActiveQuery $query) {
                    $query->andWhere(['<>', 'idUsers', $this->_user->id]);
                },
            ],
            ['email', 'string', 'max' => 255],
            ['username', 'string'],
            ['currentPassword', 'validatePassword'],
            ['newPassword', 'string', 'min' => 6],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'newPassword' => Yii::t('app', 'New Password'),
            'newPasswordRepeat' => Yii::t('app', 'Repeat Password'),
            'currentPassword' => Yii::t('app', 'Current Password'),
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->currentPassword, $this->_user->username)) {
                $this->addError($attribute, Yii::t('app', $this->currentPassword));
            }
        }
    }

    /**
     * @return boolean
     */
    public function changePassword()
    {
        if ($this->validate())
        {
            $user = $this->_user;
            if(strlen($this->newPassword) > 0)
                $user->setPassword($this->newPassword);
            $user->username = $this->username;
            $user->email = $this->email;
            return $user->updateData();
        } else {
            return false;
        }
    }
}