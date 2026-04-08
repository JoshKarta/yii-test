<?php

namespace common\components;

use common\models\Notification;
use common\models\NotificationRole;
use common\models\NotificationUser;
use common\models\User;
use Yii;

class NotificationService
{
    public static function create($title, $message, $roleIds = [])
    {
        $notification = new Notification();
        $notification->title = $title;
        $notification->message = $message;
        $notification->created_at = date('Y-m-d H:i:s');
        $notification->created_by = Yii::$app->user->id ?? null;
        $notification->save();

        // attach roles
        foreach ($roleIds as $roleId) {
            $nr = new NotificationRole();
            $nr->notification_id = $notification->id;
            $nr->role_id = $roleId;
            $nr->save();
        }

        // assign to users via role_id
        $users = User::find()
            ->where(['role_id' => $roleIds])
            ->all();

        foreach ($users as $user) {
            $nu = new NotificationUser();
            $nu->notification_id = $notification->id;
            $nu->user_id = $user->id;
            $nu->save();
        }

        return $notification;
    }
}
