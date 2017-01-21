<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 30.04.2016
 * Time: 23:07
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php
$groups = \app\models\Group::getGroups();
$arrayGroups = array();
foreach($groups as $group){
    array_push($arrayGroups,$group['name']);
}
array_shift($arrayGroups);
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'group')->dropDownList($arrayGroups)->label(false);
Yii::$app->session->set('arrayGroupNamesSt', $arrayGroups );
?>

<?=Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary'])?>
    <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
<?php ActiveForm::end(); ?>