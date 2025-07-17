<?php

namespace common\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Notification;
use common\models\UserNotification;
use common\models\NotificationTrigger;
use common\models\Roles;
use common\models\User;

class NotificationManager2
{
    /**
     * Main trigger method (used by NotificationListener or manually)
     */
    public static function trigger($notificationKey, $data = [], $link = null)
    {
        $notification = Notification::find()->where(['key' => $notificationKey, 'enabled' => true])->one();
        if (!$notification) {
            return;
        }

        $message = $notification->message_template;

        // Replace placeholders
        foreach ($data as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        $channels = json_decode($notification->channels, true);
        if (!$channels || !is_array($channels)) {
            $channels = ['database'];
        }

        $roleNames = ArrayHelper::getColumn($notification->notificationRoles, 'role'); // ['admin', 'editor']
        $roleIds = Roles::find()
            ->select('id')
            ->where(['name' => $roleNames])
            ->column();

        if (empty($roleIds)) {
            Yii::warning("No matching role IDs found for role names: " . json_encode($roleNames), __METHOD__);
            return;
        }

        $users = User::find()->where(['role_id' => $roleIds])->all();

        foreach ($users as $user) {
            Yii::info("Processing notification for user {$user->id} ({$user->username})", __METHOD__);

            if (in_array('database', $channels)) {
                $userNotif = new UserNotification([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'message' => $message,
                    'link' => $link,
                ]);

                if (!$userNotif->save()) {
                    Yii::error("Failed to save UserNotification for user {$user->id}. Errors: " . json_encode($userNotif->errors), __METHOD__);
                } else {
                    Yii::info("UserNotification created successfully for user {$user->id}", __METHOD__);
                }
            }

            if (in_array('email', $channels)) {
                Yii::info("Sending email to user {$user->id} ({$user->email})", __METHOD__);
                self::sendEmail($user, $notification->title, $message, $link);
            }
        }

        Yii::info("Triggering notification key: {$notificationKey}", __METHOD__);
        Yii::info("Notification channels: " . json_encode($channels), __METHOD__);

        $roles = ArrayHelper::getColumn($notification->notificationRoles, 'role');
        Yii::info("Roles assigned to this notification: " . json_encode($roles), __METHOD__);

        $users = User::find()->where(['role_id' => $roles])->all();
        Yii::info("Users matched by roles: " . count($users), __METHOD__);
    }

    /**
     * Auto-trigger by route based on NotificationTrigger config
     */
    public static function triggerByRoute($route, $modelId = null)
    {
        Yii::info("Triggering for route: $route", __METHOD__);

        $trigger = NotificationTrigger::find()->where(['route' => $route])->one();

        if (!$trigger) {
            Yii::warning("No notification trigger found for route: $route", __METHOD__);
            return;
        }

        $modelClass = $trigger->model_class;
        $idParam = $trigger->model_id_param;

        // Try to get the model ID from GET or POST
        $id = $modelId ?? Yii::$app->request->getBodyParam($idParam) ?? Yii::$app->request->getQueryParam($idParam);
        if (!$id || !class_exists($modelClass)) {
            Yii::info("Model not found: $route", __METHOD__);
            return;
        }

        $model = $modelClass::findOne($id);
        if (!$model) return;

        // Prepare data from model using field map
        $data = [];
        $fieldMap = json_decode($trigger->fields, true);
        if (is_array($fieldMap)) {
            foreach ($fieldMap as $placeholder => $attributePath) {
                $data[$placeholder] = self::getAttributeByPath($model, $attributePath);
            }
        }

        // Add current username
        $data['username'] = Yii::$app->user->identity->username ?? 'Guest';

        // Prepare link
        $link = null;
        if ($trigger->link_template) {
            $link = $trigger->link_template;
            foreach ($model->attributes as $key => $value) {
                $link = str_replace("{{$key}}", $value, $link);
            }
            $link = Url::to($link);
        }

        self::trigger($trigger->notification_key, $data, $link);
    }

    /**
     * Send email notification
     */
    protected static function sendEmail($user, $subject, $body, $link = null)
    {
        $fullBody = $body;
        if ($link) {
            $fullBody .= "<br><br><a href=\"{$link}\">Bekijk details</a>";
        }

        Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setSubject($subject)
            ->setHtmlBody($fullBody)
            ->send();
    }

    /**
     * Get nested attribute value from dot notation (e.g. "createdBy.username")
     */
    protected static function getAttributeByPath($model, $path)
    {
        $parts = explode('.', $path);
        $value = $model;
        foreach ($parts as $part) {
            if (is_object($value) && isset($value->{$part})) {
                $value = $value->{$part};
            } else {
                return null;
            }
        }
        return $value;
    }

    public static function buildMessageData($model, $trigger)
    {
        $fieldMap = json_decode($trigger->fields, true);
        $data = [];

        if (is_array($fieldMap)) {
            foreach ($fieldMap as $placeholder => $attributePath) {
                $data[$placeholder] = self::getAttributeByPath($model, $attributePath);
            }
        }

        $data['username'] = Yii::$app->user->identity->username ?? 'Guest';
        return $data;
    }

    public static function buildLink($model, $trigger)
    {
        $link = $trigger->link_template;
        if (!$link) return null;

        foreach ($model->attributes as $key => $value) {
            $link = str_replace("{{$key}}", $value, $link);
        }

        return \yii\helpers\Url::to($link);
    }
}
