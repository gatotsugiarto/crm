<?php

namespace common\components\rbac;

use Yii;

/**
 * Permission checks for sales rules that live below the action level, i.e.
 * things a user could otherwise do through an edit form they are allowed to
 * open (approving a quotation by picking status "Approved", changing who owns a
 * record). Used by models (validation) and views (hiding buttons/fields).
 *
 * Outside a web request (console, migrations, tests without a logged-in user)
 * every check passes, so system code is never blocked.
 */
class SalesAccess
{
    /** Change Sales Team / Assigned Sales on existing lead, account, opportunity. */
    const ASSIGN = 'backend.sales.assign';

    /**
     * Same rule as the controllers' AccessControl matchCallback: the action
     * permission, its controller's `.*` permission, or root.
     */
    public static function can($permission)
    {
        if (!Yii::$app->has('user') || Yii::$app->user->isGuest) {
            return !(Yii::$app instanceof \yii\web\Application);
        }

        $user = Yii::$app->user;
        if ($user->can($permission) || $user->can('root')) {
            return true;
        }

        $dot = strrpos($permission, '.');
        return $dot !== false && $user->can(substr($permission, 0, $dot) . '.*');
    }

    public static function canAssign()
    {
        return self::can(self::ASSIGN);
    }

    public static function canApproveQuotation()
    {
        return self::can('backend.sales.quotation.approve');
    }

    /**
     * Inline-validator body: on an existing record, only users with ASSIGN may
     * change an ownership attribute (owner_user_id = Sales Team, assigned_user_id).
     * Setting it while creating a record is allowed for everyone.
     */
    public static function checkAssignment($model, $attribute)
    {
        if (!$model->isNewRecord && $model->isAttributeChanged($attribute, false) && !self::canAssign()) {
            $model->addError($attribute, 'Only a Sales Manager can change ' . $model->getAttributeLabel($attribute) . ' on an existing record.');
        }
    }
}
