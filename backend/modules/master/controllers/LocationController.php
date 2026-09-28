<?php

namespace backend\modules\master\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use common\modules\master\models\City;
use common\modules\master\models\PostalCode;
use common\modules\master\models\Province;

/**
 * JSON lookups for the dependent location dropdowns (country -> province -> city
 * -> postal code) on the lead, account and account address forms. Read-only
 * reference data, so any logged-in user may call it.
 */
class LocationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    public function beforeAction($action)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /** @return array [{id, text}] */
    public function actionProvinces($id = null)
    {
        return $this->options(Province::dropdownFor($id));
    }

    public function actionCities($id = null)
    {
        return $this->options(City::dropdownFor($id));
    }

    public function actionPostalcodes($id = null)
    {
        return $this->options(PostalCode::dropdownFor($id));
    }

    private function options(array $list)
    {
        $out = [];
        foreach ($list as $value => $text) {
            $out[] = ['id' => $value, 'text' => $text];
        }
        return $out;
    }
}
