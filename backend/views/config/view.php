<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model common\models\Config */

$this->title = $model->key;

// For AJAX requests, don't render the full layout
if (Yii::$app->request->isAjax) {
    echo $this->render('_view_content', [
        'model' => $model,
    ]);
    return;
}

$this->params['breadcrumbs'][] = ['label' => 'Configurations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// $enableForgotPassword = (bool)Yii::$app->configuration->get('enableForgotPassword', false, 'frontend');

// $debugConfig = \common\models\Config::find()
//     ->joinWith(['category'])
//     ->where(['config_category.name' => 'frontend', 'config.key' => 'enableForgotPassword'])
//     ->one();

// echo "Database value: " . ($debugConfig ? ($debugConfig->value ? 'true' : 'false') : 'not found') . "<br>";
// echo "Cached value: " . ($enableForgotPassword ? 'true' : 'false') . "<br>";
?>
<div class="config-view">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs text-info me-2"></i>
                    Configuration: <?= Html::encode($this->title) ?>
                </h5>
                <div>
                    <?= Html::a(
                        '<i class="fas fa-edit me-1"></i> Configs',
                        ['index',],
                        ['class' => 'btn btn-dark btn-sm' . ($model->is_system ? ' disabled' : '')]
                    ) ?>
                    <?= Html::a(
                        '<i class="fas fa-edit me-1"></i> Update',
                        ['update', 'id' => $model->id],
                        ['class' => 'btn btn-primary btn-sm' . ($model->is_system ? ' disabled' : '')]
                    ) ?>
                    <?= Html::a(
                        '<i class="fas fa-trash me-1"></i> Delete',
                        ['delete', 'id' => $model->id],
                        [
                            'class' => 'btn btn-danger btn-sm' . ($model->is_system ? ' disabled' : ''),
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this configuration?',
                                'method' => 'post',
                            ],
                        ]
                    ) ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?= $this->render('_view_content', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>