<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 15.04.2016
 * Time: 21:36
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id='modalContentCreate'>
<?php $form = ActiveForm::begin(['id' => 'form-create']); ?>
<?= $form->field($model, 'Name') ?>
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
<?php ActiveForm::end(); ?>
</div>