<?php

namespace app\controllers;

use app\models\AbstractUser;
use app\models\ChangeGroup;
use app\models\Group;
use app\models\Professor;
use app\models\Student;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\ProfileUpdateForm;
use app\models\PasswordChangeForm;
use Yii;
class ProfileController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
       $ch = Yii::$app->session->get('check_result');
        echo $ch;
        \Yii::trace( $ch, "POST in ProfileController");
    }

    public function actionIndex()
    {
        //check type of user by unique Login
        $isStudent = Yii::$app->session->get('AccessST');
        if ($isStudent == 0)
        {
            return $this->render('index', [
                'model' => $this->findModel()]);
        }
        return $this->render('indexStudent', [
            'model' => $this->findModel()]);
    }

    /**
     * @return Professor the loaded model
     */
    private function findModel()
    {
        //get type of user
        $checkProf = Yii::$app->session->get('prof');
        $checkSt = Yii::$app->session->get('stud');
        //if type of user is professor
        $isStudent = Yii::$app->session->get('AccessST');
        \Yii::trace('Student?', $isStudent);
        //if type of user is professor
        if ($checkProf['name'] != null)
        {
            $model = $this->findModelProf($checkProf['type_user']);
            \Yii::trace($checkProf, "POST in ProfileController");
            return $model;
        }
        //if type of user is student
        elseif ($checkSt['name'] != null)
        {
            $model = $checkSt;
            \Yii::trace($checkSt, "POST in ProfileController");
            return $model;
        }
        //if user not found
        else { return null; }

    }

    //find student user
    private function findModelStudent($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    //find professor user
    private function findModelProf($id)
    {
        if (($model = Professor::findByUser($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        //get user login
        $get = $request->get('login');
        $user = Professor::findByUsername($get);
        Yii::$app->session->set('getLogin', $user['type_user'] );
        //create model for update information
        $model = new ProfileUpdateForm($user);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
           return $this->redirect(['index']);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionPasswordChange()
    {
        $request = Yii::$app->request;
        $get = $request->get('login');
        $user = AbstractUser::findByUsername($get);

        $model = new PasswordChangeForm($user);
        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('passwordChange', [
                'model' => $model,
            ]);
        }
    }

    public function actionChangegroup()
    {
        $request = Yii::$app->request;
        $get = $request->get('idStudent');
        //find model of student by id
        $student = $this->findModelStudent($get);
        $model = new ChangeGroup($student);
        if ($model->load(Yii::$app->request->post()) && $model->changeGroup()) {
            return $this->redirect(['profile/index']);
        } else {
            return $this->renderAjax('changegroup', [
                'model' => $model,
            ]);
        }
    }

    public function actionShowgroup()
    {
        $request = Yii::$app->request;
        $get = $request->get('Name');
        Yii::$app->session->set('groupSh', $get );
        return $this->render('showGroup');
    }
    //action delete student from group. Student is received status new
    public function actionDeletefromgroup($idDeleteStudent, $nameGroup)
    {
        $group = new Group(Group::findByGroupName($nameGroup));
        $group->deleteStudentFromGroup($idDeleteStudent);
        return $this->redirect(Yii::$app->request->referrer);

    }
    /*
    * Action delete all group
    * Students are received status new
    */
    public function actionDeleteallgroup($nameGroup)
    {
        $group = new Group(Group::findByGroupName($nameGroup));
        $group->deleteGroup($nameGroup);
        return $this->redirect(['profile/index']);

    }
}
