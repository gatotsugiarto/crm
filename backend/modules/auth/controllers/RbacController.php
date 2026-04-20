<?php

namespace backend\modules\auth\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\auth\models\AuthItem;
use common\modules\auth\models\AuthItemSearch;
use common\modules\auth\models\AuthItemChild;
use common\components\rbac\Pgenerator;

class RbacController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionValidate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    /** Pemission */
    public function actionPermission()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->searchPermission(Yii::$app->request->queryParams);
        $model = new AuthItem();

        return $this->render('permission', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionCreatepermission($group = 'backend')
    {
        $auth = Yii::$app->authManager;
        $Pgenerator = new Pgenerator();

        $modulePath = Yii::getAlias($group === 'frontend' ? '@frontend/modules' : '@backend/modules');
        $routes = $Pgenerator->getAllControllers($modulePath);
        $routes = $Pgenerator->getControllerActions($routes); // breakdown all items
        $existingPermissions = $auth->getPermissions();

        $existingNames = array_keys($existingPermissions);

        $routes = array_filter($routes, function($name) use ($existingNames) {
            return !in_array($name, $existingNames);
        }, ARRAY_FILTER_USE_KEY);

        if (Yii::$app->request->isPost) {
            $formPermissions = Yii::$app->request->post('formpermission', []);
            $parents = [];

            foreach ($formPermissions as $name => $val) {
                $isParent = strpos($name, '*') !== false;
                $baseName = strstr($name, strrchr($name, '.'), true);

                if ($isParent) {
                    // Parent permission: controller.*
                    $permission = $auth->getPermission($name);
                    if (!$permission) {
                        $permission = $auth->createPermission($name);
                        $permission->description = '[Controller permission] ' . strrchr($name, '.');
                        $auth->add($permission);
                    }
                    $parents[$baseName] = $permission;
                } else {
                    // Child permission: controller.action
                    $parentName = $baseName . '.*';
                    if (!isset($parents[$baseName])) {
                        $parent = $auth->getPermission($parentName);
                        if (!$parent) {
                            $parent = $auth->createPermission($parentName);
                            $parent->description = '[Controller permission] ' . $parentName;
                            $auth->add($parent);
                        }
                        $parents[$baseName] = $parent;
                    }

                    $child = $auth->getPermission($name);
                    if (!$child) {
                        $child = $auth->createPermission($name);
                        $child->description = '[Action permission] ' . $name;
                        $auth->add($child);
                    }

                    // Add child to parent
                    if (!$auth->hasChild($parents[$baseName], $child)) {
                        $auth->addChild($parents[$baseName], $child);
                    }
                }
            }

            Yii::$app->session->setFlash('success', 'Permissions successfully generated.');
            return $this->redirect(['permission']);
        }

        return $this->render('create_permission', [
            'routes' => $routes,
            'permissions' => $existingPermissions,
            'group' => $group,
        ]);
    }

    public function actionViewpermission($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_permission', ['model' => $model]);
        }

        return $this->render('view_permission', ['model' => $model]);
    }

    public function actionDeletepermission($id)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($id);

        if ($permission && $auth->remove($permission)) {
            Yii::$app->session->setFlash('success', "Permission <strong>{$id}</strong> deleted successfully.");
        } else {
            Yii::$app->session->setFlash('danger', "Failed to delete permission <strong>{$id}</strong>.");
        }

        return $this->redirect(['permission']);
    }

    /** Role */
    public function actionRole()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->searchRole(Yii::$app->request->queryParams);

        return $this->render('role', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreaterole()
    {
        $model = new AuthItem();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $auth = Yii::$app->authManager;

                    // add "role"
                    $role = $auth->createRole($model->name);
                    $role->description = $model->description;
                    $auth->add($role);

                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Role created successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $model->getErrors(),
                ];
            }

            // GET pertama kali buka modal → generate token baru
            $formToken = $model->getBehavior('tokenProtection')->generateToken();
            return $this->renderAjax('_form_role', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        // === Fallback Non-AJAX ===
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Role created successfully.');
                return $this->redirect(['role']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form_role', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionViewrole($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_role', ['model' => $model]);
        }

        return $this->render('view_role', ['model' => $model]);
    }


    public function actionUpdaterole($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Role updated successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $model->getErrors(),
                ];
            }

            // GET pertama kali buka modal → generate token baru
            $formToken = $model->getBehavior('tokenProtection')->generateToken();
            return $this->renderAjax('_form_role', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        // === Fallback Non-AJAX ===
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Role updated successfully.');
                return $this->redirect(['role']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form_role', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionRoleassignment($id)
    {
        $model = $this->findModel($id);
        $auth = Yii::$app->authManager;
        $roleAssignment = $auth->getChildren($id);
        $allRoles = $auth->getRoles();
        $allPermissions = $auth->getPermissions();

        if ($this->request->isPost && $model->load($this->request->post())) {

            $name = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $model->name));
            $parent_role = $auth->getRole($name);

            // Roles
            if(isset($_POST['roles'])){
                foreach ($_POST['roles'] as $role){
                    try{
                        $role = $auth->getRole($role);
                        $auth->addChild($parent_role,$role);
                    }catch( yii\base\InvalidCallException $e){
                        \Yii::$app->getSession()->setFlash('error', 'Caught exception: '.  $e->getMessage(). "\n");
                    }
                }
            }
            
            // Permissions
            if(isset($_POST['permissions'])){
                foreach ($_POST['permissions'] as $permission){
                    try{
                        $permission = $auth->getPermission($permission);
                        $auth->addChild($parent_role,$permission);
                    }catch( yii\base\InvalidCallException $e){
                        \Yii::$app->getSession()->setFlash('error', 'Caught exception: '.  $e->getMessage(). "\n");
                    }
                }
            }

            Yii::$app->session->setFlash('success', "Role/permission has been assignment.");
            return $this->redirect(['roleassignment', 'id' => $model->name]);
        }

        
        return $this->render('roleassignment', [
            'model' => $model,
            'allRoles' => $allRoles,
            'allPermissions' => $allPermissions,
            'roleAssignment' => $roleAssignment,
        ]);
    }

    public function actionDeleteroleassign($parent, $name)
    {
        $auth = Yii::$app->authManager;
        $itemparent = $auth->getRole($parent);
        $itemchild  = $auth->getRole($name);
        if(empty($itemchild)){ 
            $itemchild  = $auth->getPermission($name);
        }
        
        $auth->removeChild($itemparent, $itemchild);

        Yii::$app->session->setFlash('success', "Delete role/permission has been success.");
        return $this->redirect(['roleassignment', 'id' => $parent]);
    }

    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }
}

