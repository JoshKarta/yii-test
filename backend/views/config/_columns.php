<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'categoryName',
        'label' => 'Category',
        'value' => function ($model) {
            return $model->category ? $model->category->name : '<span class="text-muted">N/A</span>';
        },
        'format' => 'raw',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => \common\models\ConfigCategory::getCategories(),
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'allowClear' => true,
                'placeholder' => 'Select category...',
                'width' => '100%',
            ],
        ],
        'contentOptions' => ['style' => 'font-weight: 600;'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'key',
        'contentOptions' => ['style' => 'font-family: monospace;'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'value',
        'format' => 'raw',
        'value' => function ($model) {
            $value = $model->getTypedValue();

            if (is_bool($value)) {
                return $value
                    ? '<span class="badge bg-success">True</span>'
                    : '<span class="badge bg-danger">False</span>';
            } elseif (is_array($value)) {
                return '<code>' . Html::encode(implode(', ', $value)) . '</code>';
            } elseif ($value === null) {
                return '<span class="text-muted">NULL</span>';
            } elseif (strlen($value) > 50) {
                return '<span title="' . Html::encode($value) . '">' . Html::encode(substr($value, 0, 50) . '...') . '</span>';
            } else {
                return Html::encode($value);
            }
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'filter' => \common\models\Config::getTypes(),
        'filterInputOptions' => [
            'class' => 'form-control',
            'prompt' => 'All types',
        ],
        'contentOptions' => ['style' => 'text-transform: capitalize;'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'is_system',
        'filter' => [0 => 'No', 1 => 'Yes'],
        'format' => 'raw',
        'value' => function ($model) {
            return $model->is_system
                ? '<span class="badge bg-primary">System</span>'
                : '<span class="badge bg-secondary">Custom</span>';
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'sort_order',
        'width' => '80px',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => 'true',
        'template' => '{view} {update} {delete}',
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => ['title' => 'View', 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-success'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip', 'class' => 'btn btn-sm btn-outline-primary'],
        'deleteOptions' => [
            'role' => 'modal-remote',
            'title' => 'Delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'data-confirm' => false,
            'data-method' => false,
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Delete',
            'data-confirm-message' => 'Are you sure you want to delete this configuration?'
        ],
    ],
];
