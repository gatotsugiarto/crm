<?php

namespace backend\modules\master\controllers;

use Yii;
use common\modules\master\models\Employee;
use common\modules\master\models\EmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\payroll\models\Payroll;
use common\modules\payroll\models\ReportUpload;
use common\modules\payroll\models\EmployeeUpload;
use common\modules\payroll\models\EmployeeHistory;
use common\modules\master\models\Salary;
use common\modules\master\models\EmployeeStatusTransaction;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
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
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param integer $id
     * @return mixed
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
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if($model->status_id == 2){
                $model->close_salary(2);
            }else{
                $model->close_salary(1);
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionRegister($id)
    {
        $user_id    = Yii::$app->user->identity->id;

        // ambil data upload
        $upload = EmployeeUpload::findOne($id);
        if (!$upload) {
            Yii::$app->session->setFlash('danger', 'Uploaded employee data was not found.');
            return $this->redirect(['/payroll/payroll/joinresign']);
        }

        // cek apakah sudah pernah diregister
        $employeeExist = Employee::find()->where(['id' => $upload->id])->one();
        if (!$employeeExist) {

            $sql = "INSERT INTO employee SELECT id, region_id, region, company_id, company, branch_id, branch, site_office_id, site_office, department_id, department, division_id, division, e_number, UPPER(fullname), join_date, marital_status_id, marital_status, family_status_id, family_status, ptkp_id, ptkp, level_jabatan_id, level_jabatan, jabatan_id, jabatan, grade_id, grade, email, is_npwp, npwp_id, bpjs_tk, bpjs_kes, jkk_id, jkk, bank_id, bank, bank_no, employee_status_id, employee_status, join_date_prorate,resign_prorate,resign_date, 1 AS cost_center_id, UPPER(address), 1, NOW(), $user_id, NOW(), $user_id FROM employee_upload WHERE id = $id";
            $affectedRows =\Yii::$app->db->createCommand($sql)->execute();
            if ($affectedRows > 0) {

                $employee_id = Yii::$app->db->getLastInsertID();
                $fullname = $upload->fullname;
                $jabatan = $upload->jabatan;
                
                $sql2 = "UPDATE employee_join_resign SET status_id = 2 WHERE id = $id";
                \Yii::$app->db->createCommand($sql2)->execute();

                $tetap = Employee::tetap();
                $pkwt = Employee::pkwt();
                $karyawan = Employee::total();
                ReportUpload::updateAll(['tetap' => $tetap, 'pkwt' => $pkwt, 'karyawan' => $karyawan], ['id' => 2]);

                $no_salary = Employee::no_salary();
                ReportUpload::updateAll(['no_salary' => $no_salary]);
                        
                EmployeeUpload::adjust_salary_from_upload($id);

                $employeepayrollprofile = Employee::employeepayrollprofile($employee_id);

                $LoggableBehavior = new \common\components\behaviors\LoggableBehavior();
                $LoggableBehavior->manualLog('create', 'Register employee', $employee_id, $employee_id);

                $link_salary  = \Yii::$app->request->BaseUrl.'/master/salary/index';
                $message = "Employee registered successfully. Payroll profile: $employeepayrollprofile. Please manage the salary details.";

                Yii::$app->session->setFlash('danger', $message);
                return $this->redirect(['/payroll/payroll/joinresign']);
            }else{
                Yii::$app->session->setFlash('danger', 'Failed or no data changes were made.');
                return $this->redirect(['/payroll/payroll/joinresign']);
            }
        }else{

            // $sql2 = "UPDATE employee_join_resign SET status_id = 2 WHERE id = $id";
            // \Yii::$app->db->createCommand($sql2)->execute();

            Yii::$app->session->setFlash('danger', 'The new employee data has already been successfully added previously.');
            return $this->redirect(['/payroll/payroll/joinresign']);
        }
    }

    public function actionResignemployee($id)
    {
        $user_id    = Yii::$app->user->identity->id;
        
        $model = Employee::findOne($id);
        if(!$model){

            $message = "Failed or no data changes were made.";
            Yii::$app->session->setFlash('danger', $message);
            return $this->redirect(['/payroll/payroll/datauploaded']);
        }else{

            $sql = "UPDATE employee SET status_id = 2 WHERE id = $id";
            $affectedRows =\Yii::$app->db->createCommand($sql)->execute();

            if ($affectedRows > 0) {
                $Employee = Employee::findOne($id);
                $fullname = $Employee->fullname;
                $jabatan = $Employee->jabatan;
                $message = "New employee $fullname - $jabatan has been <b>non actived</b>.";
                
                $sql2 = "UPDATE employee_uploaded_detail SET status_id = 2 WHERE id = $id";
                \Yii::$app->db->createCommand($sql2)->execute();

                $resign = Payroll::resign();
                Payroll::no_salary();
                Payroll::tetap();
                Payroll::pkwt();
                Payroll::total();
                EmployeeUploaded::updateAll(['resign' => $resign+1], ['id' => 2]);
                // EmployeeUploaded::updateAll(['no_salary' => $no_salary]);

                Salary::updateAll(['status_id' => 2], ['employee_id' => $id]);
                $message = "Employee salary ".strtoupper($fullname)." has been deactivated";
                EmployeeHistory::create($referral_code, "Gaji Karyawan", $message);

                Yii::$app->session->setFlash('success', $message);
                return $this->redirect(['/master/employee/index']);
            } else {
                $message = "The employee data has already been <b>deactivated</b> previously.";
                Yii::$app->session->setFlash('danger', $message);
                return $this->redirect(['/payroll/payroll/datauploaded']);
            }

            $sql2 = "UPDATE employee_uploaded_detail SET status_id = 2 WHERE id = $id";
            \Yii::$app->db->createCommand($sql2)->execute();

            Yii::$app->session->setFlash('danger', $message);
            return $this->redirect(['/payroll/payroll/datauploaded']);
        }
    }

    public function actionActiveemployee($id)
    {
        $user_id    = Yii::$app->user->identity->id;
        
        $model = Employee::findOne($id);
        if(!$model){

            $message = "Failed or no data changes were made.";
            Yii::$app->session->setFlash('danger', $message);
            return $this->redirect(['/payroll/payroll/datauploaded']);
        }else{

            $sql = "UPDATE employee SET status_id = 1 WHERE id = $id";
            $affectedRows =\Yii::$app->db->createCommand($sql)->execute();

            if ($affectedRows > 0) {
                $Employee = Employee::findOne($id);
                $fullname = $Employee->fullname;
                $jabatan = $Employee->jabatan;
                $message = "New employee $fullname - $jabatan has been <b>actived</b>, <a href='$link_salary'><b>Click Here</b></a> to manage salaries.";
                
                $sql2 = "UPDATE employee_uploaded_detail SET status_id = 2 WHERE id = $id";
                \Yii::$app->db->createCommand($sql2)->execute();

                Yii::$app->session->setFlash('success', $message);
                return $this->redirect(['/master/employee/index']);
            } else {
                $message = "The employee data has already been successfully <b>Non Active</b> previously.";
                Yii::$app->session->setFlash('danger', $message);
                return $this->redirect(['/payroll/payroll/datauploaded']);
            }

            $sql2 = "UPDATE employee_uploaded_detail SET status_id = 2 WHERE id = $id";
            \Yii::$app->db->createCommand($sql2)->execute();

            Yii::$app->session->setFlash('danger', $message);
            return $this->redirect(['/payroll/payroll/datauploaded']);
        }
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        $before_status_id = 1;
        $after_status_id = 2;
        $module_id = 1;
        $module = 'Non active employee and salary';
        $user_id    = Yii::$app->user->identity->id;

        // Log EmployeeStatusTransaction - Start
        $sql = "INSERT employee_status_transaction SELECT 0, a.employee_id, a.id, a.status_id, $after_status_id, $module_id, '$module', 1, NOW(), $user_id, NOW(), $user_id FROM salary a INNER JOIN employee b ON a.employee_id = b.id INNER JOIN payroll_item c ON a.payroll_item_id = c.id AND c.type = 'DATA' WHERE a.employee_id = $id AND a.status_id = $before_status_id";
        \Yii::$app->db->createCommand($sql)->execute();

        // Update Employee
        $model->status_id = $after_status_id;
        $model->save();

        // Update salary
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $salaryIds = EmployeeStatusTransaction::find()
                ->select('other_id')
                ->where([
                    'status_id'   => $before_status_id,
                    'employee_id' => $id
                ])
                ->column();

            Salary::updateSalary($after_status_id, $salaryIds);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $sql = "UPDATE salary a INNER JOIN employee_status_transaction b ON a.id = b.other_id AND a.status_id = $before_status_id AND b.status_id = 1 SET a.status_id = $after_status_id, a.updated_by = $user_id, a.updated_at = NOW() WHERE a.employee_id = $id";
        \Yii::$app->db->createCommand($sql)->execute();

        // Log EmployeeStatusTransaction - Finish
        $sql = "UPDATE employee_status_transaction a INNER JOIN salary b ON a.other_id = b.id AND a.status_id = 1 SET a.status_id = 2 WHERE a.employee_id = $id";
        \Yii::$app->db->createCommand($sql)->execute();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Employee non active successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Employee non active successfully.');
        return $this->redirect(['index']);
    }


    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        $before_status_id = 2;
        $after_status_id = 1;
        $module_id = 1;
        $module = 'Re-active employee and salary';
        $user_id    = Yii::$app->user->identity->id;

        // Log EmployeeStatusTransaction - Start
        $sql = "INSERT employee_status_transaction SELECT 0, a.employee_id, a.id, a.status_id, $after_status_id, $module_id, '$module', 1, NOW(), $user_id, NOW(), $user_id FROM salary a INNER JOIN employee b ON a.employee_id = b.id INNER JOIN payroll_item c ON a.payroll_item_id = c.id AND c.type = 'DATA' WHERE a.employee_id = $id AND a.status_id = $before_status_id";
        \Yii::$app->db->createCommand($sql)->execute();

        // Update Employee
        $model->status_id = $after_status_id;
        $model->save();

        // Update salary
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $salaryIds = EmployeeStatusTransaction::find()
                ->select('other_id')
                ->where([
                    'status_id'   => $before_status_id,
                    'employee_id' => $id
                ])
                ->column();

            Salary::updateSalary($after_status_id, $salaryIds);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $sql = "UPDATE salary a INNER JOIN employee_status_transaction b ON a.id = b.other_id AND a.status_id = $before_status_id AND b.status_id = 1 SET a.status_id = $after_status_id, a.updated_by = $user_id, a.updated_at = NOW() WHERE a.employee_id = $id";
        \Yii::$app->db->createCommand($sql)->execute();

        // Log EmployeeStatusTransaction - Finish
        $sql = "UPDATE employee_status_transaction a INNER JOIN salary b ON a.other_id = b.id AND a.status_id = 1 SET a.status_id = 2 WHERE a.employee_id = $id";
        \Yii::$app->db->createCommand($sql)->execute();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Employee activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Employee activate successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
