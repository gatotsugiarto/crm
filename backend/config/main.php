<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'datecontrol'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '*'], // sementara pakai '*' untuk semua IP
        ],
        'auth' => [
            'class' => 'backend\modules\auth\Module',
        ],
        'master' => [
            'class' => 'backend\modules\master\Module',
        ],
        'payroll' => [
            'class' => 'backend\modules\payroll\Module',
        ],
        'satupayroll' => [
            'class' => 'backend\modules\satupayroll\Module',
        ],
        'productprice' => [
            'class' => 'backend\modules\productprice\Module',
        ],
        'sales' => [
            'class' => 'backend\modules\sales\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to  
            // use your own export download action or custom translation 
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',

            // format tampil ke user
            'displaySettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:d-m-Y',
            ],

            // format simpan ke DB
            'saveSettings' => [
                \kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d',
            ],

            // auto convert
            'autoWidget' => false,
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'authTimeout' => 1800, // 30 menit
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'currencyCode' => 'IDR',
            'locale' => 'id_ID',
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y H:i:s',
            'thousandSeparator' => '.',
            'decimalSeparator' => ',',
        ],
    ],
    'params' => $params,
];
