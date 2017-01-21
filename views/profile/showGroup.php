<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 14.06.2016
 * Time: 20:06
 */
use app\models\Group;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
$group = Yii::$app->session->get('groupSh');
$students = Group::getStudentsInGroup($group);
?>
<div class="container-fluid">

    <div class="row">
        <div class="top">
            <img src="../views/images/topProf.jpg"  alt="The top Group" id="top_picture" >
            <div class="form-group">
                <?php  echo Html::a( 'BACK', Yii::$app->request->referrer,['class' => 'button_back'] );?>
            </div>
            <div class="top_title" id="title_group">
                <?php echo "".$group?>
            </div>
            <div class="top_button_group">
            <?php
                echo Html::a(Yii::t('app',  Html::img('../views/images/delete_group.png', ['alt'=>'delete', 'class'=>'delete_icon_group'])), ['deleteallgroup',  'nameGroup'=>$group]);
            ?>
            </div>
        </div>
    </div>

    <div class="row">
        <table class="table table-hover" id="content_group">
            <thead>
                <th class = "tableThNew">Students</th>
                <th class = "tableThNew">Delete from group</th>
                <th class = "tableThNew">Change group</th>
            </thead>
            <tbody>
            <?php
                foreach ($students as $student)
                {
                    echo "<tr>";
                    echo "<td>";
                    echo $student['name'].' '.$student['surname'];
                    echo '<br> <font size="2%" color="#828282"> Last visit: '. date_format(date_create($student['last_visit']),"d.m.y H:i").'</font>';
                    echo "</td>";
                    echo "<td class='td_group_table'>";
                    echo Html::a(Yii::t('app',
                        Html::img('../views/images/delete.png', ['alt'=>'delete', 'class'=>'icons'])),
                        ['deletefromgroup', 'idDeleteStudent'=>$student['id'], 'nameGroup'=>$group]);
                    echo "</td>";
                    echo "<td class='td_group_table'>";
                    echo "<a value=".Url::to('index.php?r=profile%2Fchangegroup').
                        "&idStudent=".$student['id']." class = 'modalButtonChangeG' >".
                        Html::img('../views/images/edit.png', ['alt'=>'change', 'class'=>'icons'])."</a>";
                    echo "</td>";
                    echo "</tr>";
                    Modal::begin([
                        'header' => "Change group",
                        'id' => 'modalChangeGroup',
                        'size' => 'modal-lg'
                    ]);
            ?>
                    <div id='modalContent'>
                    </div>
            <?php
                    Modal::end();
                }
            ?>
            </tbody>
        </table>
    </div>
</div>