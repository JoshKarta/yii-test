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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_system', 'sort_order', 'created_by', 'updated_by'], 'integer'],
            [['category', 'key', 'value', 'type', 'description', 'created_at', 'updated_at', 'globalSearch'], 'safe'],
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
        $query = Config::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'category' => SORT_ASC,
                    'sort_order' => SORT_ASC,
                    'key' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'is_system' => $this->is_system,
            'sort_order' => $this->sort_order,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        // Date range filtering
        if (!empty($this->created_at)) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at]);
        }

        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['>=', 'updated_at', $this->updated_at]);
        }

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);

        // Global search
        if (!empty($this->globalSearch)) {
            $searchTerm = $this->globalSearch;

            $query->andWhere([
                'or',
                ['like', 'category', $searchTerm],
                ['like', 'key', $searchTerm],
                ['like', 'value', $searchTerm],
                ['like', 'description', $searchTerm],
            ]);
        }

        return $dataProvider;
    }
}
