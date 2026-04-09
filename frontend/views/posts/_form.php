<?php

use common\models\Signature;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\PostType;
use kartik\form\ActiveForm;
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

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'status')->widget(Select2::class, [
            'data' => [
                'draft' => 'Draft',
                'reviewed' => 'Reviewed',
                'published' => 'Published',
            ],
            'options' => ['placeholder' => 'Select post types...'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>


    <!-- Mark document as Final Checkbox -->



    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>