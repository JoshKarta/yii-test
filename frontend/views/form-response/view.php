<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\FormResponse */
?>
<div class="form-response-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'form_id',
            'response_json',
            'submitted_at',
        ],
    ]) ?>

</div>
