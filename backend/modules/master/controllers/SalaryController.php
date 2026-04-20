<?php

namespace backend\modules\master\controllers;

use Yii;

use common\modules\master\models\Salary;
use common\modules\master\models\SalarySearch;
use common\modules\master\models\PayrollItem;
use common\modules\master\models\UploadForm;
use common\modules\master\models\Employee;
use common\modules\payroll\models\EmployeeHistory;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;

/**
 * SalaryController implements the CRUD actions for Salary model.
 */
class SalaryController extends Controller
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

    // public function actionValidate()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $model = new Salary();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    public function actionIndex()
    {
        $searchModel = new SalarySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // 1️⃣ Ambil payroll item
        $payrollItems = PayrollItem::find()
            ->where([
                'status_id' => 1,
                'type' => 'DATA'
            ])
            ->andWhere(['in', 'category_id', [1,2]])
            ->orderBy('display_order')
            ->all();

        // 2️⃣ Build dynamic select
        $selectColumns = [
            'e.id AS employee_id',
            'e.e_number',
            'e.fullname'
        ];

        foreach ($payrollItems as $item) {
            $code = $item->code;

            $selectColumns[] = new \yii\db\Expression("
                COALESCE(MAX(CASE WHEN pi.code = '{$code}' THEN s.amount END),0) AS `{$code}`
            ");
        }

        // 3️⃣ Query pivot
        $data = (new \yii\db\Query())
            ->select($selectColumns)
            ->from(['e' => 'employee'])
            ->leftJoin(['s' => 'salary'], 's.employee_id = e.id')
            ->leftJoin(['pi' => 'payroll_item'], '
                pi.id = s.payroll_item_id
                AND pi.status_id = 1
                AND pi.category_id IN (1,2)
                AND pi.type = "DATA"
            ')
            ->where(['e.status_id' => 1])
            ->groupBy(['e.id', 'e.fullname'])
            ->orderBy(['e.id' => SORT_ASC])
            ->all();

        // 4️⃣ ArrayDataProvider
        $dataProviderInput = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);

        // 5️⃣ Build gridColumnsInput dynamic
        $gridColumnsInput = [
            [
                'attribute' => 'employee_id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'e_number',
                'label' => 'NIP',
            ],
            [
                'attribute' => 'fullname',
                'label' => 'Employee',
            ],
        ];

        foreach ($payrollItems as $item) {
            $gridColumnsInput[] = [
                'attribute' => $item->code,
                'label' => $item->code,
                // 'format' => ['decimal', 0],
                'value' => function ($model) use ($item) {
                    return (float) ($model[$item->code] ?? 0);
                },
                'format' => 'raw',
                'exportMenuStyle' => [
                    'format' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
                ],
                'contentOptions' => ['style' => 'text-align:right;'],
            ];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'dataProviderInput' => $dataProviderInput,
            'gridColumnsInput' => $gridColumnsInput,
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

    public function actionCreate()
    {
        $model = new Salary(['scenario' => 'insertData']);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {

                    $PayrollItem = PayrollItem::findOne($model->payroll_item_id);
                    if($PayrollItem){
                        $model->extraRemarks = 'Create salary '.$PayrollItem->name;
                    }else{
                        $model->extraRemarks = 'Create salary';
                    }
                    $model->extraEmployee = $model->employee_id;

                    $model->save();
                    // $fullname = Employee::findOne($employeeId)?->fullname ?? '-';
                    // EmployeeHistory::create('SAL'.date('Ymdhis'), "Create Salary", "Salary for $fullname has been successfully inserted.");
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Salary created successfully.',
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
            $PayrollItem = PayrollItem::findOne($model->payroll_item_id);
            if($PayrollItem){
                $model->extraRemarks = 'Create salary '.$PayrollItem->name;
            }else{
                $model->extraRemarks = 'Create salary';
            }
            $model->extraEmployee = $model->employee_id;
            if ($model->save()) {
                $fullname = Employee::findOne($employeeId)?->fullname ?? '-';
                EmployeeHistory::create('SAL'.date('Ymdhis'), "Create Salary", "Salary for $fullname has been successfully inserted.");
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Salary created successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // $model->scenario = 'updateData';

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {

                    $PayrollItem = PayrollItem::findOne($model->payroll_item_id);
                    if($PayrollItem){
                        $model->extraRemarks = 'Update salary '.$PayrollItem->name;
                    }else{
                        $model->extraRemarks = 'Update salary';
                    }
                    $model->extraEmployee = $model->employee_id;
                    $model->save();

                    // $fullname = Employee::findOne($employeeId)?->fullname ?? '-';
                    // EmployeeHistory::create('SAL'.date('Ymdhis'), "Update Salary", "Salary for $fullname has been successfully updated.");
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Salary updated successfully.',
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
            $PayrollItem = PayrollItem::findOne($model->payroll_item_id);
            if($PayrollItem){
                $model->extraRemarks = 'Update salary '.$PayrollItem->name;
            }else{
                $model->extraRemarks = 'Update salary';
            }
            $model->extraEmployee = $model->employee_id;

            if ($model->save()) {
                // $fullname = Employee::findOne($employeeId)?->fullname ?? '-';
                // EmployeeHistory::create('SAL'.date('Ymdhis'), "Update Salary", "Salary for $fullname has been successfully updated.");
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Salary updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $fullname = Employee::findOne($model->employee_id)?->fullname ?? '-';
        $model->delete();
        EmployeeHistory::create('SAL'.date('Ymdhis'), "Delete Salary", "Salary for $fullname has been successfully deleted.");

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Salary deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Salary deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // $fullname = Employee::findOne($model->employee_id)?->fullname ?? '-';
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Salary
        $model->status_id = 1;
        $model->extraRemarks = 'Reactive salary';
        $model->extraEmployee = $model->employee_id;
        $model->save();
        // EmployeeHistory::create('SAL'.date('Ymdhis'), "Reactive Salary", "Salary for $fullname has been successfully reactived.");

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Salary activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Salary activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        $fullname = Employee::findOne($model->employee_id)?->fullname ?? '-';
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Salary
        $model->status_id = 2;
        $model->save();
        EmployeeHistory::create('SAL'.date('Ymdhis'), "Non Active Salary", "Salary for $fullname has been successfully non actived.");

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Salary non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Salary non activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionUpload()
    {
        $model = new UploadForm();
        // $modelPayroll = new Payroll();

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_upload', [
                'model'     => $model,
            ]);
        }

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            
            if ($model->validate()) {
                $inputFile = $model->file->tempName;
                $result = Salary::saveRecords($inputFile);

                if($result === true){
                    Yii::$app->session->setFlash('success', 'The salary data was imported successfully.');
                    return $this->redirect(['index']);
                }else{
                    Yii::$app->session->setFlash('error', 'An error occurred: ' . $result);
                }
            }
        }

        return $this->render('_upload', [
            'model'     => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Salary::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
