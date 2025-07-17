<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Api $model */

$tables = Yii::$app->db->schema->tableNames;
$columnsUrl = Url::to(['api/columns']);
$relationsUrl = Url::to(['api/relations']);
$this->registerJs("
    $('#table-select').on('change', function() {
        var table = $(this).val();

        $.ajax({
            url: '$columnsUrl',
            data: { table: table },
            dataType: 'json',
            success: function(data) {
                // Remove all options
                $('#field-select').empty();
                // Add new options
                data.forEach(function(c) {
                    var option = new Option(c, c, false, false);
                    $('#field-select').append(option);
                });
                // Trigger change to update Select2
                $('#field-select').trigger('change');
            },
            error: function(xhr, status, error) {
                alert('Failed to fetch columns: ' + error);
            }
        });
        
        $.ajax({
            url: '$relationsUrl',
            data: { table: table },
            dataType: 'json',
            success: function(data) {
                $('#relation-textarea').val(JSON.stringify(data, null, 2));
                // $('#relation-textarea').empty().select2({
                //     data: data.map(c => ({ id: c, text: c }))
                // });
            },
            error: function(xhr, status, error) {
                alert('Failed to fetch columns: ' + error);
            }
        });
    });

    // Ensure Select2 shows selected values on page load
    $('#field-select').trigger('change');
");

// Get all columns for the selected table (for update, $model->table_name is set)
$allFields = [];

if ($model->table_name && Yii::$app->db->schema->getTableSchema($model->table_name)) {
    $allFields = Yii::$app->db->schema->getTableSchema($model->table_name)->getColumnNames();
}

// dump($model->getAllowedFieldsArray());
// dump($allFields);

?>

<div class="api-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'table_name')->widget(Select2::class, [
        'data' => array_combine($tables, $tables),
        'options' => ['prompt' => 'Select table', 'id' => 'table-select'],
    ]) ?>

    <?= $form->field($model, 'allowed_fields')->widget(Select2::class, [
        'data' => array_combine($allFields, $allFields), // show all columns as options
        'value' => $model->getAllowedFieldsArray(),      // pre-select saved values
        'options' => ['multiple' => true, 'id' => 'field-select'],
        'pluginOptions' => ['allowClear' => true, 'dropdownParent' => '#ajaxCrudModal'],
    ]) ?>

    <!-- <?= $form->field($model, 'relations')->textarea(['rows' => 4, 'id' => 'relation-textarea']) ?> -->

    <?= $form->field($model, 'rate_limit')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save API', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>