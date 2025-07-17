<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\switchinput\SwitchInput; // Add this line

/* @var $this yii\web\View */
/* @var $model common\models\SyncData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sync-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'table_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pk')->textInput() ?>

    <?= $form->field($model, 'action')->dropDownList(['INSERT' => 'INSERT', 'UPDATE' => 'UPDATE', 'DELETE' => 'DELETE',], ['prompt' => '']) ?>

    <?= $form->field($model, 'change_time')->textInput() ?>

    <?= $form->field($model, 'synced')->widget(SwitchInput::class, [
        'pluginOptions' => [
            'onText' => 'Yes',
            'offText' => 'No',
        ],
    ]) ?>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>