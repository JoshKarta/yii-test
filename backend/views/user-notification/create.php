<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserNotification $model */

$this->title = 'Create User Notification';
$this->params['breadcrumbs'][] = ['label' => 'User Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-notification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
