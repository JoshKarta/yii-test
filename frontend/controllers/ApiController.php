<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\TooManyRequestsHttpException;
use common\models\Api;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'update' => ['PUT'],
                ],
            ],
        ];
    }


    private function enforceRateLimit(Api $api)
    {
        $now = new \DateTime();
        $reset = $api->rate_limit_reset_at ? new \DateTime($api->rate_limit_reset_at) : null;

        if (!$reset || $now >= $reset) {
            $api->rate_limit_remaining = $api->rate_limit;
            $api->rate_limit_reset_at = (clone $now)->modify('+1 hour')->format('Y-m-d H:i:s');
        }

        if ($api->rate_limit_remaining <= 0) {
            throw new TooManyRequestsHttpException('Rate limit exceeded. Try again after ' . $api->rate_limit_reset_at);
        }

        $api->rate_limit_remaining--;
        $api->save(false);

        // Optional headers
        Yii::$app->response->headers->set('X-RateLimit-Limit', $api->rate_limit);
        Yii::$app->response->headers->set('X-RateLimit-Remaining', $api->rate_limit_remaining);
        Yii::$app->response->headers->set('X-RateLimit-Reset', strtotime($api->rate_limit_reset_at));
    }

    public function actionIndex($resource)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Token validation
            $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
            if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
                throw new \yii\web\UnauthorizedHttpException('Missing or invalid token.');
            }

            // Searcg for the record in api table where the token, name match and is active
            $token = $matches[1];
            $api = \common\models\Api::findOne(['token' => $token, 'is_active' => 1, 'name' => $resource]);

            if (!$api) {
                throw new \yii\web\ForbiddenHttpException('Invalid token or API not allowed.');
            }

            // ✅ Enforce rate limiting
            $this->enforceRateLimit($api);

            // Decode fields
            $fields = json_decode($api->allowed_fields, true);
            if (!is_array($fields)) {
                $fields = array_map('trim', explode(',', (string)$api->allowed_fields));
            }

            // ✅ Build the query
            $query = (new \yii\db\Query())->from($api->table_name);
            $selectFields = array_map(fn($f) => "{$api->table_name}.{$f}", $fields);


            if (empty($selectFields)) {
                throw new \Exception("No fields selected. Check 'allowed_fields' and 'relation_definitions'.");
            }

            // Select all necessary fields
            $query->select($selectFields);

            // For filtering
            $request = Yii::$app->request->get();
            $search = $request['s'] ?? null;

            // Exact field matches (e.g., ?status=1)
            foreach ($request as $key => $value) {
                if ($key !== 's' && in_array($key, $fields)) {
                    $query->andWhere([$key => $value]);
                }
            }

            // Global fuzzy search (e.g., ?s=john)
            if ($search) {
                $orConditions = ['or'];
                foreach ($fields as $field) {
                    $orConditions[] = ['like', "{$api->table_name}.$field", $search];
                }
                $query->andWhere($orConditions);
            }

            // ✅ Optional: log the raw SQL for debugging
            Yii::debug($query->createCommand()->getRawSql(), __METHOD__);

            // Execute and process results
            $data = $query->all();
            $finalData = [];

            foreach ($data as $row) {
                $main = [];
                $relations = [];

                foreach ($row as $key => $value) {
                    if (preg_match('/^(\w+_rel_\d+)_(.+)$/', $key, $matches)) {
                        [$_, $alias, $field] = $matches;
                        $tableKey = explode('_rel_', $alias)[0];
                        $relations[$tableKey][$field] = $value;
                    } else {
                        $main[$key] = $value;
                    }
                }

                foreach ($relations as $relKey => $relData) {
                    $main[$relKey] = $relData;
                }

                $finalData[] = $main;
            }

            return ['data' => $finalData];
        } catch (\yii\web\HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['error' => $e->getMessage()];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => $e->getMessage()]; // ⚠️ Show during debugging only
        }
    }

    // public function actionPost($resource)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     try {
    //         // Validate token
    //         $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
    //         if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
    //             throw new \yii\web\UnauthorizedHttpException('Missing or invalid token.');
    //         }

    //         $token = $matches[1];
    //         $api = \common\models\Api::findOne(['token' => $token, 'is_active' => 1, 'name' => $resource]);

    //         if (!$api) {
    //             throw new \yii\web\ForbiddenHttpException('Invalid token or API not allowed.');
    //         }

    //         // ✅ Enforce rate limiting
    //         $this->enforceRateLimit($api);

    //         // Validate POST body
    //         $data = Yii::$app->request->getBodyParams();   // not ->post()
    //         if (empty($data)) {
    //             throw new \yii\web\BadRequestHttpException('No data sent.');
    //         }

    //         $allowedFields = $api->getAllowedFieldsArray();

    //         // Filter and prepare only allowed fields
    //         $insertData = array_intersect_key($data, array_flip($allowedFields));
    //         if (empty($insertData)) {
    //             throw new \yii\web\BadRequestHttpException('No allowed fields present in request.');
    //         }

    //         // Use DB command to insert or update
    //         $db = Yii::$app->db;
    //         $transaction = $db->beginTransaction();

    //         try {
    //             // Use primary key if available for update (you can extend this logic)
    //             $tableSchema = $db->getTableSchema($api->table_name);
    //             $primaryKey = $tableSchema->primaryKey[0] ?? null;

    //             if ($primaryKey && isset($data[$primaryKey])) {
    //                 $updated = $db->createCommand()
    //                     ->update($api->table_name, $insertData, [$primaryKey => $data[$primaryKey]])
    //                     ->execute();
    //                 $message = $updated ? 'Updated successfully.' : 'No rows affected.';
    //             } else {
    //                 $db->createCommand()->insert($api->table_name, $insertData)->execute();
    //                 $message = 'Inserted successfully.';
    //             }

    //             $transaction->commit();
    //             return ['success' => true, 'message' => $message];
    //         } catch (\Throwable $e) {
    //             $transaction->rollBack();
    //             throw $e;
    //         }
    //     } catch (\yii\web\HttpException $e) {
    //         Yii::$app->response->statusCode = $e->statusCode;
    //         return ['error' => $e->getMessage()];
    //     } catch (\Throwable $e) {
    //         Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
    //         Yii::$app->response->statusCode = 500;
    //         return ['error' => $e->getMessage()];
    //     }
    // }

    public function actionUpdate($resource)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Token validation
            $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
            if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
                throw new \yii\web\UnauthorizedHttpException('Missing or invalid token.');
            }

            $token = $matches[1];
            $api = \common\models\Api::findOne(['token' => $token, 'is_active' => 1, 'name' => $resource]);

            if (!$api) {
                throw new \yii\web\ForbiddenHttpException('Invalid token or API not allowed.');
            }

            // ✅ Enforce rate limiting
            $this->enforceRateLimit($api);

            $data = Yii::$app->request->getBodyParams();

            $raw = file_get_contents('php://input');
            Yii::info("RAW BODY: " . $raw, __METHOD__);

            $data = Yii::$app->request->getBodyParams();
            Yii::info("PARSED BODY: " . json_encode($data), __METHOD__);


            if (!isset($data['pk'])) {
                throw new \yii\web\BadRequestHttpException("Missing primary key ('pk') in request body.");
            }

            $pk = $data['pk'];
            unset($data['pk']);

            if (empty($data)) {
                throw new \yii\web\BadRequestHttpException("No data provided to update.");
            }

            // ✅ Whitelist fields
            $allowedFields = $api->getAllowedFieldsArray();
            $updateData = [];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updateData[$field] = $value;
                }
            }

            if (empty($updateData)) {
                throw new \yii\web\BadRequestHttpException("None of the provided fields are allowed for update.");
            }

            // ✅ Get the table's primary key column dynamically
            $tableSchema = Yii::$app->db->getTableSchema($api->table_name);
            $primaryKey = $tableSchema->primaryKey[0] ?? 'id'; // fallback to 'id' if no primary key found

            // ✅ Special handling for sync_data table - update by 'pk' field instead of primary key
            if ($api->table_name === 'sync_data') {
                // Update all unsynced records with the same pk
                $whereCondition = ['pk' => $pk, 'synced' => 0];
            } else {
                $whereCondition = [$primaryKey => $pk];
            }

            // ✅ Run the update
            $affected = Yii::$app->db->createCommand()
                ->update($api->table_name, $updateData, $whereCondition)
                ->execute();

            if ($affected > 0) {
                return ['success' => true, 'message' => "Record #$pk updated."];
            }

            return ['success' => false, 'message' => "No changes made or record not found."];
        } catch (\yii\web\HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return ['error' => $e->getMessage()];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Internal Server Error.'];
        }
    }
}
