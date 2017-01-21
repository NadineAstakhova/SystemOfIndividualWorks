<?php


// comment out the following two lines when deployed to production
use yii\web\Application;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// ğåãèñòğàöèÿ çàãğóç÷èêà êëàññîâ Composer
require(__DIR__ . '/../vendor/autoload.php');
// ïîäêëş÷åíèå ôàéëà êëàññà Yii
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

// çàãğóçêà êîíôèãóğàöèè ïğèëîæåíèÿ
$config = require(__DIR__ . '/../config/web.php');

// ñîçäàíèå è êîíôèãóğàöèÿ ïğèëîæåíèÿ, à òàêæå âûçîâ ìåòîäà äëÿ îáğàáîòêè âõîäÿùåãî çàïğîñà
(new Application($config))->run();
