<?php

use common\models\Signature;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PostType;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Posts */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Update Post";

// Fetch post types from the database
$postTypes = ArrayHelper::map(PostType::find()->all(), 'id', 'name');
?>

<div class="posts-form">

    <div class="row">
        <div class="col-md-6">
            <h2><?= $model->title ?></h2>
        </div>
        <div class="col-md-6 align-content-center">
            <span class="float-end badge bg-danger fs-6">
                <?= $model->workflowStatus->label ?>
            </span>
        </div>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'post_type_id')->widget(Select2::class, [
        'data' => $postTypes,
        'options' => ['placeholder' => 'Select post types...'],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <!-- <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?> -->

    <!-- Mark document as Final Checkbox -->
    <?php if ($model->workflowStatus && $model->workflowStatus->label === 'hoofd'): ?>
        <?= $form->field($model, 'publish')->checkbox([
            'id' => 'mark-final-checkbox',
            'label' => 'Publiseer post',
            'uncheck' => 0,   // value saved when unchecked
            'value' => 1,     // value saved when checked
        ]) ?>
    <?php endif; ?>


    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>