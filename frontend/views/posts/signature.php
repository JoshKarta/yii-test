<?php

use common\models\Signature;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Posts */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Sign Post"
?>

<div class="posts-form">

    <h2><?= $model->title ?></h2>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6, 'disabled' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <label class="w-100">Signature</label>
    <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #ccc; border-radius: 8px;"></canvas><br>
    <button type="button" id="clear" class="btn btn-light">Clear</button>
    <?= Html::activeHiddenInput($model, 'file_name', ['id' => 'signature-input']) ?>


    <div class="form-group">
        <?= Html::submitButton('Sign <i class="fas fa-signature"></i>', ['class' => 'btn btn-warning']) ?>
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