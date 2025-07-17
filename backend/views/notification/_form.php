<?php

use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Notification;

$form = ActiveForm::begin();

echo $form->field($model, 'key')->textInput(['placeholder' => 'e.g. document/create']);

echo $form->field($model, 'title')->textInput(['placeholder' => 'Notification Title']);

echo $form->field($model, 'message_template')->textarea(['rows' => 4, 'placeholder' => 'e.g. User {username} created a report.']);

echo $form->field($model, 'channels')->widget(Select2::class, [
    'data' => [
        'database' => 'Database',
        'email' => 'Email',
    ],
    'options' => ['placeholder' => 'Select channels...', 'multiple' => true],
    'pluginOptions' => ['allowClear' => true],
]);

echo $form->field($model, 'enabled')->checkbox();

echo Html::submitButton('Save', ['class' => 'btn btn-primary']);

ActiveForm::end();
