<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\EmployeePending;

/**
 * EmployeePendingSearch represents the model behind the search form of `common\modules\master\models\EmployeePending`.
 */
class EmployeePendingSearch extends EmployeePending
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'employee_id', 'marital_status_id', 'family_status_id', 'ptkp_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['e_number', 'marital_status', 'family_status', 'ptkp', 'pending_type', 'pending_date', 'created_at', 'updated_at'], 'safe'],
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
        $query = EmployeePending::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'employee_id' => $this->employee_id,
            'marital_status_id' => $this->marital_status_id,
            'family_status_id' => $this->family_status_id,
            'ptkp_id' => $this->ptkp_id,
            'pending_date' => $this->pending_date,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'e_number', $this->e_number])
            ->andFilterWhere(['like', 'marital_status', $this->marital_status])
            ->andFilterWhere(['like', 'family_status', $this->family_status])
            ->andFilterWhere(['like', 'ptkp', $this->ptkp])
            ->andFilterWhere(['like', 'pending_type', $this->pending_type]);

        return $dataProvider;
    }
}
