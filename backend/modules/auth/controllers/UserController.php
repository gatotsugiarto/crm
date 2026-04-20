<?php

namespace backend\modules\auth\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\auth\models\User;
use common\modules\auth\models\UserSearch;
use common\modules\auth\models\ChangePasswordUser;

class UserController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new User(); // untuk form create

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionValidatechangepassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = new ChangePasswordUser($user);

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    public function actionValidate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    public function actionCreate()
    {
        $model = new User();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'User created successfully.',
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
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        // === Fallback Non-AJAX ===
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'User created successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('create', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $basicRole = $model->getRole();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->saveRole($model->role);
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'User updated successfully.',
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
            return $this->renderAjax('_form', [
                'basicRole'     => $basicRole,
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        // === Fallback Non-AJAX ===
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $model->saveRole($model->role);
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'User updated successfully.');
                return $this->redirect(['index']);
            }

            // Yii::$app->session->setFlash('success', 'User updated successfully.');
            // return $this->redirect(['index']);
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('update', [
            'model'     => $model,
            'basicRole'     => $basicRole,
            'formToken' => $formToken,
        ]);
    }

    public function actionChangepassword($id = null)
    {
        $user = User::findOne($id ?? Yii::$app->user->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $model = new ChangePasswordUser($user);

        // Jika POST (submit form)
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON; // 🔥 WAJIB di sini

            if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
                return ['success' => true, 'message' => 'Password changed successfully.'];
            }

            return [
                'success' => false,
                'message' => 'Failed to change password.',
                'errors' => $model->getErrors(),
            ];
        }

        // Jika GET (render modal)
        Yii::$app->response->format = Response::FORMAT_HTML;
        // Yii::info('Render form change password (HTML mode)', __METHOD__);

        return $this->renderAjax('_form_changepassword', [
            'model' => $model,
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

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        // $model->delete();
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        $model->status = 0;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'User delete successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'User deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionSuspend($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        $model->status = 9;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'User suspend successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'User suspend successfully.');
        return $this->redirect(['index']);
    }

    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');
        $model->status = 10;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'User reactive successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'User reactive successfully.');
        return $this->redirect(['index']);
    }

    public function actionReset($id)
    {
        $model = $this->findModel($id);
        $model->resetPassword();
        $model->save(false);
        // if (!$model->save(false)) {
        //     Yii::error("Failed to reset password for user ID: {$model->id}", __METHOD__);
        //     if (Yii::$app->request->isAjax) {
        //         Yii::$app->response->format = Response::FORMAT_JSON;
        //         return ['success' => false, 'message' => 'Save failed'];
        //     }
        // }

        // if (Yii::$app->request->isAjax) {
        //     Yii::$app->response->format = Response::FORMAT_JSON;
        //     return ['success' => true];
        // }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'User reset password successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'User reset password successfully.');
        return $this->redirect(['index']);
    }

    public function actionAssignment($id)
    {
        $model = $this->findModel($id);
        $auth = Yii::$app->authManager;
        $roleAssignment = $auth->getAssignments($model->id);
        $allRoles = $auth->getRoles();
        $allPermissions = $auth->getPermissions();

        
        // if ($this->request->isPost && $model->load($this->request->post())) {
        if ($this->request->isPost) {    

            // Roles
            if(isset($_POST['roles'])){
                foreach ($_POST['roles'] as $role){
                    try{
                        $role_name = $auth->getRole($role);
                        $auth->assign($role_name, $id);
                    }catch( yii\base\InvalidCallException $e){
                        \Yii::$app->getSession()->setFlash('error', 'Caught exception: '.  $e->getMessage(). "\n");
                    }
                }
            }

            // Permissions
            if(isset($_POST['permissions'])){
                foreach ($_POST['permissions'] as $permission){
                    try{
                        $permission_name = $auth->getPermission($permission);
                        $auth->assign($permission_name, $id);
                    }catch( yii\base\InvalidCallException $e){
                        \Yii::$app->getSession()->setFlash('error', 'Caught exception: '.  $e->getMessage(). "\n");
                    }
                }
            }
            
            Yii::$app->session->setFlash('success', "Add User role/permission assignment has been success.");
            return $this->redirect(['assignment', 'id' => $id]);
        }

        return $this->render('_assignment', [
            'model' => $model,
            'allRoles' => $allRoles,
            'roleAssignment' => $roleAssignment,
            'allPermissions' => $allPermissions,
        ]);
    }

    public function actionDeleteuserassignment($user_id, $rolepermission)
    {
        $model = $this->findModel($user_id);
        $auth = Yii::$app->authManager;
        $roleAssignment = $auth->getAssignments($model->id);
        $allRoles = $auth->getRoles();

        if(isset($rolepermission)){
            // Roles
            if ( !empty($auth->getRole($rolepermission)) ){
                $role_name = $auth->getRole($rolepermission);
                $auth->revoke($role_name, $user_id);
                Yii::$app->session->setFlash('success', "Delete User role assignment has been success.");
            }
            // Permissions
            if ( !empty($auth->getPermission($rolepermission)) ){
                $permission_name = $auth->getPermission($rolepermission);
                $auth->revoke($permission_name, $user_id);

                Yii::$app->session->setFlash('success', "Delete User permission assignment has been success.");
            }
        }

        return $this->redirect(['assignment', 'id' => $user_id]);

        return $this->render('assignment', [
            'model' => $model,
            'allRoles' => $allRoles,
            'roleAssignment' => $roleAssignment,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }

    protected function findModelChangePassword($user)
    {
        return new ChangePasswordUser($user);
    }
}

