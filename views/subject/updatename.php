<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 19.04.2016
 * Time: 13:05
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<h1><?= $form->field($model, 'Name')->textInput(['maxlength' => true])->label(false)?></h1>
<div>
    <?=Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary'])?>
    <?php echo "   ";?>
    <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
</div>
<?php ActiveForm::end(); ?>

