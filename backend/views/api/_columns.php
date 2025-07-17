<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->registerJs(<<<JS
function showToast(message) {
    let toast = $('<div class="copy-toast"></div>').text(message).appendTo('body');
    toast.fadeIn(200).delay(2000).fadeOut(400, function() {
        $(this).remove();
    });
}

$(document).on('click', '.copy-token-btn', function() {
    const token = $(this).data('token');

    // Create a temporary input to copy text
    const tempInput = $("<input>");
    $("body").append(tempInput);
    tempInput.val(token).select();
    document.execCommand("copy");
    tempInput.remove();

    showToast("Token copied to clipboard!");
});
JS);

$this->registerCss(<<<CSS
.copy-toast {
    position: fixed;
    top: 30px;
    right: 30px;
    background: #4caf50;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 9999;
    display: none;
}
CSS);


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
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'table_name',
    ],
    [
        'attribute' => 'allowed_fields',
        'value' => function ($model) {
            return is_array($model->allowed_fields)
                ? implode(', ', $model->allowed_fields)
                : $model->allowed_fields;
        },
    ],
    // [
    //     'attribute' => 'relations',
    //     'value' => function ($model) {
    //         return is_array($model->relations)
    //             ? json_encode($model->relations, JSON_PRETTY_PRINT)
    //             : $model->relations;
    //     },
    //     'format' => 'ntext',
    // ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'is_active',
    ],
    [
        'attribute' => 'token',
        'format' => 'raw',
        'value' => function ($model) {
            $token = Html::encode($model->token);
            return <<<HTML
            <div class="input-group">
                <input type="text" class="form-control token-input" value="{$token}" readonly>
                <button class="btn btn-outline-secondary copy-token-btn" type="button" data-token="{$token}">
                   <i class="fa fa-copy"></i>
                </button>
            </div>
        HTML;
        },
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'rate_limit',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'rate_limit_remaining',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'rate_limit_reset_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'created_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'updated_at',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
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
