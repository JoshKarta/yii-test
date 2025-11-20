<?php

use common\components\CreateNewButton;
use common\widgets\RecordSearchWidget;
use yii\helpers\Html;
use yii\bootstrap5\Modal;
use kartik\grid\GridView;
use yii2ajaxcrud\ajaxcrud\CrudAsset;
use yii2ajaxcrud\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configuration Management';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="config-index">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-cogs text-primary me-2"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">Manage application configurations and settings</p>
        </div>
        <div class="col-md-6 text-end">
            <?= Html::a('<i class="fas fa-sync-alt me-1"></i> Clear Cache', ['clear-cache'], [
                'class' => 'btn btn-outline-warning me-2',
                'role' => 'modal-remote',
                'data-request-method' => 'post',
            ]) ?>
            <?= Html::a(
                Yii::t('yii2-ajaxcrud', 'Create New'),
                ['create'],
                ['role' => 'modal-remote', 'title' => Yii::t('yii2-ajaxcrud', 'Create New') . ' Persoons', 'class' => 'btn btn-outline-primary']
            ) ?>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="ajaxCrudDatatable">
                <?= GridView::widget([
                    'id' => 'crud-datatable',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pjax' => true,
                    'pjaxSettings' => ['options' => ['id' => 'crud-datatable-pjax']],
                    'columns' => require(__DIR__ . '/_columns.php'),
                    'toolbar' => [
                        [
                            'content' =>
                            Html::a(
                                '<i class="fas fa-redo"></i>',
                                [''],
                                ['data-pjax' => 1, 'class' => 'btn btn-outline-success', 'title' => 'Reset Grid']
                            ) .
                                '{toggleData}'
                        ],
                    ],
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    'panel' => [
                        'type' => 'default',
                        'heading' => '<i class="fas fa-list me-2"></i> Configuration List',
                        'before' => 'Yapa',
                        'after' => BulkButtonWidget::widget([
                            'buttons' => Html::a(
                                '<i class="fas fa-trash me-1"></i> Delete Selected',
                                ["bulk-delete"],
                                [
                                    'class' => 'btn btn-danger btn-xs',
                                    'role' => 'modal-remote-bulk',
                                    'data-confirm' => false,
                                    'data-method' => false,
                                    'data-request-method' => 'post',
                                    'data-confirm-title' => 'Delete',
                                    'data-confirm-message' => 'Are you sure you want to delete the selected configurations?'
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

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",
    "size" => "modal-lg",
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