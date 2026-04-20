<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\Salary;

/**
 * SalarySearch represents the model behind the search form of `common\modules\master\models\Salary`.
 */
class SalarySearch extends Salary
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'payroll_item_id', 'status_id', 'is_processed', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['insert_by', 'employee_id', 'processed_at', 'created_at', 'updated_at', 'salary_type'], 'safe'],
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
        $query = Salary::find();
        $query->joinWith(['employee']);
        $query->joinWith(['payrollItem']);

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
            'salary.payroll_item_id' => $this->payroll_item_id,
            'salary.status_id' => $this->status_id,
            'salary.insert_by' => $this->insert_by,
            'salary.is_processed' => $this->is_processed,
            'salary.processed_at' => $this->processed_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        if($this->employee_id){
            $this->employee_id = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($this->employee_id));
        }

        $query->andFilterWhere(['like', 'salary.amount', $this->amount])
            ->andFilterWhere(['like', 'payroll_item.salary_type', $this->salary_type])
            ->andFilterWhere(['like', 'employee.fullname', $this->employee_id]);

        // 🔍 Debug SQL here
        // echo $query->createCommand()->getRawSql(); exit;

        return $dataProvider;
    }
}
