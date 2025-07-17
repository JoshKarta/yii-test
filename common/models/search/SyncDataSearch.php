<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SyncData;

/**
 * SyncDataSearch represents the model behind the search form about `common\models\SyncData`.
 */
class SyncDataSearch extends SyncData
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['change_id', 'pk'], 'integer'],
            [['table_name', 'action', 'change_time'], 'safe'],
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
        $query = SyncData::find();

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
            'change_id' => $this->change_id,
            'pk' => $this->pk,
            'change_time' => $this->change_time,
        ]);

        $query->andFilterWhere(['like', 'table_name', $this->table_name])
            ->andFilterWhere(['like', 'action', $this->action]);

        return $dataProvider;
    }
}
