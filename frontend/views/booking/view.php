<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */
?>
<div class="booking-view">
    <?= Html::button('<i class="fas fa-arrow-left"></i> Back', [
        'class' => 'btn btn-outline-secondary mb-2',
        'onclick' => 'history.back();',
        'title' => 'Return to previous page',
    ]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'description:ntext',
            'start_time',
            'end_time',
            'created_at',
        ],
    ]) ?>

</div>