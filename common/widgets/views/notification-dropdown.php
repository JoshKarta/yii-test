<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="dropdown">
    <button class="btn btn-link text-decoration-none position-relative"
        data-bs-toggle="dropdown"
        aria-expanded="false">

        <i class="fa-solid fa-bell"></i>

        <?php if ($unreadCount > 0): ?>
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                <?= $unreadCount ?>
            </span>
        <?php endif; ?>
    </button>

    <div class="dropdown-menu dropdown-menu-end p-2"
        style="width: 320px; max-height: 400px; overflow-y: auto;">

        <div class="d-flex justify-content-between align-items-center px-2 mb-2">
            <strong>Notifications</strong>
            <button class="btn btn-sm btn-link mark-all-read">Mark all</button>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="text-muted px-2">No notifications</div>
        <?php endif; ?>

        <?php foreach ($notifications as $item): ?>
            <a href="<?= Url::to(['/notification/go', 'id' => $item->id]) ?>"
                class="dropdown-item small <?= $item->is_read ? '' : 'fw-bold' ?>">

                <div><?= Html::encode($item->notification->title) ?></div>
                <div class="text-muted">
                    <?= Html::encode($item->notification->message) ?>
                </div>
            </a>
        <?php endforeach; ?>

        <div class="dropdown-divider"></div>

        <a href="<?= Url::to(['/notification/index']) ?>" class="dropdown-item text-center">
            View all
        </a>
    </div>
</div>