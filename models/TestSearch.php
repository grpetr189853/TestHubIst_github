<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Test;

/**
 * TestSearch represents the model behind the search form of `app\models\Test`.
 */
class TestSearch extends Test
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'minimum_score', 'time_limit', 'attempts'], 'integer'],
            [['name', 'foreword', 'create_time', 'deadline'], 'safe'],
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
        $query = Test::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'category_id' => $this->category_id,
            'minimum_score' => $this->minimum_score,
            'time_limit' => $this->time_limit,
            'attempts' => $this->attempts,
            'create_time' => $this->create_time,
            'deadline' => $this->deadline,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'foreword', $this->foreword]);

        return $dataProvider;
    }
}
