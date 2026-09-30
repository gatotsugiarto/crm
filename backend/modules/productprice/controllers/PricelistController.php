<?php


namespace backend\modules\productprice\controllers;

use Yii;

use common\modules\productprice\models\PriceList;
use common\modules\productprice\models\PriceListSearch;
use common\modules\productprice\models\ProductPriceSearch;
use common\modules\productprice\models\ProductDiscountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PricelistController implements the CRUD actions for PriceList model.
 */
class PricelistController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        $route = 'backend.'.str_replace('/','.',$this->getRoute());
                        $parents = strstr($route, strrchr ($route,'.'),true).'.*';
                        if (\Yii::$app->user->can($route) || \Yii::$app->user->can($parents) || \Yii::$app->user->can("root")){
                            return true;
                        }
                    }
                ],
            ],
        ];
        
        return $behaviors;
    }

    // public function actionValidate()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $model = new PriceList();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all PriceList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PriceListSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PriceList model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new ProductPriceSearch();
        $searchModel->price_list_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $discountSearchModel = new ProductDiscountSearch();
        $discountSearchModel->price_list_id = $id;
        $discountProvider = $discountSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model'       => $model,
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            'discountSearchModel'=> $discountSearchModel,
            'discountProvider'=> $discountProvider,
        ]);
    }

    /**
     * Creates a new PriceList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new PriceList(['scenario' => 'insertData']);
        $model = new PriceList();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $model->save();
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Price list created successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => ActiveForm::validate($model),
                ];
            }

            $formToken = $model->getBehavior('tokenProtection')->generateToken();
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Price list created successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Updates an existing PriceList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // $model->scenario = 'updateData';

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Price list updated successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $model->getErrors(),
                ];
            }

            $formToken = $model->getBehavior('tokenProtection')->generateToken(); // GET pertama kali buka modal → generate token baru
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Price list updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Deletes an existing PriceList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $blocker = $model->deleteBlockers();
        if ($blocker !== null) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => $blocker];
            }
            Yii::$app->session->setFlash('error', $blocker);
            return $this->redirect(['index']);
        }

        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Price list deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Price list deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Retire a price list without losing its prices: it stays in the data but no
     * longer appears in the Account / Product Price / Discount forms.
     */
    public function actionNonactive($id)
    {
        return $this->setStatus($id, 2, 'deactivated');
    }

    public function actionReactive($id)
    {
        return $this->setStatus($id, 1, 'activated');
    }

    private function setStatus($id, $statusId, $verb)
    {
        $model = $this->findModel($id);
        $model->detachBehavior('tokenProtection');
        $model->status_id = $statusId;
        $ok = $model->save(false);
        $message = $ok ? "Price list \"{$model->name}\" {$verb}." : 'Price list could not be updated.';

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $ok, 'message' => $message];
        }
        Yii::$app->session->setFlash($ok ? 'success' : 'error', $message);
        return $this->redirect(['index']);
    }

    /**
     * Finds the PriceList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PriceList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PriceList::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
