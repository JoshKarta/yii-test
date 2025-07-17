<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Sign';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the Signature page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>

    <div class="mt-5">
        <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #ccc; border-radius: 8px;"></canvas>
        <br>
        <button type="button" class="btn btn-light" id="clear">Clear</button>
        <input type="hidden" name="User[signature]" id="signature">
    </div>

</div>

<script>
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);

    document.getElementById('clear').addEventListener('click', () => {
        signaturePad.clear();
    });

    // When the form is submitted
    document.querySelector('form').addEventListener('submit', function() {
        if (!signaturePad.isEmpty()) {
            document.getElementById('signature').value = signaturePad.toDataURL();
        }
    });
</script>