<?php

namespace common\modules\productprice\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\productprice\models\ProductDiscount;

/**
 * ProductDiscountSearch represents the model behind the search form of `common\modules\productprice\models\ProductDiscount`.
 */
class ProductDiscountSearch extends ProductDiscount
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'price_list_id', 'priority', 'min_qty', 'is_stackable', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['discount_type', 'valid_from', 'valid_to', 'created_at', 'updated_at'], 'safe'],
            [['discount_value'], 'number'],
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
        $query = ProductDiscount::find();

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

        $query->andFilterWhere([
            'product_id'    => $this->product_id,
            'price_list_id' => $this->price_list_id,
            'discount_type' => $this->discount_type,
            'is_stackable'  => $this->is_stackable,
            'status_id'     => $this->status_id,
        ]);

        $query->andFilterWhere(['>=', 'valid_from', $this->valid_from ?: null]);
        $query->andFilterWhere(['<=', 'valid_to',   $this->valid_to   ?: null]);

        return $dataProvider;
    }
}
