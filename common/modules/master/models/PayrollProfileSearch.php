<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\PayrollProfile;

/**
 * PayrollProfileSearch represents the model behind the search form of `common\modules\master\models\PayrollProfile`.
 */
class PayrollProfileSearch extends PayrollProfile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['profile_name', 'payroll_mode', 'created_at', 'updated_at'], 'safe'],
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
        $query = PayrollProfile::find();

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
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'profile_name', $this->profile_name])
            ->andFilterWhere(['like', 'payroll_mode', $this->payroll_mode]);

        return $dataProvider;
    }
}
