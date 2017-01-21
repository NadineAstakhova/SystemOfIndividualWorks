<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 19.04.2016
 * Time: 14:26
 */
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<?php $Task = Yii::$app->session->get('getNameTask'); ?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<h4>Enter new date for <?php echo $Task?>:</h4>
<?= $form->field($model, 'Date')->widget(DatePicker::classname(), [
    'language' => 'en',
    'removeButton' => false,
    'type' => DatePicker::TYPE_COMPONENT_APPEND,
    'pluginOptions' => [
        'orientation' => 'bottom left',
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]
])->label(false) ?>
<div>
<?=Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary'])?>
    <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
</div>
<?php ActiveForm::end(); ?>

