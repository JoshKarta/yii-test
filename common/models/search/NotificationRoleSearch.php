<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NotificationRole;

/**
 * NotificationRoleSearch represents the model behind the search form about `common\models\NotificationRole`.
 */
class NotificationRoleSearch extends NotificationRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'role_id'], 'integer'],
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
        $query = NotificationRole::find();

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
            'notification_id' => $this->notification_id,
            'role_id' => $this->role_id,
        ]);

        return $dataProvider;
    }
}
