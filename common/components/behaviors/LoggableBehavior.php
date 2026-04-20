<?php
namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\modules\satupayroll\models\LogActivity;

class LoggableBehavior extends Behavior
{
    public $modelName;
    private $_beforeValues = [];
    private $_beforeDelete = [];
    private $_dirtyAttributes = [];
    
    public $extraRemarks;
    public $extraEmployee;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function manualLog($action, $extraRemarks = null, $extraEmployee = null, $recordId = null)
    {
        $log = new LogActivity();
        $log->controller_action = $action;
        $log->model_name = $this->modelName ?: ($this->owner ? $this->owner->tableName() : 'EmployeeUpload');
        $log->record_id = $recordId ?? ($this->owner ? $this->owner->primaryKey : 1);
        $log->employee_id = $extraEmployee;
        $log->remarks = $extraRemarks ?? ucfirst($action) . ' performed';
        $log->ip_address = Yii::$app->request->userIP ?? 'console';
        $log->user_agent = Yii::$app->request->userAgent ?? 'console';
        $log->request_url = Yii::$app->request->url ?? 'console';
        $log->status = 'success';
        $log->save(false);
    }


    protected function log($action, $extraRemarks = null, $extraEmployee = null, $before = null, $after = null)
    {
        if ($this->owner->disableLog ?? false) { 
            return; // skip log 
        }

        $log = new LogActivity();
        $log->controller_action = $action;
        $log->model_name = $this->modelName ?: $this->owner->tableName();
        $log->record_id = is_array($this->owner->primaryKey)
            ? implode(',', $this->owner->primaryKey)
            : $this->owner->primaryKey;
        $log->ip_address = Yii::$app->request->userIP ?? 'console';
        $log->user_agent = Yii::$app->request->userAgent ?? 'console';
        $log->request_url = Yii::$app->request->url ?? 'console';
        $log->before_data = $before ? json_encode($before, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : null;
        $log->after_data = $after ? json_encode($after, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : null;
        $log->status = 'success';

        $employee_id = NULL;
        if (!empty($this->extraEmployee)) { 
            $employee_id = $this->extraEmployee; 
        }
        $log->employee_id = $employee_id;
        
        // default remarks 
        $remarks = ucfirst($action) . ' performed'; 
        // kalau ada tambahan info dari controller, gabungkan 
        if (!empty($this->extraRemarks)) { 
            $remarks = $this->extraRemarks; 
        } 
        $log->remarks = $remarks;

        if (!$log->save(false)) {
            Yii::error('LogActivity failed to save: ' . json_encode($log->getErrors()));
        }
    }

    public function beforeUpdate($event)
    {
        $dirty = $this->owner->getDirtyAttributes();
        $this->_beforeValues = [];

        foreach ($dirty as $attribute => $newValue) {
            $this->_beforeValues[$attribute] = $this->owner->getOldAttribute($attribute);
        }
    }

    public function beforeDelete($event)
    {
        $this->_beforeDelete = $this->owner->getAttributes();
    }

    public function afterInsert($event)
    {
        $this->log('create', null, null, null, $this->owner->getAttributes());
    }

    public function afterUpdate($event)
    {
        if (empty($this->_beforeValues)) {
            return;
        }

        $after = [];

        foreach ($this->_beforeValues as $attribute => $oldValue) {
            $newValue = $this->owner->{$attribute};

            // Skip Expression fields
            if ($newValue instanceof Expression) {
                continue;
            }

            $after[$attribute] = $newValue;
        }

        $this->log('update', null, null, $this->_beforeValues, $after);
    }

    public function afterDelete($event)
    {
        $this->log('delete', null, null, $this->_beforeDelete, null);
    }
}
