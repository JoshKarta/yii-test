<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationRole */
?>
<div class="notification-role-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'notification_id',
            'role',
        ],
    ]) ?>

</div>
