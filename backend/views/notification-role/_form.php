<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Notification;
use common\models\Roles;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationRole */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-role-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'notification_id')->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Notification::find()->all(), 'id', 'title'),
		'options' => [
			'placeholder' => 'Select a notification...',
		],
		'pluginOptions' => [
			'allowClear' => true,
			'dropdownParent' => '#ajaxCrudModal'
		]
	]); ?>
	<?= $form->field($model, 'role_id')->widget(Select2::classname(), [
		'data' => ArrayHelper::map(Roles::find()->all(), 'id', 'name'),
		'options' => [
			'placeholder' => 'Select a role...',
			'multiple' => true,
		],
		'pluginOptions' => [
			'allowClear' => true,
			'dropdownParent' => '#ajaxCrudModal'
		]
	]); ?>


	<?php if (!Yii::$app->request->isAjax) { ?>
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
	<?php } ?>

	<?php ActiveForm::end(); ?>

</div>