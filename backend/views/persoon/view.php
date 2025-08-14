<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Persoon */
?>
<div class="persoon-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'persoonid',
            'regnr',
            'naam',
            'voornaam',
            'idnr',
            'verzekeringskaartnr',
            'geboortedatum',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
