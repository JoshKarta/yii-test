<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\TooManyRequestsHttpException;
use common\models\Api;

class ApiController2 extends Controller
{
    public function behaviors()
    {
        return [];
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


    // public function actionIndex($resource)
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     try {
    //         $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
    //         if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
    //             throw new \yii\web\UnauthorizedHttpException('Missing or invalid token.');
    //         }

    //         $token = $matches[1];
    //         $api = \common\models\Api::findOne(['access_token' => $token, 'is_active' => 1]);

    //         if (!$api || $api->table_name !== $resource) {
    //             throw new \yii\web\ForbiddenHttpException('Invalid token or API not allowed.');
    //         }

    //         // ✅ Enforce rate limit
    //         $this->enforceRateLimit($api);

    //         $query = (new \yii\db\Query())
    //             ->select($api->allowed_fields)
    //             ->from($api->table_name);

    //         $data = $query->all();
    //         return ['data' => $data];
    //     } catch (\Throwable $e) {
    //         Yii::error($e->getMessage(), __METHOD__);
    //         Yii::$app->response->statusCode = 500;
    //         return ['error' => $e->getMessage()];
    //     }
    // }

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

            // Handle relations
            // $relations = json_decode($api->relations, true);
            // $relationSelectMap = [];

            // if (is_array($relations)) {
            //     foreach ($relations as $index => $rel) {
            //         if (
            //             isset($rel['table'], $rel['on'], $rel['fields']) &&
            //             preg_match('/^[a-zA-Z0-9_]+$/', $rel['table'])
            //         ) {
            //             $alias = $rel['table'] . "_rel_$index";

            //             // Add join
            //             $query->leftJoin("$rel[table] $alias", $rel['on']);

            //             foreach ($rel['fields'] as $field) {
            //                 if ($field === "$rel[table].*") {
            //                     try {
            //                         $columns = Yii::$app->db->getTableSchema($rel['table'])->getColumnNames();
            //                     } catch (\Throwable $e) {
            //                         throw new \Exception("Invalid relation table '{$rel['table']}' or it does not exist.");
            //                     }

            //                     foreach ($columns as $column) {
            //                         $aliasName = "{$alias}_{$column}";
            //                         $selectFields[] = "$alias.$column AS $aliasName";
            //                         $relationSelectMap[$rel['on']][] = [
            //                             'alias' => $alias,
            //                             'field' => $column,
            //                             'as' => $aliasName,
            //                             'key' => $rel['table'],
            //                         ];
            //                     }
            //                 } else {
            //                     $column = str_replace("$rel[table].", '', $field);
            //                     $aliasName = "{$alias}_{$column}";
            //                     $selectFields[] = "$alias.$column AS $aliasName";
            //                     $relationSelectMap[$rel['on']][] = [
            //                         'alias' => $alias,
            //                         'field' => $column,
            //                         'as' => $aliasName,
            //                         'key' => $rel['table'],
            //                     ];
            //                 }
            //             }
            //         }
            //     }
            // }

            if (empty($selectFields)) {
                throw new \Exception("No fields selected. Check 'allowed_fields' and 'relation_definitions'.");
            }

            // Select all necessary fields
            $query->select($selectFields);

            // ✅ Apply filters (?name=John&status=1)
            // $allowedFields = is_array($api->allowed_fields)
            //     ? $api->allowed_fields
            //     : array_map('trim', explode(',', (string)$api->allowed_fields));

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
}
