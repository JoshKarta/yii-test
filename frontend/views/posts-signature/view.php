<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PostsSignature */
?>
<div class="posts-signature-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'post_id',
            'user_id',
            'signature_base64:ntext',
            'created_at',
            'signed_at',
            'updated_at',
            'version',
            'ip_address',
            'user_agent',
        ],
    ]) ?>

</div>
