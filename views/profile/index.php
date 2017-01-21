<?php

use app\models\Group;
use yii\base\InvalidConfigException;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
?>
<div class="container-fluid">
    <?php
    $this->registerJsFile(
        'scripts/index.js',
        ['depends'=>'app\assets\AppAsset']
    );
    try
    {
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                $model['name'],
                'name',
                'surname',
            ],
        ]);
    ?>

    <div class="row">
        <div class="top">
            <img src="../views/images/topProf.jpg"  alt="The top Professor" id="top_picture" >
            <div class="top_title" id="title_group">
                <?php echo   $model['name'].' '.$model['surname'];?>
            </div>
            <div class="top_button_group" id="button_profile">
                <a href="#new_works"><img src="../views/images/button_new_works.png"  alt="The section of New Works" id="img_button" ></a>
                <a href="#new_students"><img src="../views/images/button_new_st.png"  alt="The section of New Student" id="img_button" ></a>
                <a href="#subjects"><img src="../views/images/button_subjects.png"  alt="The section of Subjects" id="img_button" ></a>
                <a href="#groups"><img src="../views/images/button_groups.png"  alt="The section of  Groups" id="img_button" ></a>
            </div>
        </div>
    </div>
    <div class="row" id="profile_info">
        <div class="col-xs-5 col-sm-4 col-lg-5">
            <?php
            echo "Your login: ".$model['login'];
            echo "<br>Your email: ".$model['email'];
            echo "<div id='btn_info'>";
            echo "<a value=".Url::to('index.php?r=profile%2Fpassword-change')."&login=".$model['login']."
                    id = 'btn_change_pass' >".Html::img('../views/images/button_login.png',
                    ['alt'=>'update', 'class'=>'button_change_info'])."</a>";
            ?>
            <?php
            Modal::begin([
                'header' => '<h2>Change Password&Login</h2>',
                'id' => 'modalUpdatePass',
                'size' => 'modal-lg'
            ]);?>
            <div id='modalContentUpdatePass'>
            </div>
            <?php
            Modal::end();
            echo "</div>";
            ?>
        </div>
        <div class="col-xs-5 col-sm-4 col-lg-5">
            <?php
            echo "Your phone: ".$model['phone'];
            echo "<br>Your skype: ".$model['skype'];
            echo "<div id='btn_info'>";
            echo "<a value=".Url::to('index.php?r=profile%2Fupdate')."&login=".$model['login']."  id = 'modalButtonUpdateInf' >".Html::img('../views/images/button_update.png', ['alt'=>'update', 'class'=>'button_change_info'])."</a>";
            ?>
            <?php
            Modal::begin([
                'header' => '<h2>Update</h2>',
                'id' => 'modalUpdateInf',
                'size' => 'modal-lg'
            ]);?>
            <div id='modalContentUpdate'>
            </div>
            <?php
            Modal::end();
            echo "</div>";
            ?>
        </div>
    </div>

    <div class="row">
        <div class="section_new_works" id="new_works">
            <img src="../views/images/section_works.jpg"  alt="The section of new works" id="top_picture" >
            <div class="top_title" id="title_section">
                New Works
            </div>
        </div>
    </div>
    <div class="row">
        <table class="table table-hover">
            <thead>
            <th class = "tableThNew">Subject</th>
            <th class = "tableThNew">Work</th>
            </thead>
            <tbody>
        <?php
            $newWorks = \app\models\Professor::getAllNewWork($model['login']);
            if(count($newWorks) === 0)
            {
                echo "<tr  class='tableTrNew'>";
                echo "<td colspan=2 align='center'>";
                echo "No new work for you";
                echo "</td>";
                echo "</tr>";
            }
            else
            {
                foreach ($newWorks as $newWork)
                {
                    echo "<tr  class='tableTrNew'>";
                    echo "<td>";
                    echo $newWork['Name'];
                    echo "</td>";
                    echo "<td>";
                    $str = strpos($newWork['File'], "_");
                    $row = substr($newWork['File'], $str + 1);
                    echo $row;
                    echo "</td>";
                    echo "</tr>";
                }
            }
        ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="section_new_students" id="new_students">
            <img src="../views/images/section_students.jpg"  alt="The section of new students" id="top_picture" >
            <div class="top_title"  id="title_section">
                New Students
            </div>
        </div>
    </div>
    <div class="row">
        <table class="table table-hover">
            <thead>
            <th class = "tableThNew">Name</th>
            <th class = "tableThNew" id="thC">Select group</th>
            </thead>
            <tbody>
        <?php
            $newStudents = \app\models\Student::getAllNewStudent();
            foreach ($newStudents as $newStudent)
            {
                echo "<tr class='tableTrNew'>";
                echo "<td>";
                echo $newStudent['Name'].' '.$newStudent['Surname'];
                echo "</td>";
                echo "<td align='center'>";
                echo "<a value=".Url::to('index.php?r=profile%2Fchangegroup')."&idStudent=".$newStudent['id'].
                    " class = 'modalButtonChangeG' >".
                    Html::img('../views/images/edit.png', ['alt'=>'change', 'class'=>'icons'])."</a>";
                echo "</td>";
                echo "</tr>";
                Modal::begin([
                    'header' => "Change group",
                    'id' => 'modalChangeGroup',
                    'size' => 'modal-lg'
                ]);
        ?>
                <div id='modalContent'></div>
        <?php
                Modal::end();
            }
        ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="section_subjects" id="subjects">
            <img src="../views/images/section_subject.jpg"  alt="The section of subjects" id="top_picture" >
            <div class="top_title" id="title_section_sub" >
                Subjects
            </div>
            <div class="top_button_group">
        <?php
            echo "<a value=".Url::to('index.php?r=subject%2Fcreate')." class='btn_create' id = 'modalButton' >".
                Html::img('../views/images/button_create.png', ['alt'=>'create', 'class'=>'icon_create_subject'])."</a>";
            Modal::begin([
                'header' => '<h4>Create Subject</h4>',
                'id' => 'modalCreate',
                'size' => 'modal-sm'
            ]);
        ?>
            <div id='modalContentCreate'></div>
            <?php Modal::end();?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="list_of_subjects">
            Your subjects:
        <?php
            $arraySubjects = \app\models\Professor::getAllSubject($model['login']);
            Yii::$app->session->set('idProfForCreate', \app\models\Professor::getIdProfessor($model['login']));
            $count = count($arraySubjects);
            if ($count > 5) echo "<article>";
        ?>
        <ul>
        <?php
            foreach ($arraySubjects as $subject)
            {
                echo "<li>";
                echo Html::a(Yii::t('app', "".$subject['Name']), ['/subject/index', 'Name' => "".$subject['Name'], 'idSubject' =>\app\models\Professor::selectSubject($subject['Name'],  $model['login'])]);
                echo "</li>";
            }
        ?>
        </ul>
        <?php  if ($count > 5) echo "</article>"; ?>
        </div>
    </div>

    <div class="row">
         <div class="section_group" id="groups">
             <img src="../views/images/section_groups.jpg"  alt="The section of group" id="top_picture" >
             <div class="top_title" id="title_section">
                  Groups
             </div>
         </div>
    </div>
    <div class="row">
        <div class="list_of_groups">
            Groups which already exist
        <?php
            $arrAllGroups = Group::getGroups();
            $count = count($arrAllGroups);
            if ($count > 6) echo "<article>";
        ?>
            <ul>
        <?php
            $arrayGroups = array();
            foreach($arrAllGroups as $group){
                array_push($arrayGroups,$group['name']);
            }
            array_shift($arrayGroups);
            foreach ($arrayGroups as $group){
                echo "<li>";
                echo Html::a(Yii::t('app', "".$group), ['/profile/showgroup', 'Name' => "".$group]);
                echo "</li>";
            }
        ?>
            </ul>
        <?php  if ($count > 6) echo "</article>"; ?>
        </div>
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
<div id="scroller" class="b-top" style="display: none;">
    <img src="../views/images/icon_to_top.png"  alt="Top" class="b-top-but">
</div>
