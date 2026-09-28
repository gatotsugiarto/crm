<?php

namespace common\components;

use common\modules\master\models\City;
use common\modules\master\models\PostalCode;
use common\modules\master\models\Province;

/**
 * Keeps country / province / city / postal code consistent on a record that has
 * all four (lead, account, account_address): each level must belong to the one
 * above it. The forms already filter the lists (dependent dropdowns); this is the
 * server-side check.
 */
class LocationRules
{
    /**
     * Inline-validator body; call it for one attribute of the model.
     * @param \yii\base\Model $model
     */
    public static function check($model)
    {
        if ($model->province_id && $model->country_id) {
            $province = Province::findOne($model->province_id);
            if ($province && (int) $province->country_id !== (int) $model->country_id) {
                $model->addError('province_id', 'This province is not in the selected country.');
            }
        }
        if ($model->city_id && $model->province_id) {
            $city = City::findOne($model->city_id);
            if ($city && (int) $city->province_id !== (int) $model->province_id) {
                $model->addError('city_id', 'This city is not in the selected province.');
            }
        }
        if ($model->postal_code_id && $model->city_id) {
            $postal = PostalCode::findOne($model->postal_code_id);
            if ($postal && (int) $postal->city_id !== (int) $model->city_id) {
                $model->addError('postal_code_id', 'This postal code is not in the selected city.');
            }
        }
    }
}
