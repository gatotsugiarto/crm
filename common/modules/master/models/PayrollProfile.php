<?php

namespace common\modules\master\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\auth\models\User;

class PayrollProfile extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const PAYROLL_MODE_GROSS = 'GROSS';
    const PAYROLL_MODE_GROSS_UP = 'GROSS_UP';
    const PAYROLL_MODE_NET = 'NET';
    
    public $item_id = [];
    public $employee_id = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payroll_profile';
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
                'sessionKey' => 'payroll_profile_token',
            ],
            
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'PayrollProfile', // opsional, default pakai nama tabel
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_name', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['is_default'], 'default', 'value' => 0],
            [['payroll_mode'], 'required'],
            [['payroll_mode'], 'string'],
            [['status_id', 'created_by', 'updated_by'], 'integer'],
            [['employee_id', 'item_id', 'created_at', 'updated_at'], 'safe'],
            // [['employee_id', 'item_id'], 'each', 'rule' => ['integer']],
            [['profile_name'], 'string', 'max' => 100],
            ['payroll_mode', 'in', 'range' => array_keys(self::optsPayrollMode())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_name' => 'Profile Name',
            'payroll_mode' => 'Payroll Scheme',
            'is_default' => 'Set Default',
            'status_id' => 'Status',
            'employee_id' => 'Employees',
            'item_id' => 'Components',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // 1️⃣ Jika profile ini diset sebagai default
        if ($this->is_default == 1) {
            self::updateAll(
                ['is_default' => 0],
                ['<>', 'id', $this->id]
            );
            return true;
        }

        // 2️⃣ Jika TIDAK ADA satupun default
        $existsDefault = self::find()
            ->where(['is_default' => 1])
            ->andWhere(['<>', 'id', $this->id])
            ->exists();

        if (!$existsDefault) {
            // Ambil ID terkecil
            $minId = self::find()->min('id');

            if ($minId !== null) {
                self::updateAll(
                    ['is_default' => 1],
                    ['id' => $minId]
                );
            }
        }

        return true;
    }

    public static function getDefaultId()
    {
        return self::find()
            ->select('id')
            ->where(['is_default' => 1])
            ->scalar(); // return id atau null
    }

    public function saveEmployees()
    {
        if (empty($this->employee_id)) {
            return true;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Hapus data lama
            EmployeePayrollProfile::deleteAll(['employee_id' => $this->employee_id]);

            // Siapkan data batch insert
            $rows = [];
            foreach ($this->employee_id as $employee_id) {
                $rows[] = [
                    $this->id,
                    $employee_id,
                    1,
                    date('Y-m-d H:i:s'),
                    Yii::$app->user->id,
                    date('Y-m-d H:i:s'),
                    Yii::$app->user->id
                ];
            }

            // Batch insert
            $db->createCommand()->batchInsert(
                EmployeePayrollProfile::tableName(),
                ['profile_id', 'employee_id', 'status_id', 'created_at', 'created_by', 'updated_at', 'updated_by'],
                $rows
            )->execute();

            $transaction->commit();
            return true;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('EmployeePayrollProfile insert failed: ' . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    public function saveComponents()
    {
        if (empty($this->item_id)) {
            return true;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Hapus data lama
            PayrollProfileComponent::deleteAll(['profile_id' => $this->id]);

            // Siapkan data batch insert
            $rows = [];
            foreach ($this->item_id as $item_id) {
                $rows[] = [
                    $this->id,
                    $item_id,
                    1,
                    date('Y-m-d H:i:s'),
                    Yii::$app->user->id,
                    date('Y-m-d H:i:s'),
                    Yii::$app->user->id
                ];
            }

            // Batch insert
            $db->createCommand()->batchInsert(
                PayrollProfileComponent::tableName(),
                ['profile_id', 'item_id', 'status_id', 'created_at', 'created_by', 'updated_at', 'updated_by'],
                $rows
            )->execute();

            $transaction->commit();
            return true;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('PayrollProfileComponent insert failed: ' . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Gets query for [[EmployeePayrollProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeePayrollProfiles()
    {
        return $this->hasMany(EmployeePayrollProfile::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[Employees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::class, ['id' => 'employee_id'])->viaTable('employee_payroll_profile', ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[PayrollProfileComponents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrollProfileComponents()
    {
        return $this->hasMany(PayrollProfileComponent::class, ['profile_id' => 'id']);
    }

    public function getStatus()
    {
        return $this->hasOne(StatusActive::class, ['id' => 'status_id']);
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


    /**
     * column payroll_mode ENUM value labels
     * @return string[]
     */
    public static function optsPayrollMode()
    {
        return [
            self::PAYROLL_MODE_GROSS => 'GROSS',
            self::PAYROLL_MODE_GROSS_UP => 'GROSS_UP',
            self::PAYROLL_MODE_NET => 'NET',
        ];
    }

    /**
     * @return string
     */
    public function displayPayrollMode()
    {
        return self::optsPayrollMode()[$this->payroll_mode];
    }

    /**
     * @return bool
     */
    public function isPayrollModeGross()
    {
        return $this->payroll_mode === self::PAYROLL_MODE_GROSS;
    }

    public function setPayrollModeToGross()
    {
        $this->payroll_mode = self::PAYROLL_MODE_GROSS;
    }

    /**
     * @return bool
     */
    public function isPayrollModeGrossup()
    {
        return $this->payroll_mode === self::PAYROLL_MODE_GROSS_UP;
    }

    public function setPayrollModeToGrossup()
    {
        $this->payroll_mode = self::PAYROLL_MODE_GROSS_UP;
    }

    /**
     * @return bool
     */
    public function isPayrollModeGrossnet()
    {
        return $this->payroll_mode === self::PAYROLL_MODE_NET;
    }

    public function setPayrollModeToGrossnet()
    {
        $this->payroll_mode = self::PAYROLL_MODE_NET;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            //$dropdown[0] = 'None';
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->profile_name;
            }
        }
        
        return $dropdown;
    }
}
