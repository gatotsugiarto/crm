<?php

namespace backend\modules\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\modules\master\models\QuotationLayout;

/**
 * Master Data -> Layout Quotation: the single SPH template (letterhead, number
 * code, texts) used when quotations are created and printed.
 */
class QuotationlayoutController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        $route = 'backend.' . str_replace('/', '.', $this->getRoute());
                        $parents = strstr($route, strrchr($route, '.'), true) . '.*';
                        return Yii::$app->user->can($route) || Yii::$app->user->can($parents) || Yii::$app->user->can('root');
                    },
                ]],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', ['model' => QuotationLayout::current()]);
    }

    public function actionUpdate()
    {
        $model = QuotationLayout::current();
        if ($model->load(Yii::$app->request->post()) && $model->saveWithLogo()) {
            Yii::$app->session->setFlash('success', 'Quotation layout saved. New quotations use it; existing quotations keep their own texts.');
            return $this->redirect(['index']);
        }
        return $this->render('update', ['model' => $model]);
    }

    /** Serves the logo (stored outside the web root) for the preview. */
    public function actionLogo()
    {
        $model = QuotationLayout::current();
        if (!$model->hasLogo()) {
            throw new NotFoundHttpException('No logo uploaded yet.');
        }
        return Yii::$app->response->sendFile($model->getLogoPath(), $model->logo_file, ['inline' => true]);
    }
}
