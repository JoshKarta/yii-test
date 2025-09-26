<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Notification;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationTrigger */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-trigger-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'notification_key')->widget(Select2::class, [
        'data' => ArrayHelper::map(
            Notification::find()->where(['enabled' => 1])->all(),
            'key',
            function ($model) {
                return $model->key . ' - ' . $model->title;
            }
        ),
        'options' => [
            'placeholder' => 'Select a notification...',
            'id' => 'notification-key-select'
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => '#ajaxCrudModal'
        ]
    ]); ?>

    <?= $form->field($model, 'route')->textInput([
        'maxlength' => true,
        'id' => 'route-input',
        'placeholder' => 'Route will be auto-filled based on notification key'
    ]) ?>

    <?= $form->field($model, 'request_type')->dropDownList(['ANY' => 'ANY', 'AJAX' => 'AJAX', 'NON_AJAX' => 'NON AJAX',], ['prompt' => '']) ?>

    <?= $form->field($model, 'link_template')->textInput(['maxlength' => true]) ?>


    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
$(document).ready(function() {
    // Auto-fill route based on notification key selection
    $('#notification-key-select').on('change', function() {
        var selectedKey = $(this).val();
        if (selectedKey) {
            // Convert notification key to route format
            // For example: 'document_created' becomes 'document/create'
            var route = selectedKey.replace(/_created$/, '/create')
                                  .replace(/_updated$/, '/update')
                                  .replace(/_deleted$/, '/delete')
                                  .replace(/_viewed$/, '/view')
                                  .replace(/_/, '/');

            $('#route-input').val('/'+ route);
        } else {
            $('#route-input').val('');
        }
    });

    // If editing existing record, don't auto-fill route
    if ($('#route-input').val()) {
        // Disable auto-fill for existing records
        $('#notification-key-select').off('change');
    }
});
");
?>