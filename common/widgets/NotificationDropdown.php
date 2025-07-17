<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;
use common\models\UserNotification;

class NotificationDropdown extends Widget
{
    public $limit = 10;

    public function run()
    {
        $userId = Yii::$app->user->id;

        $unreadCount = UserNotification::find()
            ->where(['user_id' => $userId, 'is_read' => 0])
            ->count();

        $notifications = UserNotification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($this->limit)
            ->all();

        return $this->render('notificationDropdown/index', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
