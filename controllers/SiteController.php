<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use yii\web\BadRequestHttpException;
use yii\base\InvalidParamException;
use app\models\Student;

class SiteController extends Controller
{
    static public $hz;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return Yii::$app->user->isGuest ?
        $this->redirect(['main'], 301):
        $this->redirect(['profile/index'], 301);
    }

    public function actionLogin()
    {
        \Yii::info('action Login','user');
        Yii::getLogger()->flush(true);
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $isSt= Yii::$app->session->get('AccessST');
        \Yii::trace( "Student?", $isSt);
        // Selection of where to look: in the students or professors
        if( $isSt == false)
        {
            Student::$isStudent = 0;
            LoginForm::$checkStudent = 0;
            $prof = Yii::$app->session->get('userPr');
            $model->load($prof);
            \Yii::trace( self::$hz, "Professor after load in SiteController");
            \Yii::trace( $model->username, "Professor username");
            Yii::getLogger()->flush(true);
            return $this->render('login', [
                'model' => $model,

            ]);
        }
        else
        {
            Student::$isStudent = 1;
            LoginForm::$checkStudent = 1;
            \Yii::trace($model->you, "isStudent after load in SiteController");
            \Yii::trace( $model->username, "Student username");
            Yii::getLogger()->flush(true);
            return $this->render('login', [
                'model' => $model,

            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail']))
        {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionMain()
    {
        return $this->render('main');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()))
        {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                } }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionPasswordResetRequest()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Send');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry');
            }
        }
        return $this->render('passwordResetRequest', [
            'model' => $model,
        ]);
    }

    public function actionPasswordReset($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword())
        {
            Yii::$app->getSession()->setFlash('success', 'Thank you');
            return $this->goHome();
        }
        return $this->render('passwordReset', [
            'model' => $model,
        ]);
    }

    public function createDirectory($path) {
        if (file_exists($path)) {
            //echo "The directory {$path} exists";
        } else {
            mkdir($path, 0775, true);
            //echo "The directory {$path} was successfully created.";
        }
    }
}
