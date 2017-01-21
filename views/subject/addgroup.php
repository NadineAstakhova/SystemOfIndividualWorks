<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 16.04.2016
 * Time: 18:57
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<div id='modalContentAddGroup'>
    <?php $form = ActiveForm::begin(['id' => 'form-create']); ?>
    <h4>Choose group</h4>
    <?php
        $groups = \app\models\Group::getGroups();
        $arrayGroups = array();
        foreach($groups as $group){
            array_push($arrayGroups,$group['name']);
        }
        array_shift($arrayGroups);
    ?>
    <?= $form->field($model, 'namesGroups')->dropDownList($arrayGroups)->label(false);
        Yii::$app->session->set('arrayGroupNames', $arrayGroups );
    ?>
    <h4>Or create new group</h4>
    <?= $form->field($model, 'nameNewGroup')->label( 'Example: Group_1') ?>
    <div>
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
