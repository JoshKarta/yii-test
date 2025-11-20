<?php

use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Config */
/* @var $form kartik\form\ActiveForm */
?>

<div class="config-form">
    <?php $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3]
    ]); ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-cogs me-2"></i>
                Configuration Details
            </h5>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'category')->widget(Select2::classname(), [
                'data' => \common\models\Config::getCategories(),
                'options' => ['placeholder' => 'Select category...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>

            <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->widget(Select2::classname(), [
                'data' => \common\models\Config::getTypes(),
                'options' => ['placeholder' => 'Select data type...'],
                'pluginOptions' => [
                    'allowClear' => false,
                ],
            ]) ?>

            <?= $form->field($model, 'value')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>

            <?php if (!$model->isNewRecord && $model->is_system): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>System Configuration:</strong> This is a system configuration and cannot be modified.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group mt-4">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton(
                '<i class="fas fa-save me-2"></i>' .
                    ($model->isNewRecord ? 'Create' : 'Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
            <?= Html::a(
                '<i class="fas fa-times me-2"></i>Cancel',
                ['index'],
                ['class' => 'btn btn-secondary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>