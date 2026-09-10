<?php


namespace backend\modules\productprice\controllers;

use Yii;

use common\modules\productprice\models\ProductBundleItem;
use common\modules\productprice\models\ProductBundleItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ProductbundleitemController implements the CRUD actions for ProductBundleItem model.
 */
class ProductbundleitemController extends Controller
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

    //     $model = new ProductBundleItem();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all ProductBundleItem models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductBundleItemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductBundleItem model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates one or more ProductBundleItem models for the same bundle product in one submit.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new ProductBundleItem(['scenario' => 'insertData']);
        $model = new ProductBundleItem();

        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                $post = Yii::$app->request->post();
                $bundleProductId = trim((string) ($post['ProductBundleItem']['bundle_product_id'] ?? ''));
                $items = is_array($post['items'] ?? null) ? $post['items'] : [];

                if ($bundleProductId === '') {
                    return [
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => ['bundle_product_id' => ['Bundle Product cannot be blank.']],
                    ];
                }

                $errors = [];
                $seenProducts = [];
                $itemModels = [];

                foreach ($items as $i => $item) {
                    $productId = trim((string) ($item['product_id'] ?? ''));
                    $qty = trim((string) ($item['quantity'] ?? ''));
                    $rowLabel = 'Row ' . ($i + 1) . ': ';

                    if ($productId === '' && $qty === '') {
                        continue;
                    }
                    if ($productId === '') {
                        $errors[] = $rowLabel . 'Product is required.';
                        continue;
                    }
                    if ($qty === '' || !is_numeric($qty) || (int) $qty < 1) {
                        $errors[] = $rowLabel . 'Quantity must be at least 1.';
                        continue;
                    }
                    if (isset($seenProducts[$productId])) {
                        $errors[] = $rowLabel . 'This product is already added in this bundle.';
                        continue;
                    }
                    $seenProducts[$productId] = true;

                    $itemModel = new ProductBundleItem();
                    $itemModel->bundle_product_id = $bundleProductId;
                    $itemModel->product_id = $productId;
                    $itemModel->quantity = (int) $qty;

                    if (!$itemModel->validate()) {
                        foreach (array_values($itemModel->getFirstErrors()) as $msg) {
                            $errors[] = $rowLabel . $msg;
                        }
                        continue;
                    }

                    $itemModels[] = $itemModel;
                }

                if (empty($itemModels) && empty($errors)) {
                    $errors[] = 'Add at least one product to the bundle.';
                }

                if (!empty($errors)) {
                    return [
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => ['bundle_product_id' => $errors],
                    ];
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($itemModels as $itemModel) {
                        if (!$itemModel->save(false)) {
                            throw new \yii\db\Exception('Failed to save product bundle item.');
                        }
                    }
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Failed to save.',
                        'errors' => ['bundle_product_id' => ['Failed to save one or more items, please try again.']],
                    ];
                }

                $model->getBehavior('tokenProtection')->consumeToken();

                return [
                    'success' => true,
                    'message' => count($itemModels) . ' product bundle item(s) created successfully.',
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
                Yii::$app->session->setFlash('success', 'Product bundle created successfully.');
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
     * Updates an existing ProductBundleItem model.
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
                        'message' => 'Product bundle updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Product bundle updated successfully.');
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
     * Deletes an existing ProductBundleItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Product bundle deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Product bundle deleted successfully.');
        return $this->redirect(['index']);
    }

    /*
    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update ProductBundleItem        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'ProductBundleItem activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'ProductBundleItem activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update ProductBundleItem        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'ProductBundleItem non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'ProductBundleItem non activate successfully.');
        return $this->redirect(['index']);
    }
    */

    /**
     * Finds the ProductBundleItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProductBundleItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductBundleItem::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
