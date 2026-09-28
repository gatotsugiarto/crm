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
     * Whether the current user may convert/change this lead. Allowed for members of
     * the lead's Sales Team (user.team_id = lead.owner_user_id), for the Sales
     * Manager (ASSIGN; there is one manager over several teams) and for root.
     * A user without a team can't work on any existing lead.
     *
     * @param \common\modules\sales\models\Lead $lead
     * @param string $verb shown in the message, e.g. "convert" or "change"
     * @return string|null error message, or null when allowed
     */
    public static function leadTeamError($lead, $verb)
    {
        if (!(Yii::$app instanceof \yii\web\Application) || self::canAssign()) {
            return null;
        }

        $user = Yii::$app->user->identity;
        if ($user !== null && $lead->owner_user_id !== null && (int) $user->team_id === (int) $lead->owner_user_id) {
            return null;
        }

        $team = $lead->team?->name;
        return $team !== null
            ? "This lead belongs to {$team}. Only members of that team can {$verb} it."
            : "This lead has no Sales Team. Only a Sales Manager can {$verb} it.";
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
