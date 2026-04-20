<?php

namespace common\modules\master\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\master\models\PayrollItem;

/**
 * PayrollItemSearch represents the model behind the search form of `common\modules\master\models\PayrollItem`.
 */
class PayrollItemSearch extends PayrollItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'default_value', 'taxable', 'display_order', 'status_id'], 'integer'],
            [['code', 'name', 'type', 'sign', 'salary_type', 'created_at', 'updated_at'], 'safe'],
            [['percent', 'cap'], 'number'],
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
        $query = PayrollItem::find();

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
            'category_id' => $this->category_id,
            'default_value' => $this->default_value,
            'taxable' => $this->taxable,
            'display_order' => $this->display_order,
            'percent' => $this->percent,
            'cap' => $this->cap,
            'payroll_item.status_id' => $this->status_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'sign', $this->sign])
            ->andFilterWhere(['like', 'item_code', $this->item_code])
            ->andFilterWhere(['like', 'base_multiplier', $this->base_multiplier])
            ->andFilterWhere(['like', 'salary_type', $this->salary_type]);

        return $dataProvider;
    }
}
