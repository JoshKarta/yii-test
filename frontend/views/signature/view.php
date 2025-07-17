<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Signature */

$this->title = 'Signature View';
$this->params['breadcrumbs'][] = ['label' => 'Signatures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="signature-view">
    <h3>Signature</h3>

    <p><strong>User:</strong> <?= Html::encode($model->user->username ?? 'Unknown') ?></p>

    <?php if ($model->file_name): ?>
        <div>
            <img src="<?= Yii::getAlias('@web') . '/uploads/signatures/' . $model->file_name ?>"
                alt="User Signature"
                style="max-width: 100%; border: 1px solid #ccc; padding: 8px; border-radius:8px" />
        </div>
    <?php else: ?>
        <p><em>No signature uploaded.</em></p>
    <?php endif; ?>
</div>