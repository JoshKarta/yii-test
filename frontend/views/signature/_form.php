<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Signature */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="signature-form">

    <?php $form = ActiveForm::begin(['action' => ['signature/upload']]); ?>

    <label class="w-100">Signature</label>
    <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #ccc; border-radius: 8px;"></canvas><br>
    <button type="button" id="clear" class="btn btn-light">Clear</button>
    <?= Html::activeHiddenInput($model, 'file_name', ['id' => 'signature-input']) ?>


    <!-- <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div> -->
    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Create' : 'Update',
            ['class' => 'btn btn-primary mt-3']
        ) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
let canvas = document.getElementById("signature-pad");
let signaturePad = new SignaturePad(canvas);

document.getElementById("clear").addEventListener("click", () => {
    signaturePad.clear();
});

$('form').on('beforeSubmit', function (e) {
    if (!signaturePad.isEmpty()) {
        let dataURL = signaturePad.toDataURL();
        $('#signature-input').val(dataURL);
    } else {
        alert('Please provide a signature.');
        return false; // prevent submit
    }

    return true; // allow submit
});
JS;
$this->registerJs($js);
?>