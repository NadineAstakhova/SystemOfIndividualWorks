<?php

namespace app\controllers;

use app\models\AddGroup;
use app\models\AddTask;
use app\models\CreateSubject;
use app\models\Task;
use app\models\UpdateSubject;
use Yii;
use app\models\Subject;
use yii\web\NotFoundHttpException;
use app\models\UploadSubject;

class SubjectController extends \yii\web\Controller
{

    public $idS;

    public function actionIndex()
    {
        $isStudent = Yii::$app->session->get('AccessST');
        if (Yii::$app->user->isGuest || $isStudent == 1)
            throw new NotFoundHttpException('You cannot see this page. Please login.');

        $name = $_GET["Name"];
        $id = $_GET["idSubject"];
        $statusSort = $_GET["sortBy"];
        Yii::$app->session->set('sortBy', $statusSort );
        $request = Yii::$app->request;
        $get = $request->get('idSubject');
        $this->idS = $id;
        \Yii::trace(  $id, 'name');
        \Yii::trace(  $name, 'id model');
        $group = Yii::$app->session->get('group');
        \Yii::trace(  $group, 'group');
        $groups = Yii::$app->session->get('studentsGroup');
        \Yii::trace(  $groups, 'studentsGroup');

        return $this->render('index', [
            'model' => $this->findModel($get),
        ]);
    }

    /**
     * $id - GET
     * Finds the Subject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Subject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($id)
    {
        if (($model = Subject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    //upload file of work by student
    public function actionUpload()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $subject = $this->findModel($get);
        $model = new UploadSubject($subject);

        $getStudent = $request->get('idStudent');
        $getStudentName = $request->get('StudentName');
        Yii::$app->session->set('getStudent', $getStudent );
        Yii::$app->session->set('getStName', $getStudentName );
        \Yii::trace(  $getStudentName, 'student');

       if ($model->load(Yii::$app->request->post())  && $model->upload() ) {
           return $this->redirect(Yii::$app->request->referrer); }
       else
       {
            return $this->renderAjax('upload', [
                'model' => $model,
            ]);
       }
    }

    //call model for update name
    public function actionUpdatename()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $subject = $this->findModel($get);
        $model = new UpdateSubject($subject);

        if ($model->load(Yii::$app->request->post()) && $model->updateName()) {
            return $this->redirect(Yii::$app->request->referrer); }
        else
        {
            \Yii::trace('no update', $model->Name);
            return $this->renderAjax('updatename', [
                'model' => $model,
            ]);
        }
    }

    //call model for update data of student work
    public function actionUpdaterow()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $getTask = $request->get('idTask');
        Yii::$app->session->set('getTask', $getTask );

        $subject = $this->findModel($get);
        $model = new UpdateSubject($subject);

        if ($model->load(Yii::$app->request->post()) && $model->update())
        {
            \Yii::trace('update', $model->Name);
            return $this->redirect(['subject/index', 'Name' => $subject['Name'], 'idSubject' => $subject['idSubject']]); }
        else
        {
            \Yii::trace('no update', $model->Name);
            return $this->renderAjax('updaterow', [
                'model' => $model,
            ]);
        }
    }

    //call model for update date for subject task
    public function actionUpdatedate()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $subject = $this->findModel($get);
        $model = new UpdateSubject($subject);

        $getTask = $request->get('idTask');
        Yii::$app->session->set('getIdTask', $getTask );
        $getNameTask = Task::findIdentity($getTask);
        Yii::$app->session->set('getNameTask', $getNameTask['Name_of_task']);

        if ($model->load(Yii::$app->request->post()) && $model->updateDate())
        {
            \Yii::trace('update', $model->Name);
            return $this->redirect(Yii::$app->request->referrer);
        }
        else
        {
            \Yii::trace('no update', $model->Name);
            return $this->renderAjax('updatedate', [
                'model' => $model,
            ]);
        }
    }

    //call model for create new subject
    public function actionCreate()
    {
        $model = new CreateSubject();
        if ($model->load(Yii::$app->request->post()) && $model->creatingSubject())
        {
            return $this->redirect(Yii::$app->request->referrer); }
        else
        {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    //call model for add group
    public function actionAddgroup()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $subject = $this->findModel($get);
        $model = new AddGroup($subject);

        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            return $this->redirect(Yii::$app->request->referrer); }
        else
        {
            return $this->renderAjax('addgroup', [
                'model' => $model,
            ]);
        }
    }

    //call model for add task
    public function actionAddtask()
    {
        $request = Yii::$app->request;
        $get = $request->get('id');
        $subject = $this->findModel($get);
        $model = new AddTask($subject);

        if ($model->load(Yii::$app->request->post()) && $model->add()) {
            return $this->redirect(Yii::$app->request->referrer); }
        else
        {
            return $this->renderAjax('addtask', [
                'model' => $model,
            ]);
        }
    }

    //download file of work
    public function actionDownload($name)
    {
        $path = Yii::getAlias('/basic') . '/uploads';
        $file = $path.'/'.$name;
        return \Yii::$app->response->sendFile(Yii::$app->basePath.'\uploads\\'.basename($name));
    }


    //call model for delete group
    public function actionDeletegroup($idDeleteGroup, $idSubject)
    {
        $subject = $this->findModel($idSubject);
        $subject->deleteGroupFromSubject($idDeleteGroup, $idSubject);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
