<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\OpportunityProduct;

/**
 * OpportunityProductSearch represents the model behind the search form of `common\modules\sales\models\OpportunityProduct`.
 */
class OpportunityProductSearch extends OpportunityProduct
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'opportunity_id', 'product_id', 'qty', 'is_upsell'], 'integer'],
            [['price', 'discount', 'total'], 'number'],
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
        $query = OpportunityProduct::find();

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
            'product_id' => $this->product_id,
            'qty' => $this->qty,
            'price' => $this->price,
            'discount' => $this->discount,
            'total' => $this->total,
            'is_upsell' => $this->is_upsell,
        ]);

        return $dataProvider;
    }
}
