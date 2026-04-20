<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\Activity;

/**
 * ActivitySearch represents the model behind the search form of `common\modules\sales\models\Activity`.
 */
class ActivitySearch extends Activity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'contact_id', 'opportunity_id', 'reference_id', 'assigned_to', 'is_completed', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['reference_type', 'activity_type', 'priority', 'subject', 'activity_date', 'due_date', 'reminder_at', 'completed_at', 'description', 'outcome', 'created_at', 'updated_at'], 'safe'],
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
        $query = Activity::find();

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
            'account_id' => $this->account_id,
            'contact_id' => $this->contact_id,
            'opportunity_id' => $this->opportunity_id,
            'reference_id' => $this->reference_id,
            'assigned_to' => $this->assigned_to,
            'activity_date' => $this->activity_date,
            'due_date' => $this->due_date,
            'reminder_at' => $this->reminder_at,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'reference_type', $this->reference_type])
            ->andFilterWhere(['like', 'activity_type', $this->activity_type])
            ->andFilterWhere(['like', 'priority', $this->priority])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'outcome', $this->outcome]);

        return $dataProvider;
    }
}
