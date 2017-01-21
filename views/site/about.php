<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
?>
<div class="background_main" id="about">
    <img src="../views/images/topProf.jpg"  alt="Background" id="background_about_picture" >
    <div class="form-group">
        <?php  echo Html::a( 'BACK', Yii::$app->request->referrer,['class' => 'button_back_home'] );?>
    </div>
    <div class="top_title" id="title_about">
        HELP WITH YOUR WORKS
    </div>
    <div class="top_info_about">
        Проект курсовой работы на тему:
        <br>Система контроля результатов самостоятельной работы студентов
    </div>
        <div class="block_1" id="list">
        <span id="list_title">ЧТО ЭТО ДАЁТ ПРЕПОДАВАТЕЛЮ?</span>
        <ul>
            <li>упорядоченное хранение данных  присланных работ </li>
            <li>лёгкое курирование групп студентов</li>
            <li>удобное администрирование собственных предметов</li>
            <li>вы всегда и везде знаете где можно найти нужную работу </li>
            <li>и некоторые другие плюшки</li>
        </ul>
        </div>
        <div class="block_2" id="list">
        <span id="list_title">ЧТО ЭТО ДАЁТ СТУДЕНТУ?</span>
        <ul>
            <li>упорядоченное хранение работ</li>
            <li>быстрый и своевременный доступ к результатам оценивания</li>
            <li>можно всегда проверить крайний срок сдачи работы</li>
            <li>возможность заменить файл работы на исправленный</li>
            <li>и некоторые другие плюшки</li>
        </ul>
    </div>
</div>
