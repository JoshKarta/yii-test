<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MockPosts */
?>
<div class="mock-posts-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'author',
            'title',
            'slug',
            'content:ntext',
            'status',
            'published_at',
            'created_at',
            'updated_at',
            'type',
        ],
    ]) ?>

</div>
