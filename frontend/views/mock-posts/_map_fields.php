<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $mockPostsAttributes array */
/** @var $apiAttributes array */
?>

<h4>Map API Fields to MockPosts Columns</h4>
<?php $form = ActiveForm::begin(['id' => 'mapping-form']); ?>
<table class="table table-bordered">
    <tr>
        <th>MockPosts Attribute</th>
        <th>API Attribute</th>
    </tr>
    <?php foreach ($mockPostsAttributes as $attr => $val): ?>
        <tr>
            <td><?= Html::encode($attr) ?></td>
            <td>
                <?= Html::dropDownList("mapping[$attr]", null, array_combine($apiAttributes, $apiAttributes), ['prompt' => 'Select API field', 'class' => 'form-control']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?= Html::hiddenInput('rows', json_encode($rows)) ?>
<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>