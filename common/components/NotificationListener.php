<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\web\Application;
use common\components\NotificationManager;
use common\models\NotificationTrigger;

class NotificationListener extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_AFTER_ACTION => 'checkAndTrigger',
        ];
    }

    public function checkAndTrigger($event)
    {
        $route = Yii::$app->controller->route;
        $trigger = NotificationTrigger::find()->where(['route' => $route])->one();
        if (!$trigger) return;

        $request = Yii::$app->request;
        $isCreate = $trigger->trigger_type === 'create' && $request->isPost;

        if (!$isCreate) return;

        $modelClass = $trigger->model_class;
        $idParam = $trigger->model_id_param;
        if (!class_exists($modelClass)) return;

        // Case: AJAX response with model
        $result = $event->result;
        if (is_array($result) && isset($result['model']) && $result['model'] instanceof $modelClass) {
            $model = $result['model'];
        } else {
            $id = $request->getBodyParam($idParam) ?? $request->getQueryParam($idParam);
            if (!$id) return;
            $model = $modelClass::findOne($id);
        }

        if (!$model) return;

        NotificationManager::trigger(
            $trigger->notification_key,
            NotificationManager::buildMessageData($model, $trigger)
        );
    }
}
