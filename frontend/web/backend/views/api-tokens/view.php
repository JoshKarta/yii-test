<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApiTokens */
?>
<div class="api-tokens-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'token',
            'name',
            'permissions:ntext',
            'expires_at',
            'last_used_at',
            'is_active',
            'created_at',
        ],
    ]) ?>

</div>
