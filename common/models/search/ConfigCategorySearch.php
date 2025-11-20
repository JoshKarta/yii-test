<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Config;

/**
 * ConfigSearch represents the model behind the search form of `common\models\Config`.
 */
class ConfigSearch extends Config
{
    public $globalSearch;
    public $categoryName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'is_system', 'sort_order', 'created_by', 'updated_by'], 'integer'],
            [['key', 'value', 'type', 'description', 'created_at', 'updated_at', 'globalSearch', 'categoryName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Config::find()->joinWith(['category']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'category_id' => SORT_ASC,
                    'sort_order' => SORT_ASC,
                    'key' => SORT_ASC,
                ]
            ],
        ]);

        // Add sorting for category name
        $dataProvider->sort->attributes['categoryName'] = [
            'asc' => ['config_category.name' => SORT_ASC],
            'desc' => ['config_category.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'config.id' => $this->id,
            'config.category_id' => $this->category_id,
            'config.is_system' => $this->is_system,
            'config.sort_order' => $this->sort_order,
            'config.created_by' => $this->created_by,
            'config.updated_by' => $this->updated_by,
        ]);

        // Date range filtering
        if (!empty($this->created_at)) {
            $query->andFilterWhere(['>=', 'config.created_at', $this->created_at]);
        }

        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['>=', 'config.updated_at', $this->updated_at]);
        }

        $query->andFilterWhere(['like', 'config.key', $this->key])
            ->andFilterWhere(['like', 'config.value', $this->value])
            ->andFilterWhere(['like', 'config.type', $this->type])
            ->andFilterWhere(['like', 'config.description', $this->description])
            ->andFilterWhere(['like', 'config_category.name', $this->categoryName]);

        // Global search
        if (!empty($this->globalSearch)) {
            $searchTerm = $this->globalSearch;

            $query->andWhere([
                'or',
                ['like', 'config_category.name', $searchTerm],
                ['like', 'config.key', $searchTerm],
                ['like', 'config.value', $searchTerm],
                ['like', 'config.description', $searchTerm],
            ]);
        }

        return $dataProvider;
    }
}
