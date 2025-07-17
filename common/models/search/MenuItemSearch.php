<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MenuItem;

/**
 * MenuItemSearch represents the model behind the search form about `common\models\MenuItem`.
 */
class MenuItemSearch extends MenuItem
{
    public $globalSearch;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'sort_order'], 'integer'],
            [['label', 'url', 'icon', 'icon_type', 'location', 'target', 'visible', 'created_at', 'updated_at'], 'safe'],
            [['globalSearch'], 'safe'],
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
        $query = MenuItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Add global search condition
        if (!empty($this->globalSearch)) {
            $query->andWhere([
                'or',
                ['like', 'label', $this->globalSearch],
                ['like', 'url', $this->globalSearch],
                ['like', 'icon', $this->globalSearch],
                ['like', 'icon_type', $this->globalSearch],
                ['like', 'location', $this->globalSearch],
                ['like', 'target', $this->globalSearch],
                ['like', 'visible', $this->globalSearch],
                // Add other fields you want to search
            ]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'icon_type', $this->icon_type])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'visible', $this->visible]);

        return $dataProvider;
    }
}
