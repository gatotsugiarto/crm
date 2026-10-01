<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\QuotationItem;

/**
 * QuotationItemSearch represents the model behind the search form of `common\modules\sales\models\QuotationItem`.
 */
class QuotationItemSearch extends QuotationItem
{
    /** @var string|null status of the item's quotation (Draft, Sent, Approved, Rejected) */
    public $quotationStatus;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quotation_id', 'product_id', 'qty', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['price', 'discount', 'total'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['quotationStatus'], 'in', 'range' => array_keys(Quotation::optsStatus())],
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
        $query = QuotationItem::find()->joinWith('quotation q')->with('product');

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
                // table-qualified: the quotation join has columns with the same names
                'attributes' => array_merge(
                    array_combine(
                        $cols = ['id', 'quotation_id', 'product_id', 'qty', 'price', 'discount', 'total'],
                        array_map(fn($c) => ['asc' => ["quotation_item.$c" => SORT_ASC], 'desc' => ["quotation_item.$c" => SORT_DESC]], $cols)
                    ),
                    ['quotationStatus' => ['asc' => ['q.status' => SORT_ASC], 'desc' => ['q.status' => SORT_DESC]]]
                ),
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
            'quotation_item.id' => $this->id,
            'quotation_item.quotation_id' => $this->quotation_id,
            'quotation_item.product_id' => $this->product_id,
            'quotation_item.qty' => $this->qty,
            'quotation_item.price' => $this->price,
            'quotation_item.discount' => $this->discount,
            'quotation_item.total' => $this->total,
            'quotation_item.status_id' => $this->status_id,
            'quotation_item.created_at' => $this->created_at,
            'quotation_item.created_by' => $this->created_by,
            'quotation_item.updated_at' => $this->updated_at,
            'quotation_item.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['q.status' => $this->quotationStatus]);

        return $dataProvider;
    }
}
