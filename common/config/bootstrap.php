<?php

use yii\base\Event;
use yii\web\Controller;

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

Event::on(Controller::class, Controller::EVENT_BEFORE_ACTION, function ($event) {
    $route = '/' . $event->action->controller->id . '/' . $event->action->id;
    $request = Yii::$app->request;

    // Skip some routes
    $skip = ['/site/error', '/debug/default/toolbar', '/debug/default/view'];
    if (in_array($route, $skip)) {
        return;
    }

    Yii::$app->notificationManager->checkAndTrigger($route, $request);
});

Event::on(Controller::class, Controller::EVENT_AFTER_ACTION, function ($event) {
    $route = '/' . $event->action->controller->id . '/' . $event->action->id;
    $request = Yii::$app->request;

    $skip = ['/site/error', '/debug/default/toolbar', '/debug/default/view'];
    if (in_array($route, $skip)) {
        return;
    }

    $extraParams = [];
    if (isset(Yii::$app->params['lastCreatedId'])) {
        $extraParams['id'] = Yii::$app->params['lastCreatedId'];
        unset(Yii::$app->params['lastCreatedId']); // cleanup
    }

    Yii::$app->notificationManager->checkAndTrigger($route, $request, 'after', $extraParams);
});
