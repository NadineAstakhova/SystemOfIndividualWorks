<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">
    <h1>Error O_o</h1>
    <?php
        if (Html::encode($this->title) === "Exception"){
            echo  nl2br(Html::encode($message));
        }
        else
        {
            echo "<div class='alert alert-warning'>";
            echo nl2br(Html::encode($message));
            echo " </div>";
        }
    ?>
</div>
