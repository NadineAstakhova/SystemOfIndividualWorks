<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'layout'=>'main',
	'defaultRoute'=>'site/index',
    'language' => 'en-En',
    'charset'=>'UTF-8',
    'components' => [
        'i18n' => [
            'translations' => [
                '*' => [
                    'sourceLanguage' => 'ru-RU']
                ]
            ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '0000',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\AbstractUser',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'class' => 'yii\web\User',
        ],
        'subject' => [
            'class' => 'app\models\Subject',

        ],
        'errorHandler' => [
            'errorAction' => 'site/error',

        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'exportInterval' => 1,
                    'categories' => ['user'],
                    'prefix' => function ($message) {
                        $user = Yii::$app->has('professor', true) ? Yii::$app->get('professor') : null;
                        $userID = $user ? $user->getId(false) : '-';
                        return "[$userID]";
                    },
                    'logFile' => 'app\runtime\logs\logs.log',
                    'logVars' => []

                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],

    'modules' => [
        'admin' => [
            'class' => 'app\models\Professor',
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin','1234567'],
            'components' => [
                'manager' => [
                    'userClass' => 'app\models\Professor',
                    'loginFormClass' => 'app\models\EntryForm',
                    'resetPasswordForm' => 'app\models\ResetPasswordForm',
                    'PasswordResetRequestForm' => 'app\models\PasswordResetRequestForm',
                ],
            ],
            'controllerMap' => [
                'registration' => 'app\controllers\SiteController'
            ],
        ],
        'user' => [
            'class' => 'app\models\Student',
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin','1234567'],
            'components' => [
                'manager' => [
                    'userClass' => 'app\models\Student',
                    'loginFormClass' => 'app\models\LoginForm',
                    'resetPasswordForm' => 'app\models\ResetPasswordForm',
                    'PasswordResetRequestForm' => 'app\models\PasswordResetRequestForm',
                ],
            ],
            'controllerMap' => [
                'registration' => 'app\controllers\SiteController'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
   $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
   ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

}

return $config;
