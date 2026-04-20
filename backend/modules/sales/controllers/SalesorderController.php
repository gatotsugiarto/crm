<?php


namespace backend\modules\sales\controllers;

use Yii;

use common\modules\sales\models\SalesOrder;
use common\modules\sales\models\SalesOrderSearch;
use common\modules\sales\models\SalesOrderItemSearch;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * SalesorderController implements the CRUD actions for SalesOrder model.
 */
class SalesorderController extends Controller
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

    //     $model = new SalesOrder();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all SalesOrder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SalesOrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesOrder model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new SalesOrderItemSearch();
        $searchModel->sales_order_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model'       => $model,
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            // 'discountSearchModel'=> $discountSearchModel,
            // 'discountProvider'=> $discountProvider,
        ]);
    }

    public function actionConfirm($id)
    {
        Yii::$app->session->removeAllFlashes();
        $model = $this->findModel($id);

        if ($model->status === 'Confirmed') {
            Yii::$app->session->setFlash('warning', 'Already confirmed');
            return $this->redirect(['view', 'id' => $id]);
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // 🔒 Lock SO row + cek invoice — pakai raw SQL, bypass semua ORM scope
            $db->createCommand('SELECT id FROM {{%sales_order}} WHERE id = :id FOR UPDATE')
                ->bindValue(':id', $id)->queryOne();

            $existingInvoiceId = $db->createCommand(
                'SELECT id FROM {{%invoice}} WHERE sales_order_id = :so_id LIMIT 1'
            )->bindValue(':so_id', $id)->queryScalar();

            if ($existingInvoiceId) {
                $transaction->commit();
                Yii::$app->session->setFlash('warning', 'Invoice sudah ada untuk SO ini.');
                return $this->redirect(['/sales/invoice/view', 'id' => $existingInvoiceId]);
            }

            // Update status SO
            $db->createCommand('UPDATE {{%sales_order}} SET status = :s WHERE id = :id')
                ->bindValues([':s' => 'Confirmed', ':id' => $id])
                ->execute();

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Insert invoice — raw SQL, tidak ada ORM scope
            $db->createCommand()->insert('{{%invoice}}', [
                'sales_order_id' => $model->id,
                'account_id'     => $model->account_id,
                'invoice_date'   => date('Y-m-d'),
                'due_date'       => date('Y-m-d', strtotime('+30 days')),
                'total_amount'   => $model->total_amount,
                'status'         => 'Draft',
                'status_id'      => 1,
                'invoice_number' => $invoiceNumber,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
                'created_by'     => Yii::$app->user->id,
                'updated_by'     => Yii::$app->user->id,
            ])->execute();

            $invoiceId = $db->getLastInsertID();

            // Copy items
            foreach ($model->salesOrderItems as $item) {
                $db->createCommand()->insert('{{%invoice_item}}', [
                    'invoice_id' => $invoiceId,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'price'      => $item->price,
                    'discount'   => $item->discount,
                    'total'      => $item->total,
                    'status_id'  => 1,
                ])->execute();
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'SO confirmed & Invoice created');
            return $this->redirect(['/sales/invoice/view', 'id' => $invoiceId]);

        } catch (\Throwable $e) {
            $transaction->rollBack();

            // Last resort — kalau duplicate, cari invoicenya langsung
            if (str_contains($e->getMessage(), '1062')) {
                $existingInvoiceId = $db->createCommand(
                    'SELECT id FROM {{%invoice}} WHERE sales_order_id = :id LIMIT 1'
                )->bindValue(':id', $id)->queryScalar();

                if ($existingInvoiceId) {
                    Yii::$app->session->setFlash('warning', 'Invoice sudah ada.');
                    return $this->redirect(['/sales/invoice/view', 'id' => $existingInvoiceId]);
                }
            }

            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $id]);
        }
    }

    protected function generateInvoiceNumber()
    {
        return Yii::$app->db->createCommand("
            SELECT CONCAT(
                'INV/',
                DATE_FORMAT(NOW(), '%Y%m%d'),
                '/',
                LPAD(
                    IFNULL(MAX(CAST(SUBSTRING_INDEX(invoice_number, '/', -1) AS UNSIGNED)), 0) + 1,
                    4,
                    '0'
                )
            )
            FROM invoice
            WHERE DATE(created_at) = CURDATE()
        ")->queryScalar();
    }

    /**
     * Creates a new SalesOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new SalesOrder(['scenario' => 'insertData']);
        $model = new SalesOrder();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $model->save();
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'SalesOrder created successfully.',
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
                Yii::$app->session->setFlash('success', 'SalesOrder created successfully.');
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
     * Updates an existing SalesOrder model.
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
                        'message' => 'SalesOrder updated successfully.',
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
                Yii::$app->session->setFlash('success', 'SalesOrder updated successfully.');
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
     * Deletes an existing SalesOrder model.
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
                'message' => 'SalesOrder deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'SalesOrder deleted successfully.');
        return $this->redirect(['index']);
    }

    /*
    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update SalesOrder        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'SalesOrder activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'SalesOrder activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update SalesOrder        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'SalesOrder non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'SalesOrder non activate successfully.');
        return $this->redirect(['index']);
    }
    */

    /**
     * Finds the SalesOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return SalesOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesOrder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
