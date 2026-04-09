<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notification */
?>
<div class="notification-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'message:ntext',
            'type',
            'created_at',
            'updated_at',
            'created_by',
        ],
    ]) ?>

</div>
