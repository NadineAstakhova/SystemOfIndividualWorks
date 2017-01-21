<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 18.07.2016
 * Time: 23:40
 */
use yii\helpers\Html;
?>
<div class="background_main">
    <img src="../views/images/topProf.jpg"  alt="Background" id="background_main_picture" >
    <div class="top_title" id="title_main">
        HELP WITH YOUR WORKS
    </div>
    <div class='top_info_main'>
    <?php echo "We make your hard work easier"; ?>
    </div>
    <div class="top_button_group" id="button_subject">
        <?php
            echo Html::a(Yii::t('app',Html::img('../views/images/button_login_main.png', ['alt'=>'login', 'id'=>'img_button_main'])),['login']);
            echo Html::a(Yii::t('app',Html::img('../views/images/button_signup.png', ['alt'=>'signup', 'id'=>'img_button_main'])),['signup']);
            echo Html::a(Yii::t('app',Html::img('../views/images/button_about.png', ['alt'=>'about', 'id'=>'img_button_main'])),['about']);
        ?>
    </div>
</div>
