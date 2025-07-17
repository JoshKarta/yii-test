<?php

namespace frontend\controllers;

use Yii;
use common\models\MockPosts;
use common\models\search\MockPostsSearch;
use common\models\search\PostsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;

/**
 * MockPostsController implements the CRUD actions for MockPosts model.
 */
class MockPostsController extends Controller
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

    protected function fetchData($apiUrl, $parameter = null, $bearerToken, $method = 'GET')
    {
        $client = new \yii\httpclient\Client();

        if ($parameter) {
            $apiUrl .= '?' . $parameter;
        }

        $response = $client->createRequest()
            ->setMethod($method)
            ->setUrl($apiUrl)
            ->addHeaders(['Authorization' => 'Bearer ' . $bearerToken])
            ->send();

        if ($response->isOk && isset($response->data['data'])) {
            return $response->data['data'];
        }
        return null;
    }

    /**
     * Fetches data from the external API and returns it as an array.
     * @return array|null
     */
    protected function fetchApiData()
    {
        $apiUrl = 'http://localhost:9000/api/posts';
        $bearerToken = 'F_RoBKDdxtV8DKPoqNeAqtAN2byl-95-V0LNgkz_W7VtUHSvFMJc0KfZPi8-xfVA';
        $client = new \yii\httpclient\Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($apiUrl)
            ->addHeaders(['Authorization' => 'Bearer ' . $bearerToken])
            ->send();

        if ($response->isOk && isset($response->data['data'])) {
            return $response->data['data'];
        }
        return null;
    }

    protected function fetchSyncData()
    {
        $apiUrl = 'http://localhost:9000/api/sync?synced=0';
        $bearerToken = 'lB9XAvqRSFNU6SYrSzImapPjfP_1xx70DNGvMQSrGGVHiEWEiyz7QwNqw3o8PvBO';
        $client = new \yii\httpclient\Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($apiUrl)
            ->addHeaders(['Authorization' => 'Bearer ' . $bearerToken])
            ->send();

        if ($response->isOk && isset($response->data['data'])) {
            return $response->data['data'];
        }
        return null;
    }



    /**
     * Lists all MockPosts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MockPostsSearch();
        $modelDataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Fetch Synced data 
        $syncData = $this->fetchSyncData();
        $apiData = [];

        if (empty($syncData) || !MockPosts::find()->exists()) {
            // If syncData is empty, fetch all API data
            $apiData = $this->fetchApiData();
        } else {
            // If syncData is not empty, fetch each record by its ID from the API
            foreach ($syncData as $item) {
                $apiData[] = $this->findRecord($item['pk']);
                // Update synced status after fetching
            }
        }


        $APIdataProvider = new ArrayDataProvider([
            'allModels' => $apiData ?? [],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $APIdataProvider,
            'apiData' => $apiData,
            'modelDataProvider' => $modelDataProvider,
        ]);
    }


    /**
     * Displays a single MockPosts model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "MockPosts #" . $id,
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
     * Creates a new MockPosts model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new MockPosts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " MockPosts",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Create'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " MockPosts",
                    'content' => '<span class="text-success">' . Yii::t('yii2-ajaxcrud', 'Create') . ' MockPosts ' . Yii::t('yii2-ajaxcrud', 'Success') . '</span>',
                    'footer' =>  Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Create New') . " MockPosts",
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
     * Updates an existing MockPosts model.
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
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " MockPosts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::button(Yii::t('yii2-ajaxcrud', 'Save'), ['class' => 'btn btn-primary', 'type' => 'submit'])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "MockPosts #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button(Yii::t('yii2-ajaxcrud', 'Close'), ['class' => 'btn btn-default pull-left', 'data-bs-dismiss' => 'modal']) .
                        Html::a(Yii::t('yii2-ajaxcrud', 'Update'), ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => Yii::t('yii2-ajaxcrud', 'Update') . " MockPosts #" . $id,
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
     * Delete an existing MockPosts model.
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
     * Delete multiple existing MockPosts model.
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
            return ['forceClose' => true, 'forceReload' => '#model-datatable'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the MockPosts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MockPosts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MockPosts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Fetch a single record from the API by ID.
     * @param integer|string $id
     * @return array|null
     * @throws NotFoundHttpException if the record cannot be found
     */
    protected function findRecord($id)
    {
        $apiData = $this->fetchApiData();
        if ($apiData !== null && is_array($apiData)) {
            foreach ($apiData as $record) {
                // Adjust 'id' to your API's primary key field if different
                if (isset($record['id']) && $record['id'] == $id) {
                    return $record;
                }
            }
        }
        throw new NotFoundHttpException('Record not found in the API.');
    }

    /**
     * Add selected MockPosts models.
     * For ajax request will return json object
     * and for non-ajax request if addition is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddSelected()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array of selected record primary keys

        // Define your mapping: 'mock_posts_field' => 'api_field'
        $mapping = [
            'source_id' => 'id',           // mock_posts.source_id = apiData.id
            'title'     => 'title',        // mock_posts.title = apiData.title
            'type'      => 'post_type',         // mock_posts.type = apiData.type
            'status'    => 'status',       // mock_posts.status = apiData.status
            'content'   => 'content',      // mock_posts.content = apiData.content
            'author'    => 'author',       // mock_posts.author = apiData.author
            'published_at' => 'created_at'
            // Add more as needed
        ];

        $inserted = [];
        foreach ($pks as $pk) {
            $apiRecord = $this->findRecord($pk);
            $model = new MockPosts();
            foreach ($mapping as $modelAttr => $apiAttr) {
                $model->$modelAttr = $apiRecord[$apiAttr] ?? null;
            }

            // Check if source_id exists
            $existing = MockPosts::findOne(['source_id' => $model->source_id]);
            if ($existing) {
                $existing->delete();
            }

            if ($model->save(false)) {
                $inserted[] = $model->attributes;

                // Update the sync status for this record
                $syncUpdateSuccess = $this->updateSyncStatus($pk);
                if (!$syncUpdateSuccess) {
                    Yii::warning("Failed to update sync status for record with pk: {$pk}", __METHOD__);
                }
            }
        }

        // if ($request->isAjax) {
        //     Yii::$app->response->format = Response::FORMAT_JSON;
        //     return [
        //         'title' => 'Inserted MockPosts',
        //         'content' => $this->renderAjax('_selected_api_data', [
        //             'records' => $inserted,
        //         ]),
        //         'footer' => Html::button('Close', [
        //             'class' => 'btn btn-default pull-left',
        //             'data-bs-dismiss' => 'modal'
        //         ]),
        //     ];
        // } else {
        //     echo '<pre>';
        //     print_r($inserted);
        //     exit;
        // }
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#model-datatable'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates the synced status for a record via PUT request.
     * @param integer|string $id
     * @return bool
     */ protected function updateSyncStatus($id)
    {
        $apiUrl      = 'http://localhost:9000/api/sync';   // ✅ resource only
        $bearerToken = 'lB9XAvqRSFNU6SYrSzImapPjfP_1xx70DNGvMQSrGGVHiEWEiyz7QwNqw3o8PvBO';

        $client   = new \yii\httpclient\Client();
        $response = $client->createRequest()
            ->setMethod('PUT')
            ->setUrl($apiUrl)
            ->addHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])
            ->setContent(json_encode([
                'pk'     => $id,
                'synced' => 1,
            ]))
            ->send();

        // optional: log the response for debugging
        Yii::info('Sync API response: ' . $response->getContent(), __METHOD__);

        return $response->isOk;
    }
}
