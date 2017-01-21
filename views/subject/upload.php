<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UploadSubject */
/* @var $form ActiveForm*/

?>
<?php ?>

    <div id='modalContent'>
    <?php
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data' ] ]);
    //получим модель предмета для загрузки
    $subject = \app\models\Subject::findBySubjectName($model->Name);
    //получим все задания для данного предмета
    $tasks = \app\models\Subject::getAllTasks($subject->idSubject);
    //передадим ссылку на первичеый ключ предмета
    Yii::$app->session->set('idSubjectForTask', $subject->idSubject );
    Yii::$app->session->set('nameSubjectForTask', $subject->Name );
    //массив заданий для вывода
    $arrayTasks = array();
    foreach($tasks as $task){
        array_push($arrayTasks,$task['Name_of_task']);
    }
    //передача массива в модель
    Yii::$app->session->set('arrayTasksNames', $arrayTasks );
    ?>
        <h4>Choose task</h4>
        <?= $form->field($model, 'task')->dropDownList($arrayTasks)->label(false);?>
        <h4>Choose file</h4>
    <?= $form->field($model, 'file')->fileInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary']) ?>
        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>

    </div>


    <?php ActiveForm::end(); ?>
</div>
<!-- subject-upload -->
