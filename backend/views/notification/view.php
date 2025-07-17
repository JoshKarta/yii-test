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
            'key',
            'title',
            'message_template:ntext',
            'channels',
            'enabled',
            'send_email:email',
            'created_at',
        ],
    ]) ?>

</div>
