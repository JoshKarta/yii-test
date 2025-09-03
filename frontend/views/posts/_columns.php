<?php

use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    // [
    //     'class' => '\kartik\grid\DataColumn',
    //     'attribute' => 'author_id',
    // ],
    // [
    //     'class' => '\kartik\grid\DataColumn',
    //     'attribute' => 'updated_by',
    // ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'title',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'format' => 'raw',
        'attribute' => 'status',
        'value' => function ($model) {
            return '<div class="badge bg-danger">' . $model->workflowStatus->label . '</div>';
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'post_type_id',
        'value' => 'postType.name'
    ],
    // [
    //     'class' => '\kartik\grid\DataColumn',
    //     'attribute' => 'slug',
    // ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'content',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'published_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'created_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'updated_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'signature_id',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete} {signature} {twig}',
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'signature' => function ($url, $model, $key) {
                return \yii\helpers\Html::a(
                    '<i class="fas fa-pen-nib"></i>',
                    ['/posts-signature/create', 'id' => $model->id],
                    [
                        'role' => 'modal-remote',
                        'title' => Yii::t('app', 'Add Signature'),
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-sm btn-outline-warning',
                        'data-request-method' => 'get'
                    ]
                );
            },
            'twig' => function ($url, $model, $key) {
                return \yii\helpers\Html::a(
                    '<i class="fa-solid fa-file-pdf"></i>',
                    ['/posts/twig', 'id' => $model->id],
                    [
                        'role' => '',
                        'title' => Yii::t('app', 'Add Signature'),
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-sm btn-outline-info',
                        // 'data-request-method' => 'get'
                    ]
                );
            },
        ],
        'viewOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'View'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Update'), 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => [
            'role' => 'modal-remote',
            'title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false,
            'data-method' => false, // for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
        ],
    ],

];
