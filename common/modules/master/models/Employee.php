<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\PayrollProfile;
use common\modules\master\models\EmployeePayrollProfile;
use common\modules\master\models\Salary;
use common\modules\master\models\CostCenter;
use common\modules\auth\models\User;


class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee';
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
                'modelName' => 'Employee', // opsional, default pakai nama tabel
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'company_id', 'branch_id', 'site_office_id', 'department_id', 'division_id', 'marital_status_id', 'family_status_id', 'ptkp_id', 'level_jabatan_id', 'jabatan_id', 'grade_id', 'is_npwp', 'jkk_id', 'bank_id', 'employee_status_id', 'join_prorate', 'resign_prorate', 'status_id', 'created_by', 'updated_by'], 'integer'],
            [['region', 'company', 'branch', 'site_office', 'department', 'division', 'fullname', 'marital_status', 'family_status', 'level_jabatan', 'jabatan', 'grade', 'bank', 'bank_no', 'employee_status'], 'required'],
            [['join_date', 'resign_date', 'created_at', 'updated_at'], 'safe'],
            [['bpjs_tk', 'bpjs_kes', 'jkk', 'address'], 'string'],
            [['region', 'company', 'branch', 'site_office', 'department', 'division', 'fullname', 'marital_status', 'family_status', 'ptkp', 'level_jabatan', 'jabatan', 'grade', 'npwp_id', 'bank', 'bank_no', 'employee_status'], 'string', 'max' => 255],
            [['e_number'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['e_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'region' => 'Region',
            'company_id' => 'Company ID',
            'company' => 'Company',
            'branch_id' => 'Branch ID',
            'branch' => 'Branch',
            'site_office_id' => 'Site Office ID',
            'site_office' => 'Site Office',
            'department_id' => 'Department ID',
            'department' => 'Department',
            'division_id' => 'Division ID',
            'division' => 'Division',
            'e_number' => 'NIP',
            'fullname' => 'Employee',
            'join_date' => 'Join Date',
            'marital_status_id' => 'Marital Status ID',
            'marital_status' => 'Marital Status',
            'family_status_id' => 'Family Status ID',
            'family_status' => 'Family Status',
            'ptkp_id' => 'PTKP ID',
            'ptkp' => 'PTKP',
            'level_jabatan_id' => 'Level Jabatan ID',
            'level_jabatan' => 'Level Jabatan',
            'jabatan_id' => 'Jabatan ID',
            'jabatan' => 'Jabatan',
            'grade_id' => 'Grade ID',
            'grade' => 'Grade',
            'email' => 'Email',
            'is_npwp' => 'Mempunyai NPWP',
            'npwp_id' => 'No NPWP',
            'bpjs_tk' => 'Bpjs Tk',
            'bpjs_kes' => 'Bpjs Kes',
            'jkk_id' => 'JKK ID',
            'jkk' => 'JKK',
            'bank_id' => 'Bank ID',
            'bank' => 'Bank',
            'bank_no' => 'No Rekening',
            'employee_status_id' => 'Employee Status ID',
            'employee_status' => 'Employee Status',
            'resign_date' => 'Resign Date',
            'join_prorate' => 'Join Prorate',
            'resign_date' => 'Resign Date',
            'resign_prorate' => 'Resign Prorate',
            'address' => 'Address',
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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

    public function getCostcenter()
    {
        return $this->hasOne(CostCenter::class, ['id' => 'cost_center_id']);
    }

    public function close_salary($status_id)
    {
        $employee_id = $this->id;
        Salary::updateAll(['status_id' => $status_id], ['employee_id' => $employee_id]);
    }

    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    public static function tetap()
    {
        return (int) self::find()->select('COUNT(*)')->where(['status_id' => 1])->andWhere(['<', 'employee_status_id', 3])->scalar();
    }

    public static function pkwt()
    {
        return (int) self::find()->select('COUNT(*)')->where(['status_id' => 1, 'employee_status_id' => 3])->scalar();
    }

    public static function total()
    {
        return (int) self::find()->select('COUNT(*)')->where(['status_id' => 1])->scalar();
    }

    public static function no_salary()
    {
        $query = "SELECT COUNT(a.id) AS no_salary FROM employee a LEFT JOIN (SELECT employee_id FROM salary WHERE payroll_item_id = 1 AND status_id = 1) b ON a.id = b.employee_id WHERE a.status_id = 1 AND b.employee_id IS NULL";
        $result = \Yii::$app->db->createCommand($query)->queryOne();
        $no_salary = 0;
        if($result){
            $no_salary = $result['no_salary'];
        }

        return $no_salary;
    }

    public static function employeepayrollprofile($employee_id)
    {
        $profile_id = PayrollProfile::getDefaultId();

        if (!$profile_id) {
            return '';
        }

        // cek apakah sudah ada
        $exists = EmployeePayrollProfile::find()
            ->where([
                'employee_id' => $employee_id,
                'profile_id'  => $profile_id,
            ])
            ->exists();

        if (!$exists) {
            $model = new EmployeePayrollProfile();
            $model->employee_id = $employee_id;
            $model->profile_id  = $profile_id;
            $model->extraRemarks = 'Create payroll method';
            $model->extraEmployee = $employee_id;

            if (!$model->save()) {
                Yii::error(
                    "Failed to create EmployeePayrollProfile | employee_id={$employee_id} | profile_id={$profile_id} | errors=" .
                    \yii\helpers\VarDumper::dumpAsString($model->errors),
                    'payroll'
                );
            }
        }

        $payrollProfile = PayrollProfile::findOne($profile_id);
        return $payrollProfile ? $payrollProfile->profile_name : '';
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->fullname.' ['.$model->e_number.']';
            }
        }
            
        return $dropdown;
    }

    public static function dropdown_payroll()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            // $models = static::find()->all();
            $models = Employee::find()->alias('e')
            ->innerJoin('v_employee ve', 've.id = e.id')
            ->select(['e.*'])
            ->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->fullname.' ['.$model->e_number.']';
            }
        }
            
        return $dropdown;
    }
}
