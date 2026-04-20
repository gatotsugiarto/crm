<?php

namespace backend\modules\master\controllers;

use Yii;

use common\modules\master\models\EmployeePayrollProfile;
use common\modules\master\models\EmployeePayrollProfileSearch;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * EmployeepayrollprofileController implements the CRUD actions for EmployeePayrollProfile model.
 */
class EmployeepayrollprofileController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all EmployeePayrollProfile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EmployeePayrollProfileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new EmployeePayrollProfile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EmployeePayrollProfile();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                // if ($model->validate()) {
                //     $model->save();
                //     $model->getBehavior('tokenProtection')->consumeToken();

                //     return [
                //         'success' => true,
                //         'message' => 'Payroll profile created successfully.',
                //     ];
                // }

                $employeeIds = $model->employee_id;
                $payrollProfileId = $model->profile_id;
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {

                    // 1️⃣ Hapus data lama
                    EmployeePayrollProfile::deleteAll([
                        'employee_id' => $employeeIds
                    ]);

                    // 2️⃣ Siapkan data batch insert
                    $rows = [];
                    foreach ($employeeIds as $employeeId) {
                        $rows[] = [
                            $employeeId,
                            $payrollProfileId,
                            1,
                            date('Y-m-d H:i:s'),
                            Yii::$app->user->id,
                            date('Y-m-d H:i:s'),
                            Yii::$app->user->id
                        ];
                    }

                    // 3️⃣ Batch insert
                    $db->createCommand()->batchInsert(
                        EmployeePayrollProfile::tableName(),
                        ['employee_id', 'profile_id', 'status_id', 'created_at', 'created_by', 'updated_at', 'updated_by'],
                        $rows
                    )->execute();

                    $transaction->commit();

                    return [
                        'success' => true,
                        'message' => 'Payroll profile created successfully.',
                    ];

                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
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
            
            $employeeIds = $model->employee_id;
            $payrollProfileId = $model->profile_id;

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();

            try {

                // 1️⃣ Hapus data lama
                EmployeePayrollProfile::deleteAll([
                    'employee_id' => $employeeIds
                ]);

                // 2️⃣ Siapkan data batch insert
                $rows = [];
                foreach ($employeeIds as $employeeId) {
                    $rows[] = [
                        $employeeId,
                        $payrollProfileId,
                        1,
                        date('Y-m-d H:i:s'),
                        Yii::$app->user->id,
                        date('Y-m-d H:i:s'),
                        Yii::$app->user->id
                    ];
                }

                // 3️⃣ Batch insert
                $db->createCommand()->batchInsert(
                    EmployeePayrollProfile::tableName(),
                    ['employee_id', 'profile_id', 'status_id', 'created_at', 'created_by', 'updated_at', 'updated_by'],
                    $rows
                )->execute();

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Payroll profile created successfully.');
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
                Yii::$app->session->setFlash('danger', $e);
            }

            $model->getBehavior('tokenProtection')->consumeToken();
            return $this->redirect(['index']);

            // if ($model->save()) {
            //     $model->getBehavior('tokenProtection')->consumeToken();
            //     Yii::$app->session->setFlash('success', 'Payroll profile created successfully.');
            //     return $this->redirect(['index']);
            // }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Updates an existing EmployeePayrollProfile model.
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
                        'message' => 'Payroll profile updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Payroll profile updated successfully.');
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
     * Deletes an existing EmployeePayrollProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmployeePayrollProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EmployeePayrollProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeePayrollProfile::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
