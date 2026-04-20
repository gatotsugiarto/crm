<?php

namespace common\modules\master\models;

use Yii;

use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\auth\models\User;
use common\modules\master\models\PayrollItem;
use common\modules\master\models\Employee;

use common\modules\payroll\models\EmployeeHistory;



class Salary extends \yii\db\ActiveRecord
{

    public $salary_type;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salary';
    }

    public function behaviors()
    {
        if ($this instanceof UserSearch) {
            return [];
        }

        return [
            // created_at & updated_at => NOW()
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],

            // created_by & updated_by => user login
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            
            // token protection untuk form
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'salary_token',
            ],
            
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Salary', // opsional, default pakai nama tabel
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['processed_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            // [['amount'], 'default', 'value' => 0.00],
            [['status_id'], 'default', 'value' => 1],
            [['is_processed'], 'default', 'value' => 0],
            [['employee_id', 'payroll_item_id', 'amount'], 'required'],
            [['employee_id', 'payroll_item_id', 'status_id', 'is_processed', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['insert_by', 'salary_type', 'processed_at', 'created_at', 'updated_at'], 'safe'],
            // [['employee_id', 'payroll_item_id'],
            //     'unique',
            //     'targetAttribute' => ['employee_id', 'payroll_item_id'],
            //     'message' => 'Jenis gaji telah diinput di pegawai yang sama',
            //     'on' => 'insertData',
            // ],

            ['payroll_item_id', 'validateDuplicateSalaryType', 'on' => ['insertData']],
        ];
    }

    public function validateDuplicateSalaryType($attribute, $params)
    {
        $exists = self::find()
            ->where(['employee_id' => $this->employee_id, 'payroll_item_id' => $this->payroll_item_id])
            // ->andWhere(['!=', 'id', $this->id]) 
            ->exists();

        if ($exists) {
            $this->addError('payroll_item_id', 'Jenis gaji telah diinput dipegawai yang sama');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee',
            'payroll_item_id' => 'Payroll Component',
            'amount' => 'Amount',
            'status_id' => 'Status',
            'is_processed' => 'Is Processed',
            'insert_by' => 'Insert From',
            'salary_type' => 'Salary Type',
            'processed_at' => 'Processed At',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }


    public static function saveRecords($inputFile)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $spreadsheet = IOFactory::load($inputFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                throw new \Exception("Empty file.");
            }

            $header = $rows[0];

            // 🔹 Mapping payroll_code => payroll_item_id
            $payrollMap = PayrollItem::find()
                ->select(['id', 'code'])
                ->indexBy('code')
                ->asArray()
                ->all();

            foreach ($rows as $index => $row) {

                if ($index == 0) {
                    continue; // skip header
                }

                $employeeId = trim($row[0]);

                if (empty($employeeId)) {
                    continue;
                }

                // 🔥 mulai dari BASIC (index 3) 📌 ID,NIP,Employee 🎯 mulai  BASIC dst...
                for ($i = 3; $i < count($header); $i++) {

                    $payrollCode = trim($header[$i]);
                    $amount = isset($row[$i]) ? (float)$row[$i] : 0;

                    if (!isset($payrollMap[$payrollCode])) {
                        continue; // skip jika code tidak ada
                    }

                    $payrollItemId = $payrollMap[$payrollCode]['id'];

                    $existing = self::find()
                        ->where([
                            'employee_id' => $employeeId,
                            'payroll_item_id' => $payrollItemId,
                        ])
                        ->one();
                    
                    // ✅ Jika sudah ada
                    if ($amount > 0) {
                        if ($existing) {

                            // Jika amount sama → SKIP
                            if ((float)$existing->amount == $amount) {
                                continue;
                            }

                            // Jika beda → UPDATE
                            $existing->employee_id = $employeeId;
                            $existing->amount = $amount;
                            $existing->insert_by = 'IMPORT';

                            $existing->extraRemarks = 'Update salary '.$existing->payrollItem->name;
                            $existing->extraEmployee = $employeeId;

                            $existing->save(false);

                        } else {

                            // ✅ Jika tidak ada → INSERT
                            $model = new self();
                            $model->employee_id = $employeeId;
                            $model->payroll_item_id = $payrollItemId;
                            $model->amount = $amount;
                            $model->insert_by = 'IMPORT';

                            $PayrollItem = PayrollItem::findOne($payrollItemId);
                            if($PayrollItem){
                                $model->extraRemarks = 'Create salary '.$PayrollItem->name;
                            }else{
                                $model->extraRemarks = 'Create salary';
                            }
                            $model->extraEmployee = $employeeId;

                            $model->save(false);
                        }
                    }
                }
            }

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $e;
        }
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollItem()
    {
        return $this->hasOne(PayrollItem::class, ['id' => 'payroll_item_id']);
    }

    public function getStatus()
    {
        return $this->hasOne(StatusActive::class, ['id' => 'status_id']);
    }

    public function getProcessed()
    {
        return $this->hasOne(Status::class, ['id' => 'is_processed']);
    }

    // Relasi ke user created
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Relasi ke user updated
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public static function updateSalary($after_status_id, $salaryIds)
    {
        self::updateAll(['status_id' => $after_status_id],['id' => $salaryIds]);
    }

}
