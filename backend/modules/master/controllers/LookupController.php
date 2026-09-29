<?php

namespace backend\modules\master\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use common\modules\master\models\Team;

/**
 * JSON lookups for dependent dropdowns outside the location chain. Read-only,
 * so any logged-in user may call it (like LocationController).
 */
class LookupController extends Controller
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

    /**
     * Members of a Sales Team, for Assigned Sales on the account form.
     * @return array [{id, text}]
     */
    public function actionTeamMembers($id = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];
        foreach (Team::membersDropdown($id) as $value => $text) {
            $out[] = ['id' => $value, 'text' => $text];
        }
        return $out;
    }
}
