<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NotificationTrigger;

/**
 * NotificationTriggerSearch represents the model behind the search form about `common\models\NotificationTrigger`.
 */
class NotificationTriggerSearch extends NotificationTrigger
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['route', 'notification_key', 'model_class', 'model_id_param', 'fields', 'link_template'], 'safe'],
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
        $query = NotificationTrigger::find();

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
        ]);

        $query->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'notification_key', $this->notification_key])
            ->andFilterWhere(['like', 'model_class', $this->model_class])
            ->andFilterWhere(['like', 'model_id_param', $this->model_id_param])
            ->andFilterWhere(['like', 'fields', $this->fields])
            ->andFilterWhere(['like', 'link_template', $this->link_template]);

        return $dataProvider;
    }
}
