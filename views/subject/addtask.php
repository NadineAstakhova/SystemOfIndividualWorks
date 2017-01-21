<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.04.2016
 * Time: 12:52
 */
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id='modalContent'>
    <?php $form = ActiveForm::begin(['id' => 'form-create']); ?>
    <h4>Enter type and number of task</h4>
    <?= $form->field($model, 'Name_of_task')->label( 'Example: Lab_1') ?>
    <h4>Enter date of task</h4>
    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
        'language' => 'en',
        'removeButton' => false,
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'pluginOptions' => [
            'orientation' => 'top right',
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true
        ]
    ])->label(false) ?>
    <div>
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
