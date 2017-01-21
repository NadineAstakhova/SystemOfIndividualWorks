<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\UploadedFile;
use kartik\file\FileInput;
?>
<div class="user-profile-update">
    <div class="user-form">
        <?php
            $form = ActiveForm::begin(['id' => 'update-form', 'options' => ['enctype' => 'multipart/form-data']]);
            $lg = Yii::$app->session->get('getLogin');
            $model->name = \app\models\Professor::getName($lg);
            $model->surname = \app\models\Professor::getSurname($lg);
            $model->skype = \app\models\Professor::getSkype($lg);
            $model->phone = \app\models\Professor::getPhone($lg);
        ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'skype')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary']) ?>
            <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>