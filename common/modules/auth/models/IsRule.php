<?php
namespace common\modules\auth\models;

use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class IsRule extends Rule
{
    public $name = 'isRule';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    
    public function execute($user, $item, $params)
    {
        if(isset($params['model'])){ //Directly specify the model you plan to use via param
            $model = $params['model']; 
        }else{ //Use the controller findModel method to get the model - this is what executes via the behaviour/rules
            $id = \Yii::$app->request->get('id'); //Note, this is an assumption on your url structure.
            $model = \Yii::$app->controller->findUserModel($id); //Note, this only works if you change findModel to be a public function within the controller.
        }
        return $model->created_by == $user;
    }
}
?>