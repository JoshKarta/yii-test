<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'workflowSource' => [
            'class' => 'cornernote\workflow\manager\components\WorkflowDbSource',
        ],
        'notificationManager' => [
            'class' => 'common\components\NotificationManager',
        ],
    ],
    'modules' => [
        'workflow' => [
            'class' => 'cornernote\workflow\manager\Module',
        ],
    ],
];
