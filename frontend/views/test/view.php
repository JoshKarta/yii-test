<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Test */
?>
<div class="test-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idtest',
            'value',
        ],
    ]) ?>

</div>
