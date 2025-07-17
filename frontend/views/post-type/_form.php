<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PostType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<!-- <?= $form->field($model, 'layout_template')->textarea(['rows' => 6]) ?> -->

	<?= $form->field($model, 'layout_template')->widget(CKEditor::class, [
		'options' => ['rows' => 20, 'style' => 'border-radius:8px'],
		'preset' => 'custom',
	]); ?>

	<?php if (!Yii::$app->request->isAjax) { ?>
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
	<?php } ?>

	<?php ActiveForm::end(); ?>

</div>