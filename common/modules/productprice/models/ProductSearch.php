<?php

namespace common\modules\productprice\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\productprice\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\modules\productprice\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'uom_id', 'is_bundle_expand', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['code', 'name', 'type', 'bundle_price_type', 'description', 'created_at', 'updated_at'], 'safe'],
            [['base_price'], 'number'],
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
        $query = Product::find();

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
            'category_id' => $this->category_id,
            'uom_id' => $this->uom_id,
            'base_price' => $this->base_price,
            'is_bundle_expand' => $this->is_bundle_expand,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'bundle_price_type', $this->bundle_price_type])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
