<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationUser */
?>
<div class="notification-user-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'notification_id',
            'user_id',
            'message:ntext',
            'link',
            'is_read',
            'created_at',
        ],
    ]) ?>

</div>
