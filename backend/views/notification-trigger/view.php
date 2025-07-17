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
            'trigger_type',
            'notification_key',
            'model_class',
            'model_id_param',
            'fields',
            'link_template',
        ],
    ]) ?>

</div>