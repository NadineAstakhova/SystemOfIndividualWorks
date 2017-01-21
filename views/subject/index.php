<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 15.02.2016
 * Time: 22:09á
 * Template of subject outputting
 *  <?= Html::a(Yii::t('app', 'Upload'), ['upload', ['id'=>$model->id]], ['class' => 'btn btn-primary']) ?>
 *  <?= Html::beginForm(['subject/upload', 'id' => $id], 'post', ['enctype' => 'multipart/form-data']) ?>
<?= Html::submitButton('Submit', ['class' => 'submit']) ?>
<?= Html::endForm() ?>
 */
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Subject;
?>

<div class="container-fluid">
    <?php
        $this->registerJsFile(
            'scripts/index.js',
            ['depends'=>'app\assets\AppAsset']
        );
        $groups =Subject::getGroups($model->idSubject);
        $tasks =Subject::getAllTasks($model->idSubject);
    ?>
    <div class="row">
        <div class="top">
            <img src="../views/images/topProf.jpg"  alt="The top Subject" id="top_picture" >
            <div class="form-group">
                <?php  echo Html::a( 'BACK', Yii::$app->request->referrer,['class' => 'button_back'] );?>
            </div>
            <div class="top_title" id="title_group">
                <?php echo $model->Name;?>
            </div>
            <div class="top_button_group" id="button_subject">
                <?php
                echo "<a value=".Url::to('index.php?r=subject%2Fupdatename')."&id=".$model->idSubject." id = 'modalButtonName' >".
                    Html::img('../views/images/button_update_sub.png',
                        ['alt'=>'update', 'id'=>'img_button'])."</a>";

                Modal::begin([
                    'header' => '<h4>Update name</h4>',
                    'id' => 'modalUpdateName',
                    'size' => 'modal-sm'
                ]);?>
                <div id='modalContent'>
                </div>
                <?php
                Modal::end();?>
                <?php
                echo "<a value=".Url::to('index.php?r=subject%2Faddgroup')."&id=".$model->idSubject." id = 'modalButtonGroup' >".
                    Html::img('../views/images/button_add_group.png',
                        ['alt'=>'update', 'id'=>'img_button'])."</a>";

                Modal::begin([
                    'header' => '<h4>Add Group</h4>',
                    'id' => 'modal',
                    'size' => 'modal-lg'
                ]);?>
                <div id='modalContent'>
                </div>
                <?php
                Modal::end();?>
                <?php
                echo "<a value=".Url::to('index.php?r=subject%2Faddtask')."&id=".$model->idSubject." id = 'modalButtonTask' >".
                    Html::img('../views/images/button_add_task.png',
                        ['alt'=>'update', 'id'=>'img_button'])."</a>";
                Modal::begin([
                    'header' => '<h4>Add Task</h4>',
                    'id' => 'modalTask',
                    'size' => 'modal-lg'
                ]);?>
                <div id='modalContent'>
                </div>
                <?php
                Modal::end();
                ?>
                <a href="#statistics"><img src="../views/images/button_stat.png"  alt="The section of Statistics" id="img_button" ></a>
            </div>
        </div>
    </div>

    <div class="row" id="subject_info">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class = "tableThNew" id='sub_th'>Student</th>
                <th class = "tableThNew" id='sub_th'>Individual work</th>
                <th class = "tableThNew" id='sub_th'>Status</th>
                <th class = "tableThNew" id='sub_th'>Completion date</th>
                <th class = "tableThNew" id='sub_th'>Mark</th>
                <th class = "tableThNew" id='sub_th'>Update</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $statusSort = Yii::$app->session->get('sortBy');
            echo $model->statusName;
            if (    $statusSort === null)
                Subject::outputTable($groups,$model->idSubject, $model->Name);

            else {
                echo "<div class='sort'>";
                echo "Filter by <span id='by_sort'>".$statusSort.'</span>'.
                    Html::a(Yii::t('app',Html::img('../views/images/remove.png', ['alt'=>'delete', 'class'=>'icons', 'id'=>'icons_remove'])),
                        ['index', 'Name' => $model->Name, 'idSubject'=>$model->idSubject ]);
                echo "</div>";
                Subject::outputSortTable($groups, $model->idSubject, $model->Name, $statusSort);
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="section_statistics" id="statistics">
            <img src="../views/images/section_statistics.jpg"  alt="The section of statistics" id="top_picture" >
            <div class="top_title" id="title_section_stat">
                Deadline&Statistics
            </div>
        </div>
    </div>
    <div class="row" >
        <div class = 'col-xs-12 col-sm-6 col-lg-5' id="title_deadline">
            <h1>Deadline</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>Work</th>
                    <th>Deadline</th>
                    <th>Update date</th>
                </tr>
                </thead>
                <tbody>
            <?php
            foreach ($tasks as $task)
            {
                echo "<tr>";
                echo "<td>";
                echo Html::a(Yii::t('app',$task['Name_of_task']),
                    ['index', 'Name' => $model->Name, 'idSubject'=>$model->idSubject, 'sortBy' => $task['Name_of_task'] ]);
                echo "</td>";
                echo "<td>";
                echo " ".$task['Date']." ";
                echo "</td>";
                echo "<td class='tableTrNew'>";
                echo "<a value=".Url::to('index.php?r=subject%2Fupdatedate')."&id=".$model->idSubject."&idTask=".$task['idList_of_task'].
                    " class = 'modalButtonDate'>".Html::img('../views/images/edit.png', ['alt'=>'change', 'class'=>'icons_update'])."</a>";
                echo "</td>";
                Modal::begin([
                    'header' => "Update",
                    'id' => 'modalDate',
                    'size' => 'modal-sm'
                ]);?>
                <div id='modalContent'>
                </div>
                <?php
                Modal::end();
                echo "</tr>";
            }?>
                </tbody>
            </table>
        </div>
        <div class = 'col-xs-12 col-sm-6 col-lg-7' id="title_deadline">
            <h1>Statistics</h1>
        <?php
            echo "Task Passed";
            echo "<br>";
            Subject::getStatistics($model->idSubject);
        ?>
        </div>
    </div>
</div>
<div id="scroller" class="b-top" style="display: none;">
    <img src="../views/images/icon_to_top.png"  alt="Top" class="b-top-but">
</div>
