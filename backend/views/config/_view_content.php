<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model common\models\Config */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'category',
        'key',
        [
            'attribute' => 'value',
            'format' => 'raw',
            'value' => function ($model) {
                $value = $model->getTypedValue();

                if (is_bool($value)) {
                    return $value
                        ? '<span class="badge bg-success"><i class="fas fa-check me-1"></i>True</span>'
                        : '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>False</span>';
                } elseif (is_array($value)) {
                    return '<pre class="bg-light p-2 rounded"><code>' .
                        Json::encode($value, JSON_PRETTY_PRINT) .
                        '</code></pre>';
                } else {
                    return '<code class="bg-light p-1 rounded">' .
                        Html::encode($value) .
                        '</code>';
                }
            },
        ],
        'type',
        'description:ntext',
        [
            'attribute' => 'is_system',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->is_system
                    ? '<span class="badge bg-primary"><i class="fas fa-shield-alt me-1"></i>System Configuration</span>'
                    : '<span class="badge bg-secondary"><i class="fas fa-user-edit me-1"></i>Custom Configuration</span>';
            },
        ],
        'sort_order',
        'created_at:datetime',
        'updated_at:datetime',
        [
            'attribute' => 'createdBy.username',
            'label' => 'Created By',
        ],
        [
            'attribute' => 'updatedBy.username',
            'label' => 'Updated By',
        ],
    ],
]) ?>