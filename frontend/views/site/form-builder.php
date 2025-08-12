<?php

use meysampg\formbuilder\FormBuilder;
use yii\bootstrap5\Html;

$this->title = 'Form builder';

echo FormBuilder::widget([
    'accessVariableName' => 'formBuilder',
    'data' => [
        [
            "type" => "header",
            "subtype" => "h1",
            "label" => "Form name",
            "name" => 'form_name'
        ],
        [
            "type" => "text",
            "label" => "Text Field",
            "className" => "form-control",
            "name" => "text-1753886622791",
            "subtype" => "text"
        ],
    ],
    'dataType' => 'json',
    'showActionButtons' => true
]);

$this->registerJS('
    $(document).on("click", ".save-template", function() {
        var formData = formBuilder.actions.getData();
        var formTitle = prompt("Enter form title:", "Generated Form " + new Date().toISOString().slice(0,10));
        
        if (formTitle) {
            $.ajax({
                url: "' . \yii\helpers\Url::to(['/form/create']) . '",
                type: "POST",
                data: {
                    "Form[title]": formTitle,
                    "Form[description]": "Form created with form builder",
                    "Form[json]": formData,
                    "_csrf": $("meta[name=csrf-token]").attr("content")
                },
                success: function(response) {
                    if(response.forceReload) {
                        alert("Form saved successfully!");
                        window.location.reload();
                    } else {
                        alert("Form saved successfully!");
                    }
                },
                error: function() {
                    alert("Error saving form!");
                }
            });
        }
    });
');
