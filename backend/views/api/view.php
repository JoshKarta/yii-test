<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Api */
?>
<div class="api-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'table_name',
            'allowed_fields:ntext',
            'relations:ntext',
            'is_active',
            'token',
            'rate_limit',
            'rate_limit_remaining',
            'rate_limit_reset_at',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
