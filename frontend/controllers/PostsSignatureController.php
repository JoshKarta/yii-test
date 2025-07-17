<?php

namespace frontend\controllers;

use common\components\SignatureHelper;
use Yii;
use common\models\PostsSignature;
use common\models\search\PostsSignatureSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * PostsSignatureController implements the CRUD actions for PostsSignature model.
 */
class PostsSignatureController extends Controller
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
     * Lists all PostsSignature models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new PostsSignatureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single PostsSignature model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "PostsSignature #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                    Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new PostsSignature model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $request = Yii::$app->request;
        $model = new PostsSignature();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PostsSignature",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    // 'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                    //     Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PostsSignature",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' PostsSignature ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " PostsSignature",
                    'content' => $this->renderAjax('create', [
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
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing PostsSignature model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " PostsSignature #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit', 'form' => 'signature-form'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "PostsSignature #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " PostsSignature #" . $id,
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
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing PostsSignature model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

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
     * Delete multiple existing PostsSignature model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys
        foreach ($pks as $pk) {
            $model = $this->findModel($pk);
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
     * Finds the PostsSignature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PostsSignature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PostsSignature::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // public function actionSaveSignature($id)
    // {
    //     $request = Yii::$app->request;
    //     $model = new \common\models\PostsSignature();

    //     if ($request->isAjax) {
    //         Yii::$app->response->format = Response::FORMAT_JSON;

    //         if ($model->load($request->post())) {
    //             // Store the raw signature before encryption
    //             $rawSignature = $model->signature_base64;

    //             // Make sure we're getting the full data URL format
    //             // if (strpos($rawSignature, 'data:image') !== 0) {
    //             //     return [
    //             //         'success' => false,
    //             //         'message' => 'Invalid signature format',
    //             //     ];
    //             // }

    //             // Encrypt the signature using the same method as non-AJAX
    //             $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
    //             $model->signature_base64 = $encryptedSignature;
    //             $model->user_id = Yii::$app->user->id;
    //             $model->signed_at = date('Y-m-d H:i:s');
    //             $model->ip_address = Yii::$app->request->userIP;
    //             $model->post_id = $id;
    //             $model->user_agent = Yii::$app->request->userAgent;

    //             if ($model->save()) {
    //                 return [
    //                     'success' => true,
    //                     'forceReload' => '#crud-datatable-pjax',
    //                     'title' => "Signature Added",
    //                     'content' => '<span class="text-success">Signature saved successfully</span>',
    //                     'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal'])
    //                 ];
    //             } else {
    //                 return [
    //                     'success' => false,
    //                     'errors' => $model->getErrors(),
    //                 ];
    //             }
    //         }

    //         return [
    //             'success' => false,
    //             'message' => 'Invalid request.',
    //         ];
    //     } else {
    //         // Handle non-AJAX requests (optional)
    //         if ($model->load($request->post())) {
    //             $rawSignature = $model->signature_base64;
    //             $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
    //             $model->signature_base64 = $encryptedSignature;
    //             $model->user_id = Yii::$app->user->id;
    //             $model->signed_at = date('Y-m-d H:i:s');
    //             $model->ip_address = Yii::$app->request->userIP;
    //             $model->post_id = $id;
    //             $model->user_agent = Yii::$app->request->userAgent;

    //             if ($model->save()) {
    //                 return $this->redirect(['view', 'id' => $model->id]);
    //             }
    //         }
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    // public function actionSaveSignature($id)
    // {
    //     $request = Yii::$app->request;
    //     $model = new \common\models\PostsSignature();

    //     if ($request->isAjax) {
    //         Yii::$app->response->format = Response::FORMAT_JSON;

    //         // Debug: Log all POST data
    //         \Yii::error('POST data: ' . print_r($request->post(), true), 'signature-debug');

    //         if ($model->load($request->post())) {
    //             // Debug: Log model attributes after loading
    //             \Yii::error('Model attributes after load: ' . print_r($model->attributes, true), 'signature-debug');

    //             $rawSignature = $model->signature_base64;

    //             // Debug: Check if signature is empty
    //             if (empty($rawSignature)) {
    //                 \Yii::error('Signature is empty after model load', 'signature-debug');
    //                 return [
    //                     'success' => false,
    //                     'message' => 'No signature data received',
    //                     'debug' => [
    //                         'post_data' => $request->post(),
    //                         'model_attributes' => $model->attributes
    //                     ]
    //                 ];
    //             }

    //             // Validate signature format
    //             if (strpos($rawSignature, 'data:image') !== 0) {
    //                 \Yii::error('Invalid signature format: ' . substr($rawSignature, 0, 50), 'signature-debug');
    //                 return [
    //                     'success' => false,
    //                     'message' => 'Invalid signature format received',
    //                 ];
    //             }

    //             // Encrypt the signature
    //             $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
    //             $model->signature_base64 = $encryptedSignature;
    //             $model->user_id = Yii::$app->user->id;
    //             $model->signed_at = date('Y-m-d H:i:s');
    //             $model->ip_address = Yii::$app->request->userIP;
    //             $model->post_id = $id;
    //             $model->user_agent = Yii::$app->request->userAgent;

    //             if ($model->save()) {
    //                 \Yii::error('Signature saved successfully', 'signature-debug');
    //                 return [
    //                     'success' => true,
    //                     'forceReload' => '#crud-datatable-pjax',
    //                     'message' => 'Signature saved successfully'
    //                 ];
    //             } else {
    //                 \Yii::error('Model save failed: ' . print_r($model->getErrors(), true), 'signature-debug');
    //                 return [
    //                     'success' => false,
    //                     'message' => 'Failed to save signature',
    //                     'errors' => $model->getErrors(),
    //                 ];
    //             }
    //         } else {
    //             \Yii::error('Model load failed. POST data: ' . print_r($request->post(), true), 'signature-debug');
    //             return [
    //                 'success' => false,
    //                 'message' => 'Failed to load form data',
    //                 'debug' => [
    //                     'post_data' => $request->post(),
    //                     'model_errors' => $model->getErrors()
    //                 ]
    //             ];
    //         }
    //     } else {
    //         // Handle non-AJAX requests
    //         if ($model->load($request->post())) {
    //             $rawSignature = $model->signature_base64;
    //             $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
    //             $model->signature_base64 = $encryptedSignature;
    //             $model->user_id = Yii::$app->user->id;
    //             $model->signed_at = date('Y-m-d H:i:s');
    //             $model->ip_address = Yii::$app->request->userIP;
    //             $model->post_id = $id;
    //             $model->user_agent = Yii::$app->request->userAgent;

    //             if ($model->save()) {
    //                 return $this->redirect(['view', 'id' => $model->id]);
    //             }
    //         }
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    public function actionSaveSignature($id)
    {
        $request = Yii::$app->request;
        $model = new \common\models\PostsSignature();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load($request->post())) {
                $rawSignature = $model->signature_base64;

                // Check if signature is empty
                if (empty($rawSignature)) {
                    return [
                        'success' => false,
                        'message' => 'No signature data received. Please try again.',
                    ];
                }

                // Validate signature format
                if (strpos($rawSignature, 'data:image') !== 0) {
                    return [
                        'success' => false,
                        'message' => 'Invalid signature format. Please clear and try again.',
                    ];
                }

                try {
                    // Encrypt the signature
                    $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
                    $model->signature_base64 = $encryptedSignature;
                    $model->user_id = Yii::$app->user->id;
                    $model->signed_at = date('Y-m-d H:i:s');
                    $model->ip_address = Yii::$app->request->userIP;
                    $model->post_id = $id;
                    $model->user_agent = Yii::$app->request->userAgent;

                    if ($model->save()) {
                        // Log successful save
                        Yii::info("Signature saved successfully for user {$model->user_id} on post {$id}", 'signature');

                        return [
                            'success' => true,
                            'forceReload' => '#crud-datatable-pjax',
                            'message' => 'Your signature has been placed successfully!'
                        ];
                    } else {
                        // Log validation errors
                        Yii::error('Signature save failed: ' . print_r($model->getErrors(), true), 'signature');

                        $errorMessages = [];
                        foreach ($model->getErrors() as $field => $errors) {
                            $errorMessages[] = implode(', ', $errors);
                        }

                        return [
                            'success' => false,
                            'message' => 'Validation failed: ' . implode('. ', $errorMessages),
                            'model' => $model
                        ];
                    }
                } catch (\Exception $e) {
                    // Log the exception
                    Yii::error('Exception during signature save: ' . $e->getMessage(), 'signature');

                    return [
                        'success' => false,
                        'message' => 'An error occurred while processing your signature. Please try again.',
                    ];
                }
            } else {
                // Log form load failure
                Yii::error('Failed to load signature form data: ' . print_r($request->post(), true), 'signature');

                return [
                    'success' => false,
                    'message' => 'Failed to process form data. Please refresh and try again.',
                ];
            }
        } else {
            // Handle non-AJAX requests
            Yii::$app->session->removeAllFlashes();

            // Only process form if it was actually submitted
            if ($request->isPost && $model->load($request->post())) {
                $rawSignature = $model->signature_base64;

                try {
                    $encryptedSignature = SignatureHelper::encryptAndEncode($rawSignature, Yii::$app->user->id);
                    $model->signature_base64 = $encryptedSignature;
                    $model->user_id = Yii::$app->user->id;
                    $model->signed_at = date('Y-m-d H:i:s');
                    $model->ip_address = Yii::$app->request->userIP;
                    $model->post_id = $id;
                    $model->user_agent = Yii::$app->request->userAgent;

                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'Signature placed successfully!');
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $model->addError('signature_base64', 'Failed to save signature.');
                    }
                } catch (\Exception $e) {
                    $model->addError('signature_base64', 'An error occurred while saving the signature.');
                }
            }

            // Create a fresh model for the form if not processing submission
            if (!$request->isPost) {
                $model = new \common\models\PostsSignature();
                $model->post_id = $id; // Set the post_id for the form
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
}
