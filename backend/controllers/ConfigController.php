<?php

namespace backend\controllers;

use Yii;
use common\models\Config;
use common\models\search\ConfigSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;

/**
 * ConfigController implements the CRUD actions for Config model.
 */
class ConfigController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'bulk-delete', 'clear-cache'],
                        'allow' => true,
                        // 'roles' => ['admin'], // Only users with admin role can access
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'bulk-delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Config models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Configuration Management",
                'content' => $this->renderAjax('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                    Html::a('Create New', ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote'])
            ];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Config model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "Configuration #" . $model->id,
                'content' => $this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote']) .
                    (!$model->is_system ? Html::a('Delete', ['delete', 'id' => $id], [
                        'class' => 'btn btn-danger',
                        'role' => 'modal-remote',
                        'data-request-method' => 'post',
                        'data-confirm-title' => 'Are you sure?',
                        'data-confirm-message' => 'Are you sure you want to delete this configuration?'
                    ]) : '')
            ];
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Config model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Config();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                return [
                    'title' => "Create New Configuration",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {
                // Set default values
                if (empty($model->type)) {
                    $model->type = Config::TYPE_STRING;
                }

                if ($model->save()) {
                    // Clear configuration cache
                    Yii::$app->configuration->clearCache();

                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => "Create New Configuration",
                        'content' => '<span class="text-success">Create Configuration success</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                            Html::a('Create More', ['create'], ['class' => 'btn btn-success', 'role' => 'modal-remote'])
                    ];
                } else {
                    return [
                        'title' => "Create New Configuration",
                        'content' => $this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])
                    ];
                }
            } else {
                return [
                    'title' => "Create New Configuration",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])
                ];
            }
        }

        // Non-AJAX request
        if ($model->load($request->post())) {
            if (empty($model->type)) {
                $model->type = Config::TYPE_STRING;
            }

            if ($model->save()) {
                Yii::$app->configuration->clearCache();
                Yii::$app->session->setFlash('success', 'Configuration created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Config model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        // Prevent editing system configurations
        if ($model->is_system) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title' => "Update Configuration #" . $id,
                    'content' => '<div class="alert alert-warning">System configurations cannot be modified.</div>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
                ];
            }
            Yii::$app->session->setFlash('warning', 'System configurations cannot be modified.');
            return $this->redirect(['index']);
        }

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                return [
                    'title' => "Update Configuration #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                // Clear configuration cache
                Yii::$app->configuration->clearCache();

                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Configuration #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote']) .
                        Html::a('Delete', ['delete', 'id' => $id], [
                            'class' => 'btn btn-danger',
                            'role' => 'modal-remote',
                            'data-request-method' => 'post',
                            'data-confirm-title' => 'Are you sure?',
                            'data-confirm-message' => 'Are you sure you want to delete this configuration?'
                        ])
                ];
            } else {
                return [
                    'title' => "Update Configuration #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])
                ];
            }
        }

        // Non-AJAX request
        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->configuration->clearCache();
            Yii::$app->session->setFlash('success', 'Configuration updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Config model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        // Prevent deleting system configurations
        if ($model->is_system) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title' => "Delete Configuration #" . $id,
                    'content' => '<div class="alert alert-warning">System configurations cannot be deleted.</div>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
                ];
            }
            Yii::$app->session->setFlash('warning', 'System configurations cannot be deleted.');
            return $this->redirect(['index']);
        }

        if ($model->delete()) {
            // Clear configuration cache
            Yii::$app->configuration->clearCache();

            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Delete Configuration #" . $id,
                    'content' => '<span class="text-success">Configuration deleted successfully</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
                ];
            }

            Yii::$app->session->setFlash('success', 'Configuration deleted successfully.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Bulk delete action
     */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $ids = $request->post('ids');

        if (!empty($ids)) {
            $models = Config::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $systemConfigs = [];

            foreach ($models as $model) {
                // Skip system configurations
                if (!$model->is_system) {
                    if ($model->delete()) {
                        $deletedCount++;
                    }
                } else {
                    $systemConfigs[] = $model->key;
                }
            }

            if ($deletedCount > 0) {
                // Clear configuration cache
                Yii::$app->configuration->clearCache();
            }

            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                $message = "{$deletedCount} configuration(s) deleted successfully.";
                if (!empty($systemConfigs)) {
                    $message .= " System configurations (" . implode(', ', $systemConfigs) . ") were skipped.";
                }

                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Bulk Delete",
                    'content' => '<div class="alert alert-success">' . $message . '</div>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
                ];
            }

            if ($deletedCount > 0) {
                Yii::$app->session->setFlash('success', "{$deletedCount} configuration(s) deleted successfully.");
                if (!empty($systemConfigs)) {
                    Yii::$app->session->setFlash('warning', "System configurations (" . implode(', ', $systemConfigs) . ") were skipped.");
                }
            } else {
                Yii::$app->session->setFlash('warning', 'No configurations were deleted. System configurations cannot be deleted.');
            }
        } else {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title' => "Bulk Delete",
                    'content' => '<div class="alert alert-danger">No configurations selected for deletion.</div>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
                ];
            }
            Yii::$app->session->setFlash('error', 'No configurations selected for deletion.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Clear configuration cache
     */
    public function actionClearCache()
    {
        $request = Yii::$app->request;

        Yii::$app->configuration->clearCache();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'forceReload' => '#crud-datatable-pjax',
                'title' => "Clear Cache",
                'content' => '<div class="alert alert-success">Configuration cache cleared successfully.</div>',
                'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-bs-dismiss' => "modal"])
            ];
        }

        Yii::$app->session->setFlash('success', 'Configuration cache cleared successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Config model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Config the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Config::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested configuration does not exist.');
    }
}
