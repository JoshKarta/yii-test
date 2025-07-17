<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Api;

/**
 * ApiSearch represents the model behind the search form about `common\models\Api`.
 */
class ApiSearch extends Api
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rate_limit', 'rate_limit_remaining'], 'integer'],
            [['name', 'table_name', 'relations', 'is_active', 'token', 'rate_limit_reset_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Api::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'rate_limit' => $this->rate_limit,
            'rate_limit_remaining' => $this->rate_limit_remaining,
            'rate_limit_reset_at' => $this->rate_limit_reset_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'table_name', $this->table_name])
            ->andFilterWhere(['like', 'allowed_fields', $this->allowed_fields])
            ->andFilterWhere(['like', 'relations', $this->relations])
            ->andFilterWhere(['like', 'is_active', $this->is_active])
            ->andFilterWhere(['like', 'token', $this->token]);

        return $dataProvider;
    }
}
