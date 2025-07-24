<?php

use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="booking-form">
    <?php $form = ActiveForm::begin(['id' => 'booking-form']); ?>

    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= $form->field($model, 'date')->textInput(['id' => 'booking-date', 'readonly' => true]) ?>
    <!-- <?= $form->field($model, 'start_time')->input('time', ['step' => 600]) ?> -->
    <!-- <?= $form->field($model, 'end_time')->input('time', ['step' => 600]) ?> -->

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'start_time')->widget(TimePicker::class, [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'minuteStep' => 30,
                    'defaultTime' => '09:00',
                ]
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_time')->widget(TimePicker::class, [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'minuteStep' => 30,
                    'defaultTime' => '09:00',
                ]
            ]); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>