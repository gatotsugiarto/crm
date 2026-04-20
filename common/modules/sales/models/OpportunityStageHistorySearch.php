<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\OpportunityStageHistory;

/**
 * OpportunityStageHistorySearch represents the model behind the search form of `common\modules\sales\models\OpportunityStageHistory`.
 */
class OpportunityStageHistorySearch extends OpportunityStageHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'opportunity_id', 'changed_by', 'days_in_previous_stage'], 'integer'],
            [['old_stage', 'new_stage', 'changed_at', 'description'], 'safe'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = OpportunityStageHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'opportunity_id' => $this->opportunity_id,
            'changed_at' => $this->changed_at,
            'changed_by' => $this->changed_by,
            'days_in_previous_stage' => $this->days_in_previous_stage,
        ]);

        $query->andFilterWhere(['like', 'old_stage', $this->old_stage])
            ->andFilterWhere(['like', 'new_stage', $this->new_stage])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
