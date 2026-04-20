<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\Employee;

/**
 * EmployeeSearch represents the model behind the search form of `common\modules\master\models\Employee`.
 */
class EmployeeSearch extends Employee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'company_id', 'branch_id', 'site_office_id', 'department_id', 'division_id', 'marital_status_id', 'family_status_id', 'ptkp_id', 'level_jabatan_id', 'jabatan_id', 'grade_id', 'is_npwp', 'jkk_id', 'bank_id', 'employee_status_id', 'join_prorate', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['region', 'company', 'branch', 'site_office', 'department', 'division', 'e_number', 'fullname', 'join_date', 'marital_status', 'family_status', 'ptkp', 'level_jabatan', 'jabatan', 'grade', 'email', 'npwp_id', 'bpjs_tk', 'bpjs_kes', 'jkk', 'bank', 'bank_no', 'employee_status', 'created_at', 'updated_at'], 'safe'],
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
        $query = Employee::find();

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
            'region_id' => $this->region_id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'site_office_id' => $this->site_office_id,
            'department_id' => $this->department_id,
            'division_id' => $this->division_id,
            'join_date' => $this->join_date,
            'marital_status_id' => $this->marital_status_id,
            'family_status_id' => $this->family_status_id,
            'ptkp_id' => $this->ptkp_id,
            'level_jabatan_id' => $this->level_jabatan_id,
            'jabatan_id' => $this->jabatan_id,
            'grade_id' => $this->grade_id,
            'is_npwp' => $this->is_npwp,
            'jkk_id' => $this->jkk_id,
            'bank_id' => $this->bank_id,
            'employee_status_id' => $this->employee_status_id,
            'join_prorate' => $this->join_prorate,
            'resign_date' => $this->resign_date,
            'resign_date' => $this->resign_date,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        if($this->fullname) $this->fullname = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($this->fullname));
        if($this->e_number) $this->e_number = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($this->e_number));

        $query->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'branch', $this->branch])
            ->andFilterWhere(['like', 'site_office', $this->site_office])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'division', $this->division])
            ->andFilterWhere(['like', 'e_number', $this->e_number])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'marital_status', $this->marital_status])
            ->andFilterWhere(['like', 'family_status', $this->family_status])
            ->andFilterWhere(['like', 'ptkp', $this->ptkp])
            ->andFilterWhere(['like', 'level_jabatan', $this->level_jabatan])
            ->andFilterWhere(['like', 'jabatan', $this->jabatan])
            ->andFilterWhere(['like', 'grade', $this->grade])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'npwp_id', $this->npwp_id])
            ->andFilterWhere(['like', 'bpjs_tk', $this->bpjs_tk])
            ->andFilterWhere(['like', 'bpjs_kes', $this->bpjs_kes])
            ->andFilterWhere(['like', 'jkk', $this->jkk])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'bank_no', $this->bank_no])
            ->andFilterWhere(['like', 'employee_status', $this->employee_status]);

        return $dataProvider;
    }
}
