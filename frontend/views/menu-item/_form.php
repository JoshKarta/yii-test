<?php

use kartik\form\ActiveForm;
use kartik\switchinput\SwitchInput;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\models\MenuItem $model */
/** @var yii\widgets\ActiveForm $form */

// Parent Items
$parentItems = \common\models\MenuItem::find()
    ->where(['parent_id' => null])
    ->select(['label', 'id'])
    ->indexBy('id')
    ->column();

?>

<div class="menu-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
        </div>
    </div>


    <?= $form->field($model, 'location')->dropDownList([
        'backend' => 'Backend',
        'frontend' => 'Frontend',
        'both' => 'Both',
    ]) ?>

    <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
        'data' => $parentItems,
        'options' => [
            'placeholder' => 'No Parent (Top Level)',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => '#ajaxCrudModal'
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'icon_type')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'target')->dropDownList([
                '_self' => 'Same Tab',
                '_blank' => 'New Tab',
            ], ['prompt' => 'Select Target']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'heading')->widget(SwitchInput::classname(), [
                'type' => SwitchInput::CHECKBOX,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'visible')->widget(SwitchInput::classname(), [
                'type' => SwitchInput::CHECKBOX,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'only_developers')->widget(SwitchInput::classname(), [
                'type' => SwitchInput::CHECKBOX,
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>