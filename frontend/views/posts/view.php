<?php

use yii\widgets\DetailView;
use common\models\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\Posts */
?>
<div class="posts-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'author_id',
            'updated_by',
            'title',
            'slug',
            'content:ntext',
            'status' => [
                'label' => 'Status',
                'format' => 'raw',
                'value' =>
                '<div class="badge bg-danger">'
                    . $model->workflowStatus->label .
                    '</div>',
            ],
            'published_at',
            'created_at',
            'updated_at',
            'post_type_id',
        ],
    ]) ?>

</div>