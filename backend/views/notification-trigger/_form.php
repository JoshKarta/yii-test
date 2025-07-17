<?php

use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Notification;


// Notification keys dropdown
$notificationKeys = ArrayHelper::map(Notification::find()->select(['key', 'title'])->all(), 'key', 'title');
?>

<div>
    <?php
    $form = ActiveForm::begin();
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'notification_key')->widget(Select2::class, [
                'data' => $notificationKeys,
                'options' => [
                    'placeholder' => 'Select notification key...',
                    'id' => 'notification-key'
                ],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'route')->textInput([
                'id' => 'notification-route',
                'readonly' => true,
            ]); ?>
        </div>
    </div>

    <?= $form->field($model, 'trigger_type')->dropDownList([
        'create' => 'Create',
        'update' => 'Update',
    ], [
        'prompt' => 'Select trigger type...',
        'id' => 'trigger-type'
    ]); ?>

    <?= $form->field($model, 'model_class')->textInput([
        'placeholder' => 'e.g. common\\models\\Document',
        'value' => $model->model_class ?: 'common\models\\',
    ]); ?>

    <?= $form->field($model, 'model_id_param')->textInput(['placeholder' => 'e.g. id']); ?>

    <?= $form->field($model, 'fields')->textarea([
        'rows' => 4,
        'placeholder' => '{"document_name": "title", "created_by": "createdBy.username"}'
    ]); ?>

    <?= $form->field($model, 'link_template')->textInput(['placeholder' => '/document/view?id={id}']); ?>

    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']); ?>

    <?php
    ActiveForm::end();
    ?>

</div>

<?php
// Register the JS
$js = <<<JS
$('#notification-key').on('change', function() {
    let selectedKey = $(this).val();
    $('#notification-route').val(selectedKey);
});
JS;

$this->registerJs($js);
?>