<?php

namespace common\modules\master\models;

use Yii;

/**
 * This is the model class for table "status_active".
 *
 * @property int $id
 * @property string $status_active
 *
 * @property Client[] $clients
 * @property Company[] $companies
 */
class StatusActive extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_active';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_active'], 'required'],
            [['status_active'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_active' => 'Status Active',
        ];
    }

    /**
     * Gets query for [[Clients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Client::class, ['status_id' => 'id']);
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['status_id' => 'id']);
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            //$dropdown[0] = 'None';
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->status_active;
            }
        }
        
        return $dropdown;
    }

}
