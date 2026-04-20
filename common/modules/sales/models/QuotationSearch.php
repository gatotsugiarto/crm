<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\Quotation;

/**
 * QuotationSearch represents the model behind the search form of `common\modules\sales\models\Quotation`.
 */
class QuotationSearch extends Quotation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'opportunity_id', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['quotation_number', 'quotation_date', 'valid_until', 'status', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
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
        $query = Quotation::find();

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
            'opportunity_id' => $this->opportunity_id,
            'quotation_date' => $this->quotation_date,
            'valid_until' => $this->valid_until,
            'total_amount' => $this->total_amount,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'quotation_number', $this->quotation_number])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
