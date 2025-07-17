<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use dominus77\sweetalert2\Asset;

/* @var $this yii\web\View */
/* @var $model common\models\PostsSignature */
/* @var $form yii\widgets\ActiveForm */

$postId = Yii::$app->request->get('id') ?: $model->post_id;

?>

<div class="posts-signature-form">

    <?php $form = ActiveForm::begin([
        'id' => 'signature-form',
        'action' => ['posts-signature/save-signature', 'id' => $postId],
        'method' => 'post',
        'options' => [
            'class' => 'signature-form',
            'data-pjax' => false // Prevent pjax interference
        ]
    ]); ?>

    <label class="w-100">Signature</label>
    <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #ccc; border-radius: 8px;"></canvas><br>
    <button type="button" id="clear" class="btn btn-light">Clear</button>
    <?= Html::activeHiddenInput($model, 'signature_base64', ['id' => 'signature-input']) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save Signature', [
            'class' => 'btn btn-success',
            'id' => 'save-signature-btn'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <!-- <div class="form-group mt-3">
        <?= Html::submitButton('Save Signature', [
            'class' => 'btn btn-success',
            'id' => 'save-signature-btn'
        ]) ?>
    </div> -->
</div>

<?php
$js = <<<JS
// Initialize signature pad
let canvas = document.getElementById("signature-pad");
let signaturePad = new SignaturePad(canvas);

// Helper function to show toast notifications
function showToast(icon, title, timer = 3000) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true
    });
}

// Clear button functionality
document.getElementById("clear").addEventListener("click", () => {
    signaturePad.clear();
    document.getElementById('signature-input').value = '';
});

// Handle form submission for signature form
jQuery(document).off('submit', '#signature-form').on('submit', '#signature-form', function(e) {
    e.preventDefault();
    e.stopPropagation();

    console.log('Signature form submitted');

    if (signaturePad.isEmpty()) {
        showToast('warning', 'Please provide a signature before saving');
        return false;
    }

    // Get signature data
    let dataURL = signaturePad.toDataURL('image/png');
    document.getElementById('signature-input').value = dataURL;

    console.log('Signature captured:', dataURL.substring(0, 100) + '...');

    let formElement = this;
    let formData = new FormData(formElement);

    // Log form data for debugging (truncate long values)
    for (let [key, value] of formData.entries()) {
        let displayValue = typeof value === 'string' && value.length > 100 ? value.substring(0, 100) + '...' : value;
        console.log(key +  displayValue);
    }

    // Send AJAX request using XMLHttpRequest
    let xhr = new XMLHttpRequest();
    xhr.open('POST', formElement.action);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function() {
        console.log('Response status:', xhr.status);
        console.log('Response text:', xhr.responseText);

        if (xhr.status === 200) {
            try {
                let response = JSON.parse(xhr.responseText);
                console.log('Parsed response:', response);

                if (response.success) {
                    // Close modal and reload grid if needed
                    jQuery('#ajaxCrudModal').modal('hide');

                    if (response.forceReload) {
                        jQuery.pjax.reload({container: response.forceReload});
                    }

                    showToast('success', response.message || 'Signature saved successfully!');
                } else {
                    showToast('error', response.message || 'Error saving signature', 4000);
                }
            } catch (error) {
                console.error('Error parsing response:', error);
                showToast('error', 'Error processing server response', 4000);
            }
        } else {
            console.error('HTTP Error:', xhr.status);
            showToast('error', 'Server error occurred', 4000);
        }
    };

    xhr.onerror = function() {
        console.error('Network error');
        showToast('error', 'Network error occurred', 4000);
    };

    xhr.send(formData);

    return false;
});

// Reinitialize signature pad when modal is shown
// jQuery(document).on('shown.bs.modal', '#ajaxCrudModal', function() {
//     console.log('Modal shown, reinitializing signature pad');
//     if (typeof SignaturePad !== 'undefined') {
//         let canvas = document.getElementById("signature-pad");
//         if (canvas) {
//             signaturePad = new SignaturePad(canvas);
//         }
//     }
// });

// Reinitialize signature pad when modal is shown
jQuery(document).on('shown.bs.modal', '#ajaxCrudModal', function() {
    console.log('Modal shown, reinitializing signature pad');
    if (typeof SignaturePad !== 'undefined') {
        let canvas = document.getElementById("signature-pad");
        if (canvas) {
            // Destroy existing instance if it exists
            if (signaturePad) {
                signaturePad.clear();
                signaturePad.off(); // Remove event listeners if available
            }
            // Create new instance
            signaturePad = new SignaturePad(canvas);
        }
    }
});
JS;
$this->registerJs($js);
?>