<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FormResponse;

/**
 * FormResponseSearch represents the model behind the search form about `common\models\FormResponse`.
 */
class FormResponseSearch extends FormResponse
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'form_id'], 'integer'],
            [['response_json', 'submitted_at'], 'safe'],
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
        $query = FormResponse::find();

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
            'form_id' => $this->form_id,
            'submitted_at' => $this->submitted_at,
        ]);

        $query->andFilterWhere(['like', 'response_json', $this->response_json]);

        return $dataProvider;
    }
}
