<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\EmployeePayrollProfile;

/**
 * EmployeePayrollProfileSearch represents the model behind the search form of `common\modules\master\models\EmployeePayrollProfile`.
 */
class EmployeePayrollProfileSearch extends EmployeePayrollProfile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['employee_id', 'profile_id', 'created_at', 'updated_at'], 'safe'],
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
        $query = EmployeePayrollProfile::find();
        $query->joinWith(['employee']);
        // $query->joinWith(['profile']);

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
            'profile_id' => $this->profile_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        if($this->employee_id){
            $this->employee_id = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($this->employee_id));
        }

        // if($this->profile_id){
        //     $this->profile_id = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($this->profile_id));
        // }

        // $query->andFilterWhere(['like', 'payroll_profile.profile_name', $this->profile_id])
        //     ->andFilterWhere(['like', 'employee.fullname', $this->employee_id]);

        $query->andFilterWhere(['like', 'employee.fullname', $this->employee_id]);

        return $dataProvider;
    }
}
