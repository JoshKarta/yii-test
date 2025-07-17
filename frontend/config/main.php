<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'workflow' => [
            'class' => 'cornernote\workflow\manager\Module',
        ],
    ],
    'components' => [
        // 'view' => [
        //     'renderers' => [
        //         'twig' => [
        //             'class' => 'yii\twig\ViewRenderer',
        //             'cachePath' => '@runtime/Twig/cache',
        //             'options' => [
        //                 'debug' => true,
        //                 'auto_reload' => true,
        //                 'strict_variables' => false,
        //                 'autoescape' => 'html',
        //                 'optimizations' => 0,
        //                 'trim_blocks' => false, // ⬅️ Important
        //                 'autoescape' => false,  // ⬅️ Optional: allow raw HTML content
        //                 'autoescape_strategy' => false,
        //                 'debug' => true,
        //                 'autoescape' => false,
        //             ],
        //             'globals' => [
        //                 'html' => '\yii\helpers\Html',
        //             ],
        //         ],
        //     ],
        // ],
        'i18n' => [
            'translations' => [
                'yii2-ajaxcrud' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2ajaxcrud/ajaxcrud/messages',
                    'sourceLanguage' => 'en',
                ],
            ]
        ],
        'workflowSource' => [
            'class' => 'cornernote\workflow\manager\components\WorkflowDbSource',
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
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
                'GET api/<resource:[\w\-]+>' => 'api/index',
                'PUT api/<resource:[\w\-]+>' => 'api/update',
            ],
        ],
    ],
    'params' => $params,
    'as notificationListener' => [
        'class' => \common\components\NotificationListener::class,
    ],
    // 'on beforeRequest' => function ($event) {
    //     Yii::$app->attachBehavior('notificationListener', [
    //         'class' => \common\components\NotificationListener::class
    //     ]);
    // },
    // 'on afterRequest' => function ($event) {
    //     Yii::$app->detachBehavior('notificationListener');
    // },
];
