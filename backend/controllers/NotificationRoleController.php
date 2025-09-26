<?php

namespace backend\controllers;

use Yii;
use common\models\NotificationRole;
use common\models\search\NotificationRoleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * NotificationRoleController implements the CRUD actions for NotificationRole model.
 */
class NotificationRoleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulkdelete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all NotificationRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single NotificationRole model.
     * @param integer $notification_id
     * @param integer $role_id
     * @return mixed
     */
    public function actionView($notification_id, $role_id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "NotificationRole #" . $notification_id,
                $role_id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($notification_id, $role_id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'notification_id, $role_id' => $notification_id, $role_id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($notification_id, $role_id),
            ]);
        }
    }

    /**
     * Creates a new NotificationRole model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new NotificationRole();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                // Return form for GET request
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " NotificationRole",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }

            // Handle POST request
            if ($model->load($request->post())) {
                $roleIds = $request->post('NotificationRole')['role_id'] ?? [];
                $notificationId = $model->notification_id;
                $created = 0;
                $duplicates = [];
                $errors = [];

                if (is_array($roleIds) && !empty($roleIds)) {
                    foreach ($roleIds as $roleId) {
                        // Check for duplicate
                        $exists = NotificationRole::find()
                            ->where(['notification_id' => $notificationId, 'role_id' => $roleId])
                            ->exists();

                        if ($exists) {
                            $duplicates[] = $roleId;
                            continue;
                        }

                        $nr = new NotificationRole();
                        $nr->notification_id = $notificationId;
                        $nr->role_id = $roleId;

                        if ($nr->save()) {
                            $created++;
                        } else {
                            $errors[] = $nr->getErrors();
                        }
                    }

                    // Prepare response
                    $response = [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " NotificationRole",
                        'content' => '<span class="text-success">' .
                            Yii::t('yii2-ajaxcrud', 'Created {count} notification role(s)', ['count' => $created]) .
                            '</span>',
                        'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                            Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                    ];

                    // Add duplicate warning if needed
                    if (!empty($duplicates)) {
                        $response['content'] .= '<br><span class="text-warning">' .
                            Yii::t('yii2-ajaxcrud', 'Skipped {count} duplicate(s)', ['count' => count($duplicates)]) .
                            '</span>';
                    }

                    // Add error message if needed
                    if (!empty($errors)) {
                        $response['content'] .= '<br><span class="text-danger">' .
                            Yii::t('yii2-ajaxcrud', 'Errors occurred for {count} role(s)', ['count' => count($errors)]) .
                            '</span>';
                    }

                    return $response;
                } else {
                    // No roles selected case
                    return [
                        'title' => Yii::t('yii2-ajaxcrud', 'Error'),
                        'content' => '<span class="text-danger">' .
                            Yii::t('yii2-ajaxcrud', 'Please select at least one role') .
                            '</span>',
                        'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default', 'data-bs-dismiss' => 'modal'])
                    ];
                }
            } else {
                // Model didn't load properly
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Error'),
                    'content' => '<span class="text-danger">' .
                        Yii::t('yii2-ajaxcrud', 'Error loading data') .
                        '</span>',
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default', 'data-bs-dismiss' => 'modal'])
                ];
            }
        } else {
            // Non-AJAX request handling
            if ($model->load($request->post())) {
                $roleIds = $request->post('NotificationRole')['role_id'] ?? [];
                $notificationId = $model->notification_id;
                $created = 0;
                $duplicates = [];

                if (is_array($roleIds) && !empty($roleIds)) {
                    foreach ($roleIds as $roleId) {
                        $exists = NotificationRole::find()
                            ->where(['notification_id' => $notificationId, 'role_id' => $roleId])
                            ->exists();

                        if ($exists) {
                            $duplicates[] = $roleId;
                            continue;
                        }

                        $nr = new NotificationRole();
                        $nr->notification_id = $notificationId;
                        $nr->role_id = $roleId;

                        if ($nr->save()) {
                            $created++;
                        }
                    }

                    if ($created) {
                        Yii::$app->session->setFlash(
                            'success',
                            Yii::t('yii2-ajaxcrud', 'Created {count} notification role(s)', ['count' => $created])
                        );
                    }
                    if (!empty($duplicates)) {
                        Yii::$app->session->setFlash(
                            'warning',
                            Yii::t('yii2-ajaxcrud', 'Skipped {count} duplicate(s)', ['count' => count($duplicates)])
                        );
                    }

                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash(
                        'error',
                        Yii::t('yii2-ajaxcrud', 'Please select at least one role')
                    );
                }
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NotificationRole model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $notification_id
     * @param integer $role_id
     * @return mixed
     */
    public function actionUpdate($notification_id, $role_id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($notification_id, $role_id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " NotificationRole #" . $notification_id,
                    $role_id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "NotificationRole #" . $notification_id,
                    $role_id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'notification_id, $role_id' => $notification_id, $role_id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " NotificationRole #" . $notification_id,
                    $role_id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'notification_id' => $model->notification_id, 'role_id' => $model->role_id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing NotificationRole model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $notification_id
     * @param integer $role_id
     * @return mixed
     */
    public function actionDelete($notification_id, $role_id)
    {
        $request = Yii::$app->request;
        $this->findModel($notification_id, $role_id)->delete();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
    }

    /**
     * Delete multiple existing NotificationRole model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $notification_id
     * @param integer $role_id
     * @return mixed
     */
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            list($notification_id, $role_id) = explode('-', $pk);
            $model = $this->findModel($notification_id, $role_id);
            $model->delete();
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the NotificationRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $notification_id
     * @param integer $role_id
     * @return NotificationRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($notification_id, $role_id)
    {
        if (($model = NotificationRole::findOne(['notification_id' => $notification_id, 'role_id' => $role_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
