<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Form */
?>
<div class="form-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'description:ntext',
            'json',
            'created_by',
            'created_at',
        ],
    ]) ?>

</div>
