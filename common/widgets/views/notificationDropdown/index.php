<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var integer $unreadCount */
/** @var \common\models\UserNotification[] $notifications */

$itemsHtml = '';
foreach ($notifications as $notif) {
    $itemClass = $notif->is_read ? 'text-muted' : 'fw-bold';
    $itemsHtml .= Html::a(
        Html::encode($notif->message),
        'javascript:void(0);',
        [
            'class' => "dropdown-item notification-link {$itemClass}",
            'style' => 'white-space: normal;',
            'data-id' => $notif->id,
            'data-link' => Url::to([$notif->link]),
        ]
    );
}

// Dropdown HTML
echo Html::tag(
    'li',
    Html::a(
        '🔔' . ($unreadCount ? " <span class='badge bg-danger'>{$unreadCount}</span>" : ''),
        '#',
        [
            'class' => 'nav-link dropdown-toggle',
            'data-bs-toggle' => 'dropdown',
            'role' => 'button',
            'aria-expanded' => 'false',
        ]
    ) .
        Html::tag('ul', $itemsHtml ?: '<li class="dropdown-item text-muted">No notifications</li>', [
            'class' => 'dropdown-menu dropdown-menu-end',
            'style' => 'max-height: 300px; overflow-y: auto; width: 300px;',
        ]),
    ['class' => 'nav-item dropdown']
);
