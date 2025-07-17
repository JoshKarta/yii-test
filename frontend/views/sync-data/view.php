<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SyncData */
?>
<div class="sync-data-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'change_id',
            'table_name',
            'pk',
            'action',
            'change_time',
        ],
    ]) ?>

</div>
