<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\Request;
use common\models\Notification;
use common\models\NotificationTrigger;
use common\models\NotificationUser;

class NotificationManager extends Component
{
    /**
     * Main entry point for route-based triggers.
     */
    public function checkAndTrigger(string $route, Request $request, string $phase = 'before', array $extraParams = []): void
    {
        $triggers = NotificationTrigger::find()->where(['route' => $route])->all();
        if (empty($triggers)) {
            return;
        }

        foreach ($triggers as $trigger) {
            // Match request type
            if ($trigger->request_type === 'AJAX' && !$request->isAjax) {
                continue;
            }
            if ($trigger->request_type === 'NON_AJAX' && $request->isAjax) {
                continue;
            }

            // Handle create routes: skip BEFORE, only handle AFTER
            if ($phase === 'before' && str_contains($route, '/create')) {
                continue;
            }

            $notification = Notification::findOne(['key' => $trigger->notification_key, 'enabled' => 1]);
            if (!$notification) {
                continue;
            }

            // Merge GET, POST, and any extra parameters passed in (like model ID after save)
            $replacements = array_merge($request->get(), $request->post(), $extraParams);

            // Apply templates
            $message = $this->applyTemplate($notification->message_template, $replacements);
            $link = $this->applyTemplate($trigger->link_template, $replacements);

            // Find users via roles
            $users = (new \yii\db\Query())
                ->select('u.id')
                ->from('user u')
                ->innerJoin('notification_role nr', 'nr.role_id = u.role_id')
                ->where(['nr.notification_id' => $notification->id])
                ->all();

            foreach ($users as $user) {
                $nu = new NotificationUser([
                    'notification_id' => $notification->id,
                    'user_id'         => $user['id'],
                    'message'         => $message,
                    'link'            => $link,
                ]);
                $nu->save(false);
            }

            if ($notification->send_email) {
                foreach ($users as $user) {
                    $this->sendEmail($user['id'], $notification, $message, $link);
                }
            }
        }
    }

    /**
     * Replace {{placeholders}} with request or extra params.
     */
    protected function applyTemplate(?string $template, array $params): ?string
    {
        if (!$template) {
            return null;
        }

        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($params) {
            $key = trim($matches[1]);
            return $params[$key] ?? '';
        }, $template);
    }

    protected function sendEmail($userId, Notification $notification, string $message, ?string $link = null): void
    {
        $user = \common\models\User::findOne($userId);
        if (!$user || !$user->email) {
            return;
        }

        Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setSubject($notification->title)
            ->setTextBody($message . ($link ? "\n\n" . Yii::$app->urlManager->createAbsoluteUrl($link) : ''))
            ->send();
    }
}
