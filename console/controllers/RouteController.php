<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

class RouteController extends Controller
{
    public function actionList()
    {
        $routes = [];
        $controllerDir = Yii::getAlias('@backend/controllers'); // Adjust for backend/common if needed
        $files = FileHelper::findFiles($controllerDir, ['only' => ['*Controller.php']]);

        foreach ($files as $file) {
            $className = 'frontend\\controllers\\' . basename($file, '.php');
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $method) {
                    if (strpos($method->name, 'action') === 0 && $method->name !== 'actions') {
                        // Convert actionMethodName to action-method-name
                        $actionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', substr($method->name, 6)));
                        $controllerName = strtolower(preg_replace('/Controller$/', '', $reflection->getShortName()));
                        $routes[] = "$controllerName/$actionName";
                    }
                }
            }
        }

        // Remove duplicates and sort
        $routes = array_unique($routes);
        sort($routes);

        // Output routes
        foreach ($routes as $route) {
            $this->stdout("$route\n");
        }
    }
}
