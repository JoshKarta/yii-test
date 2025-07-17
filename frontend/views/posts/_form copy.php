<?php

use common\models\Signature;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Posts */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Update Post"
?>

<div class="posts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?php
    // Check if signature_id is empty
    if (empty($model->signature_id)) {
        $userId = Yii::$app->user->id;
        $userSignature = Signature::find()->where(['user_id' => $userId])->one();

        if ($userSignature) {
            // If "sign" GET param is set, show signature and set hidden field
            if (Yii::$app->request->get('sign') == 1) {
                // Show signature image
                echo '<div class="mb-3">';
                echo Html::img(Yii::getAlias('@web') . '/uploads/signatures/' . $userSignature->file_name, ['alt' => 'Signature', 'style' => 'max-width:300px;max-height:150px;display:block;border:1px solid #ccc;padding:8px;border-radius:8px;']);
                echo '</div>';
                // Set hidden field for signature_id
                echo Html::activeHiddenInput($model, 'signature_id', ['value' => $userSignature->id]);
            } else {
                // Show "Sign Post" button
                echo Html::a(
                    'Sign Post',
                    ['posts/update', 'id' => $model->id, 'sign' => 1],
                    ['class' => 'btn btn-outline-danger mb-3']
                );
            }
        } else {
            // User does not have a signature, show "Create Signature" button
            echo Html::a(
                'Create Signature',
                ['signature/create'],
                ['class' => 'btn btn-danger mb-3']
            );
        }
    } else {
        // If already signed, show the signature
        $signature = Signature::findOne($model->signature_id);
        if ($signature) {
            echo '<div class="mb-3">';
            echo Html::img($signature->file_name, ['alt' => 'Signature', 'style' => 'max-width:300px;max-height:150px;display:block;border:1px solid #ccc;padding:8px;border-radius:8px;']);
            echo '</div>';
        }
    }
    ?>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>


$js = <<<JS
    let canvas=document.getElementById("signature-pad");
    let signaturePad=new SignaturePad(canvas);

    document.getElementById("clear").addEventListener("click", ()=> {
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

    // Connect modal footer button to form submission
    $(document).on('click', '.modal-footer .btn-primary', function(e) {
    // Find the form in the modal and submit it
    $(this).closest('.modal-dialog').find('form').submit();
    return false;
    });
    JS;
    $this->registerJs($js);


    <!-- <?php
            $js = <<<JS
// $('form').on('beforeSubmit', function (e) {
//     if (!signaturePad.isEmpty()) {
//         // Make sure we're getting the complete data URL with the image/png prefix
//         let dataURL = signaturePad.toDataURL();
//         $('#signature-input').val(dataURL);
        
//         // Submit the form via AJAX
//         var form = $(this);
//         $.ajax({
//             url: form.attr("action"),
//             type: form.attr("method"),
//             data: form.serialize(),
//             dataType: 'json',  // Explicitly specify expected response type
//             success: function (data) {
//                 if (data.success) {
//                     if (data.forceReload) {
//                         $.pjax.reload({container: data.forceReload});
//                     }
//                     $("#ajaxCrudModal").modal("hide");
//                 } else {
//                     // Show error message from server or default message
//                     alert(data.message || "Error occurred during signature saving");
//                     $("#ajaxCrudModal").modal("hide"); // Hide modal on error
//                 }
//             },
//             error: function (xhr, status, error) {
//                 console.error("AJAX Error:", status, error);
//                 alert("Error occurred during signature saving: " + error);
//                 $("#ajaxCrudModal").modal("hide"); // Hide modal on error
//             }
//         });
//         console.log(dataURL);
//         return false; // prevent default form submission
//     } else {
//         alert('Please provide a signature.');
//         return false; // prevent submit
//     }
// });

let canvas = document.getElementById("signature-pad");
let signaturePad = new SignaturePad(canvas);

document.getElementById("clear").addEventListener("click", () => {
    signaturePad.clear();
    $('#signature-input').val('');
});

// Intercept form submit event
$(document).on('submit', 'form', function(e) {
    e.preventDefault();

    if (!signaturePad.isEmpty()) {
        let dataURL = signaturePad.toDataURL('image/png');
        $('#signature-input').val(dataURL);

        let form = $(this);

        console.log(dataURL);
        

        // $.ajax({
        //     url: form.attr("action"),
        //     type: form.attr("method"),
        //     data: form.serialize(),
        //     dataType: 'json',
        //     success: function (data) {
        //         if (data.success) {
        //             if (data.forceReload) {
        //                 $.pjax.reload({container: data.forceReload});
        //             }
        //             $("#ajaxCrudModal").modal("hide");
        //         } else {
        //             alert(data.message || "Error occurred during signature saving");
        //             $("#ajaxCrudModal").modal("hide");
        //         }
        //     },
        //     error: function (xhr, status, error) {
        //         console.error("AJAX Error:", xhr.responseText);
        //         alert("Error occurred during signature saving");
        //         $("#ajaxCrudModal").modal("hide");
        //     }
        // });
    } else {
        alert('Please provide a signature.');
    }

    return false;
});
JS;
            $this->registerJs($js);
            ?> -->