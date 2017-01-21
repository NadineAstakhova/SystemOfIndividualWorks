<?php
use yii\helpers\Html;

/* @var $this yii\web\View */


$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/password-reset', 'token' => $user->password_reset_token]);
?>

    ������������, <?= Html::encode($user->username) ?>!

    �������� �� ������, ����� ������� ������:

<?= Html::a(Html::encode($resetLink), $resetLink) ?>