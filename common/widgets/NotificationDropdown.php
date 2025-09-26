<?php

namespace common\widgets;

use common\models\NotificationUser;
use Yii;
use yii\base\Widget;

class NotificationDropdown extends Widget
{
    public $limit = 10;

    public function run()
    {
        $userId = Yii::$app->user->id;

        $unreadCount = NotificationUser::find()
            ->where(['user_id' => $userId, 'is_read' => 0])
            ->count();

        $notifications = NotificationUser::find()
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
