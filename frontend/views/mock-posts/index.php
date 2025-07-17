<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap5\Modal;
use kartik\grid\GridView;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MockPostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mock Posts';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
// Dynamically generate columns from the first row of apiData
// dd($apiData);

$columns = [];

// Add checkbox column as the first column
$columns[] = [
    'class' => 'kartik\grid\CheckboxColumn',
    'checkboxOptions' => function ($model, $key, $index, $column) {
        // Use a unique value for the checkbox, e.g. 'id' or 'post_id'
        return ['value' => $model['id'] ? $model['id'] : $model->id];
    },
];

if (!empty($apiData) && is_array($apiData)) {
    $firstRow = reset($apiData);
    foreach ($firstRow as $key => $value) {
        $columns[] = [
            'attribute' => $key,
            'label' => ucfirst(str_replace('_', ' ', $key)),
            'value' => function ($model) use ($key) {
                $val = $model[$key] ?? null;
                return is_array($val) ? json_encode($val) : $val;
            },
        ];
    }
}
?>

<div class="mock-posts-index">
    <div id="ajaxCrudDatatable">
        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Data from External API </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <?= GridView::widget([
                            'id' => 'crud-datatable',
                            'rowOptions' => function ($model, $key, $index, $grid) {
                                return ['data-key' => $key];
                            },
                            'dataProvider' => $dataProvider,
                            // 'filterModel' => $searchModel,
                            'pjax' => true,
                            // 'columns' => require(__DIR__ . '/_columns.php'),
                            'columns' => $columns,
                            'toolbar' => [
                                [
                                    'content' =>
                                    // Html::a(
                                    //     Yii::t('yii2-ajaxcrud', 'Create New'),
                                    //     ['create'],
                                    //     ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Mock Posts', 'class' => 'btn btn-outline-primary']
                                    // ) .
                                    Html::a(
                                        '<i class="fa fa-redo"></i>',
                                        [''],
                                        ['data-pjax' => 1, 'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]
                                    )
                                    // '{toggleData}' .
                                    // '{export}'
                                ],
                            ],
                            'striped' => true,
                            'hover' => true,
                            'condensed' => true,
                            'responsive' => true,
                            'panel' => [
                                'type' => 'default',
                                'heading' => '</i> <b> API DATA </b>',
                                'before' => '<em>* ' . Yii::t('yii2-ajaxcrud', 'Resize Column') . '</em>',
                                'after' => BulkButtonWidget::widget([
                                    'buttons' =>
                                    // Html::a(
                                    //     '<i class="fa fa-trash"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', 'Delete All'),
                                    //     ["bulkdelete"],
                                    //     [
                                    //         'class' => 'btn btn-danger btn-xs',
                                    //         'role' => 'modal-remote-bulk',
                                    //         'data-confirm' => false,
                                    //         'data-method' => false, // for override yii data api
                                    //         'data-request-method' => 'post',
                                    //         'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                                    //         'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
                                    //     ]
                                    // ) .
                                    Html::a(
                                        '<i class="fa fa-plus"></i>&nbsp; Add Selected to MockPosts',
                                        ['add-selected'], // You need to create this action in your controller
                                        [
                                            'class' => 'btn btn-outline-success ms-2',
                                            'role' => 'modal-remote-bulk',
                                            'data-confirm' => false,
                                            'data-method' => false,
                                            'data-request-method' => 'post',
                                            'data-confirm-title' => 'Add',
                                            'data-confirm-message' => 'Are you sure you want to add the selected items to MockPosts?'
                                        ]
                                    ),
                                ]) .
                                    '<div class="clearfix"></div>',
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Data in model
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <?= GridView::widget([
                            'id' => 'model-datatable',
                            'dataProvider' => $modelDataProvider,
                            'filterModel' => $searchModel,
                            'pjax' => true,
                            'columns' => require(__DIR__ . '/_columns.php'),
                            'toolbar' => [
                                [
                                    'content' =>
                                    Html::a(
                                        Yii::t('yii2-ajaxcrud', 'Create New'),
                                        ['create'],
                                        ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Posts', 'class' => 'btn btn-outline-primary']
                                    ) .
                                        Html::a(
                                            '<i class="fa fa-redo"></i>',
                                            [''],
                                            ['data-pjax' => 1, 'class' => 'btn btn-outline-success', 'title' => Yii::t('yii2-ajaxcrud', 'Reset Grid')]
                                        ) .
                                        '{toggleData}' .
                                        '{export}'
                                ],
                            ],
                            'striped' => true,
                            'hover' => true,
                            'condensed' => true,
                            'responsive' => true,
                            'panel' => [
                                'type' => 'default',
                                'heading' => '<i class="fa fa-list"></i> <b>' . $this->title . '</b>',
                                'before' => '<em>* ' . Yii::t('yii2-ajaxcrud', 'Resize Column') . '</em>',
                                'after' => BulkButtonWidget::widget([
                                    'buttons' => Html::a(
                                        '<i class="fa fa-trash"></i>&nbsp; ' . Yii::t('yii2-ajaxcrud', 'Delete All'),
                                        ["bulkdelete"],
                                        [
                                            'class' => 'btn btn-danger btn-xs',
                                            'role' => 'modal-remote-bulk',
                                            'data-confirm' => false,
                                            'data-method' => false, // for overide yii data api
                                            'data-request-method' => 'post',
                                            'data-confirm-title' => Yii::t('yii2-ajaxcrud', 'Delete'),
                                            'data-confirm-message' => Yii::t('yii2-ajaxcrud', 'Delete Confirm')
                                        ]
                                    ),
                                ]) .
                                    '<div class="clearfix"></div>',
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "", // always need it for jquery plugin
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => false,
    ],
    "options" => [
        "tabindex" => false
    ]
]) ?>
<?php Modal::end(); ?>
<!-- <?php
        $this->registerJs(
            'var gridData = ' . json_encode($dataProvider->allModels) . '; $("#crud-datatable").data("kvGridData", gridData);'
        );
        ?> -->
<?php
// $this->registerJs(<<<JS
// $(document).on('click', '.btn-outline-success', function(e) {
//     e.preventDefault();
//     var keys = $('#crud-datatable').yiiGridView('getSelectedRows');
//     var allData = $('#crud-datatable').data('kvGridData') || {};
//     var selectedRows = [];
//     for (var i = 0; i < keys.length; i++) {
//         if (allData[keys[i]]) {
//             selectedRows.push(allData[keys[i]]);
//         }
//     }
//     $.ajax({
//         url: $(this).attr('href'),
//         type: 'POST',
//         data: {rows: JSON.stringify(selectedRows)},
//         success: function(data) {
//             if (data.success) {
//                 alert(data.message); // Or use a toast/notification
//                 location.reload();   // Optionally reload the page or grid
//             }
//         }
//     });
// });
// JS);
?>