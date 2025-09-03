<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Roles */
?>
<div class="roles-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
        ],
    ]) ?>

</div>
