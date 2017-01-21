<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 12.04.2016
 * Time: 23:22
 */
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
?>

<?php
    $this->registerJsFile(
        'scripts/index.js',
        ['depends'=>'app\assets\AppAsset']
    );
?>

<div class="container-fluid">
    <div class="row">
        <div class="top">
            <img src="../views/images/topProf.jpg"  alt="The top Student" id="top_picture" >
            <div class="top_title">
                <?php
                try {
                    DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            $model['name'],
                            'name',
                            'surname',
                            'status',
                        ],
                    ]) ;
                echo "".$model['name']." ".$model['surname']."";
                ?>
            </div>
            <div class="top_info_student">
                Group:
                <?php  echo \app\models\Student::getNameGroup($model['login']);?>
                <br>Date of registration:
                <?php  echo  \app\models\Student::getRegistrationDate();?>
                <br>Last visit:
                <?php  echo \app\models\Student::getLastVisit();?>
            </div>
       </div>
    </div>

    <div class="row">
        <div id="title_subjects_student">
            <?php
                echo "Subjects";
            ?>
        </div>
    </div>

    <div class="row">
        <table class="table table-hover" id="table_student_subjects">
            <tbody>
        <?php
            $subjects = \app\models\Student::getSubjectGroups($model['login']);
            if (count($subjects) < 1)
                echo "<h4>You have not yet assigned to a group. Please wait.</h4>";
            foreach ($subjects as $subject)
            {
                echo "<tr>";
                echo "<td colspan=4 align='center'  id='td_student_table'>";
                echo "<a value=".Url::to('index.php?r=subject%2Fupload&id='.$subject['idSubject']).
                    '&idStudent='.\app\models\Student::getIdStudent($model['login'])['id'].
                    "&StudentName=".$model['name']."_".$model['surname']."
                    id = 'modalButton' class='btn-lg btn-default sub_upload' style ='margin:5px' >
                    ".$subject['name']."<i class='glyphicon glyphicon-upload'></i></a>";
                echo "</td>";
                echo "</tr>";

                $tasks = \app\models\Student::getMySubjectTasks($subject['idSubject']);

                Modal::begin([
                    'header' => '<h4>Upload</h4>',
                    'id' => 'modal',
                    'size' => 'modal-lg'
                ]);
        ?>
                <div id='modalContent'>
                </div>
        <?php
                Modal::end();
                $allSubjectsTask = \app\models\Subject::getAllTasks($subject['idSubject']);
                $arr = array();

                foreach ($allSubjectsTask as $subjectTasks)
                {
                    echo "<tr>";
                    echo "<td>";
                    $fl=0;
                    $tmpFile = "";
                    $tmpStatus = "";
                    $tmpMark = "";
                    $tmpComDate = "";
                    foreach ($tasks as $stTask)
                        if ($stTask["FK_Task"]==$subjectTasks['idList_of_task'])
                        {
                            $fl=1;
                            $str=strpos($stTask['File'], "_");
                            $row=substr($stTask['File'],  $str+1);
                            $tmpFile = $row;
                            $tmpStatus = $stTask['Status'];
                            $tmpMark = $stTask['Mark'];
                            $tmpComDate = $stTask['Completion_date'];
                            $date=date_create($tmpComDate);
                        }
                        if ($fl==0)
                        {
                            echo $subjectTasks['Name_of_task'];
                            echo "</td>";
                            echo "<td align='center'>";
                            $dateT = date_create($subjectTasks['Date']);
                            echo date_format( $dateT,"d.m.y") ;
                            echo "</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "</tr>";
                        }
                        else
                        {
                            echo $tmpFile;
                            echo "</td>";
                            echo "<td>";
                            echo  $tmpStatus;
                            echo "</td>";
                            echo "<td>";
                            echo date_format( $date,"d.m.y") ;
                            echo "</td>";
                            echo "<td>";
                            echo  $tmpMark;
                            echo "</td>";
                            echo "</tr>";
                        }
                }
            }
        ?>
            </tbody>
        </table>
    </div>

    <?php
        }
    catch(InvalidConfigException $e)
    {
        echo "user not found";
        Yii::$app->user->logout();
    }
    ?>
</div>
