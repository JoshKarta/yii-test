<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PostType */
?>
<div class="post-type-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'layout_template:ntext',
        ],
    ]) ?>

</div>
