<?php

namespace common\components;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\Notification;
use common\models\UserNotification;
use common\models\NotificationTrigger;
use common\models\Roles;
use common\models\User;

class NotificationManager
{
    public static function trigger($notificationKey, $data = [])
    {
        $notification = Notification::find()
            ->where(['key' => $notificationKey, 'enabled' => true])
            ->one();

        if (!$notification) {
            return;
        }

        $message = $notification->message_template;
        foreach ($data as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        $roleNames = ArrayHelper::getColumn($notification->notificationRoles, 'role');
        $roleIds = Roles::find()->select('id')->where(['name' => $roleNames])->column();
        if (empty($roleIds)) return;

        $users = User::find()->where(['role_id' => $roleIds])->all();

        foreach ($users as $user) {
            $userNotif = new UserNotification([
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'message' => $message,
            ]);

            if (!$userNotif->save()) {
                Yii::error("Notification failed for user {$user->id}: " . json_encode($userNotif->errors), __METHOD__);
            }

            if ($notification->send_email) {
                self::sendEmail($user, $notification->title, $message);
            }
        }
    }

    public static function triggerByRoute($route, $modelId = null)
    {
        $trigger = NotificationTrigger::find()->where(['route' => $route])->one();
        if (!$trigger) return;

        $modelClass = $trigger->model_class;
        $idParam = $trigger->model_id_param;

        $id = $modelId ??
            Yii::$app->request->getBodyParam($idParam) ??
            Yii::$app->request->getQueryParam($idParam);

        if (!$id || !class_exists($modelClass)) return;

        $model = $modelClass::findOne($id);
        if (!$model) return;

        $data = self::buildMessageData($model, $trigger);
        self::trigger($trigger->notification_key, $data);
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

    protected static function sendEmail($user, $subject, $body)
    {
        Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setSubject($subject)
            ->setHtmlBody($body)
            ->send();
    }
}
