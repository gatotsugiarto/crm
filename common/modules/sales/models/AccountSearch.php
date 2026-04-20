<?php

namespace common\modules\sales\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sales\models\Account;

/**
 * AccountSearch represents the model behind the search form of `common\modules\sales\models\Account`.
 */
class AccountSearch extends Account
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_account_id', 'price_list_id', 'status_id', 'created_by', 'updated_by', 'owner_user_id'], 'integer'],
            [['code', 'name', 'account_type', 'industry', 'tax_number', 'phone', 'email', 'website', 'address', 'city', 'province', 'country', 'created_at', 'updated_at'], 'safe'],
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
        $query = Account::find();

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
            'parent_account_id' => $this->parent_account_id,
            'price_list_id' => $this->price_list_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'city_id' => $this->city_id,
            'province_id' => $this->province_id,
            'country_id' => $this->country_id,
            'postal_code_id' => $this->postal_code_id,
            'owner_user_id' => $this->owner_user_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'account_type', $this->account_type])
            ->andFilterWhere(['like', 'industry', $this->industry])
            ->andFilterWhere(['like', 'tax_number', $this->tax_number])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
