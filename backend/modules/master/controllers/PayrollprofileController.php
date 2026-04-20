<?php

namespace backend\modules\master\controllers;

use Yii;

use common\modules\master\models\PayrollProfile;
use common\modules\master\models\PayrollProfileSearch;

use common\modules\master\models\PayrollProfileComponent;
use common\modules\master\models\EmployeePayrollProfile;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PayrollprofileController implements the CRUD actions for PayrollProfile model.
 */
class PayrollprofileController extends Controller
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
     * Lists all PayrollProfile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PayrollProfileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $item_id = array();
        $PayrollProfileComponent = PayrollProfileComponent::find()->where(['profile_id' => $id])->all();
        foreach($PayrollProfileComponent as $rows){
            $item_id[] = $rows->item->name.' ['.$rows->item->code.']';
        }

        $employee_id = array();
        $EmployeePayrollProfile = EmployeePayrollProfile::find()->where(['profile_id' => $id])->all();
        foreach($EmployeePayrollProfile as $rows){
            $employee_id[] = $rows->employee->fullname.' ['.$rows->employee->e_number.']';
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
                'item_id' => $item_id,
                'employee_id' => $employee_id
            ]);
        }

        return $this->render('view', [
            'model' => $model,
            'item_id' => $item_id,
            'employee_id' => $employee_id
        ]);
    }

    public function actionCreate()
    {
        $model = new PayrollProfile();

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

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();
                    // $model->saveComponents(); 
                    // $model->saveEmployees(); 

                    return [ 
                        'success' => true, 
                        'message' => 'Payroll profile created successfully, please component & employee assignment', 
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => ActiveForm::validate($model),
                ];
            }

            $item_id = array();
            $employee_id = array();
            $formToken = $model->getBehavior('tokenProtection')->generateToken();
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
                'item_id' => $item_id,
                'employee_id' => $employee_id,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // if ($model->save()) {
            //     $model->getBehavior('tokenProtection')->consumeToken();
            //     Yii::$app->session->setFlash('success', 'Payroll profile created successfully.');
            //     return $this->redirect(['index']);
            // }

            if ($model->validate() && $model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                // $model->saveComponents(); 
                // $model->saveEmployees(); 

                Yii::$app->session->setFlash('success', 'Payroll profile created successfully, please component & employee assignment');
                return $this->redirect(['index']);
            }
        }

        $item_id = array();
        $employee_id = array();
        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
            'item_id' => $item_id,
            'employee_id' => $employee_id,
        ]);
    }

    /**
     * Updates an existing PayrollProfile model.
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

                if ($model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();
                    $model->saveComponents(); 
                    $model->saveEmployees(); 

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
            
            $item_id = array();
            $PayrollProfileComponent = PayrollProfileComponent::find()->where(['profile_id' => $id])->asarray()->all();
            foreach($PayrollProfileComponent as $rows){
                $item_id[] = $rows['item_id'];
            }

            $employee_id = array();
            $EmployeePayrollProfile = EmployeePayrollProfile::find()->where(['profile_id' => $id])->asarray()->all();
            foreach($EmployeePayrollProfile as $rows){
                $employee_id[] = $rows['employee_id'];
            }
            
            return $this->renderAjax('_form', [
                'model'     => $model,
                'item_id' => $item_id,
                'employee_id' => $employee_id,
                'formToken' => $formToken,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // if ($model->save()) {
            //     $model->getBehavior('tokenProtection')->consumeToken();
            //     Yii::$app->session->setFlash('success', 'Payroll profile updated successfully.');
            //     return $this->redirect(['index']);
            // }

            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                $model->saveComponents(); 
                $model->saveEmployees(); 
                
                Yii::$app->session->setFlash('success', 'Payroll profile updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        
        $item_id = array();
        $PayrollProfileComponent = PayrollProfileComponent::find()->where(['profile_id' => $id])->asarray()->all();
        foreach($PayrollProfileComponent as $rows){
            $item_id[] = $rows['item_id'];
        }

        $employee_id = array();
        $EmployeePayrollProfile = EmployeePayrollProfile::find()->where(['profile_id' => $id])->asarray()->all();
        foreach($EmployeePayrollProfile as $rows){
            $employee_id[] = $rows['employee_id'];
        }

        return $this->render('_form', [
            'model'     => $model,
            'item_id' => $item_id,
            'employee_id' => $employee_id,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Deletes an existing PayrollProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        PayrollProfileComponent::deleteAll([
            'profile_id' => $id
        ]);
        $model->delete();
        

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Payroll profile deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Payroll profile deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update
        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Payroll profile activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Payroll profile activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update 
        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Payroll profile non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Payroll profile non activate successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the PayrollProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PayrollProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PayrollProfile::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
