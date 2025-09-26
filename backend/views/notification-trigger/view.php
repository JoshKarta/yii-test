<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationTrigger */
?>
<div class="notification-trigger-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'route',
            'notification_key',
            'request_type',
            'link_template',
        ],
    ]) ?>

</div>
