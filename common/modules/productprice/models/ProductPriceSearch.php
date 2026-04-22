<?php

namespace common\modules\productprice\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\productprice\models\ProductPrice;

/**
 * ProductPriceSearch represents the model behind the search form of `common\modules\productprice\models\ProductPrice`.
 */
class ProductPriceSearch extends ProductPrice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'price_list_id', 'min_qty', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['valid_from', 'valid_to', 'created_at', 'updated_at'], 'safe'],
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
    // public function search($params, $formName = null)
    // {
    //     $query = ProductPrice::find();

    //     // add conditions that should always apply here

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //         'pagination' => [
    //             'pageSize' => 30,
    //         ],
    //         'sort' => [
    //             'defaultOrder' => [
    //                 'id' => SORT_DESC,
    //             ],
    //         ],
    //     ]);

    //     $this->load($params, $formName);

    //     if (!$this->validate()) {
    //         // uncomment the following line if you do not want to return any records when validation fails
    //         // $query->where('0=1');
    //         return $dataProvider;
    //     }

    //     // grid filtering conditions
    //     $query->andFilterWhere([
    //         'id' => $this->id,
    //         'product_id' => $this->product_id,
    //         'price_list_id' => $this->price_list_id,
    //         'price' => $this->price,
    //         'valid_from' => $this->valid_from,
    //         'valid_to' => $this->valid_to,
    //         'min_qty' => $this->min_qty,
    //         'status_id' => $this->status_id,
    //         'created_at' => $this->created_at,
    //         'created_by' => $this->created_by,
    //         'updated_at' => $this->updated_at,
    //         'updated_by' => $this->updated_by,
    //     ]);

    //     return $dataProvider;
    // }

    public function search($params)
    {
        $query = ProductPrice::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['price_list_id' => $this->price_list_id]);
        $query->andFilterWhere(['product_id'    => $this->product_id]);
        $query->andFilterWhere(['status_id'     => $this->status_id]);
        $query->andFilterWhere(['>=', 'valid_from', $this->valid_from ?: null]);
        $query->andFilterWhere(['<=', 'valid_to',   $this->valid_to   ?: null]);

        return $dataProvider;
    }
}
