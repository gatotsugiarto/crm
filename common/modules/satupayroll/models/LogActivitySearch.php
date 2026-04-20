<?php

namespace common\modules\satupayroll\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\satupayroll\models\LogActivity;

/**
 * LogActivitySearch represents the model behind the search form of `common\modules\satupayroll\models\LogActivity`.
 */
class LogActivitySearch extends LogActivity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['employee_id', 'action_by', 'record_id', 'controller_action', 'model_name', 'created_at', 'ip_address', 'user_agent', 'request_url', 'before_data', 'after_data', 'status', 'remarks'], 'safe'],
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
        $query = LogActivity::find();
        $query->joinWith(['employee']);
        $query->joinWith(['user']);

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
            // 'record_id' => $this->record_id,
            // 'action_by' => $this->action_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'controller_action', $this->controller_action])
            ->andFilterWhere(['like', 'log_activity.id', $this->record_id])
            ->andFilterWhere(['like', 'model_name', $this->model_name])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'request_url', $this->request_url])
            ->andFilterWhere(['like', 'before_data', $this->before_data])
            ->andFilterWhere(['like', 'after_data', $this->after_data])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'employee.fullname', $this->employee_id])
            ->andFilterWhere(['like', 'user.fullname', $this->action_by])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
