<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 19.04.2016
 * Time: 11:44
 */
use app\models\Subject;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php
$groups =Subject::getGroups($model->idSubject);
$tasks =Subject::getAllTasks($model->idSubject);
?>
<?php
$this->registerJsFile(
    'scripts/index.js',
    ['depends'=>'app\assets\AppAsset']
);
?>

<div class="modalContentStudent">
<?php
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

$idTask = Yii::$app->session->get('getTask');
$idT =  Yii::$app->session->set('getTaskId', $idTask );;
$arrayWorks = array();

foreach ($groups as $group) {
    $studentInGroup = Subject::getStudentsInGroup($model->idSubject, $group['idgroup']);
    foreach ($studentInGroup as $students) {
        if ($students['idInd_work'] == $idTask) {
            echo "Student: " . $students['name'] . " " . $students['surname'];
            echo "<br><br>";
            $str=strpos($students['File'], "_");
            $row=substr($students['File'],  $str+1);
            echo "Task: " . $row;
            echo "<br><br>";
            $model->Status =$students['Status'];
            echo "Status: ".$form->field($model, 'Status')->dropDownList(\app\models\Individual_Work::$arrStatus,
                    array('prompt'=>$model->Status, 'style'=>'width:300px; margin-left: 33%; margin-top:3%; margin-bottom:-1%'))->label(false);
            echo "<br>";
            $date = date_create($students['Completion_date']);
            echo "Date: ".date_format( $date,"d.m.y");
            echo "<br><br>";
            $model->Mark = $students['Mark'];
            echo  "Mark: ".$form->field($model, 'Mark')->textInput(['maxlength' => true,'style'=>'width:300px; margin-left: 33%; margin-top:3%'])->label(false);
            echo "<br>";
            echo Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-primary']);
        }
    }
 }
?>
    <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>
</div>
<?php ActiveForm::end(); ?>
