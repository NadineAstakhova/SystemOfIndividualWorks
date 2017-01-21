<?php


// comment out the following two lines when deployed to production
use yii\web\Application;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// ����������� ���������� ������� Composer
require(__DIR__ . '/../vendor/autoload.php');
// ����������� ����� ������ Yii
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

// �������� ������������ ����������
$config = require(__DIR__ . '/../config/web.php');

// �������� � ������������ ����������, � ����� ����� ������ ��� ��������� ��������� �������
(new Application($config))->run();
