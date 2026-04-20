<?php

namespace common\modules\auth\models;

use Yii;
use yii\db\ActiveRecord;
use yii\rbac\Item;

use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

/**
 * Model untuk tabel "auth_item".
 */
class AuthItem extends ActiveRecord
{
    public function behaviors()
    {
        return [
            // created_at & updated_at => NOW()
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => time(),
            ],
            // created_by & updated_by => user login
            // [
            //     'class' => BlameableBehavior::class,
            //     'createdByAttribute' => 'created_by',
            //     'updatedByAttribute' => 'updated_by',
            // ],
            // token protection untuk form
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'role_token',
            ],
            // log activity otomatis
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Role', // opsional, default pakai nama tabel
            ],
        ];
    }

    public static function tableName()
    {
        return 'auth_item';
    }

    public function rules()
    {
        return [
            [['name', 'description', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            ['name', 'validateRolename'],
        ];
    }

    public function validateRolename($attribute, $params)
    {
        if ($this->name !== str_replace(' ', '', $this->name)) {
            $this->addError($attribute, 'Whitespace not allowed.');
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->name)) {
            $this->addError($attribute, 'Special character not allowed.');
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::class, ['item_name' => 'name']);
    }

    public function getRuleName()
    {
        return $this->hasOne(AuthRule::class, ['name' => 'rule_name']);
    }

    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::class, ['parent' => 'name']);
    }

    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::class, ['child' => 'name']);
    }

    /**
     * Ambil semua permission sebagai array indexed by name.
     * @return AuthItem[]
     */
    public static function getPermissionAsArray()
    {
        return static::find()
            ->where(['type' => Item::TYPE_PERMISSION])
            ->indexBy('name')
            ->all();
    }

    /**
     * Dropdown list untuk permission.
     * @return array
     */
    public static function dropdown()
    {
        static $dropdown;

        if ($dropdown === null) {
            $dropdown = ['#' => '#'];
            $models = static::find()
                ->where(['type' => Item::TYPE_PERMISSION])
                ->select(['name'])
                ->orderBy('name')
                ->all();

            foreach ($models as $model) {
                $dropdown[$model->name] = $model->name;
            }
        }

        return $dropdown;
    }

    /**
     * Dropdown list untuk basic role.
     * @return array
     */
    public static function dropdownBasic()
    {
        static $dropdown;

        if ($dropdown === null) {
            // $dropdown = ['#' => '#'];
            $models = static::find()
                ->where(['type' => Item::TYPE_ROLE, 'data' => 2])
                ->select(['name','description'])
                ->orderBy('name')
                ->all();

            foreach ($models as $model) {
                $dropdown[$model->name] = $model->description;
            }
        }

        return $dropdown;
    }
}
