<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ConfigCategory $model */

$this->title = 'Create Config Category';
$this->params['breadcrumbs'][] = ['label' => 'Config Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
