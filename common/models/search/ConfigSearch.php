<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Config;

class ConfigSearch extends Config
{
    public $categoryName; // Add this for category filtering

    public function rules()
    {
        return [
            [['id', 'category_id', 'sort_order', 'is_system', 'created_by', 'updated_by'], 'integer'],
            [['key', 'value', 'type', 'description', 'categoryName', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Config::find()->joinWith(['category']); // Use joinWith instead of with for filtering

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Enable sorting on the related column
        $dataProvider->sort->attributes['categoryName'] = [
            'asc' => ['config_category.name' => SORT_ASC],
            'desc' => ['config_category.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'type' => $this->type,
            'is_system' => $this->is_system,
            'sort_order' => $this->sort_order,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'config_category.name', $this->categoryName]); // Filter by category name

        // Date filters
        if (!empty($this->created_at)) {
            $query->andFilterWhere(['DATE(created_at)' => $this->created_at]);
        }

        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['DATE(updated_at)' => $this->updated_at]);
        }

        return $dataProvider;
    }
}
