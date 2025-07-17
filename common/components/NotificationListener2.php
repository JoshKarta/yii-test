<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\web\Application;
use common\components\NotificationManager;
use common\models\NotificationTrigger;

class NotificationListener2 extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_AFTER_ACTION => 'checkAndTrigger',
        ];
    }

    public function handleNotificationTrigger($event)
    {
        $route = Yii::$app->controller->route;

        // Look up in notification_trigger table
        NotificationManager::triggerByRoute($route);
    }

    public function checkAndTrigger($event)
    {
        $route = Yii::$app->controller->route;

        $trigger = NotificationTrigger::find()->where(['route' => $route])->one();
        if (!$trigger) return;

        $request = Yii::$app->request;

        $isCreate = $trigger->trigger_type === 'create' && $request->isPost;
        $isUpdate = $trigger->trigger_type === 'update' && $request->isPost;

        if (!$isCreate && !$isUpdate) return;

        $modelClass = $trigger->model_class;
        $idParam = $trigger->model_id_param;

        if (!class_exists($modelClass)) return;

        // ✅ Check if event result contains a model (used in your Ajax JSON)
        $result = $event->result;
        if (is_array($result) && isset($result['model']) && $result['model'] instanceof $modelClass) {
            $model = $result['model'];
        } else {
            // fallback to param ID
            $id = $request->getBodyParam($idParam) ?? $request->getQueryParam($idParam);
            if (!$id) return;
            $model = $modelClass::findOne($id);
        }

        if (!$model) return;

        NotificationManager::trigger(
            $trigger->notification_key,
            NotificationManager::buildMessageData($model, $trigger),
            NotificationManager::buildLink($model, $trigger)
        );
    }
}
