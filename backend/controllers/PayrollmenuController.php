<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\User;
use common\models\Member;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;



/**
 * Site controller
 */
class PayrollmenuController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('payrollmenu');
    }

    // public function actionMasterdata()
    // {
    //     return $this->render('masterdata');
    // }

    // public function actionValidate()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $user = Yii::$app->user->identity;
    //     $model = new ChangePasswordUser($user);

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    // public function actionChangepassword($id = null)
    // {
    //     $user = User::findOne($id ?? Yii::$app->user->id);
    //     if (!$user) {
    //         throw new NotFoundHttpException('User not found.');
    //     }

    //     $model = new ChangePasswordUser($user);

    //     if (Yii::$app->request->isPost) {
    //         Yii::$app->response->format = Response::FORMAT_JSON;

    //         if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
    //             // ✅ Set flash message di session (opsional, untuk fallback redirect)
    //             Yii::$app->session->setFlash('success', 'Password changed successfully.');

    //             // ✅ Kirim flash message via JSON agar bisa ditampilkan langsung
    //             return [
    //                 'success' => true,
    //                 'message' => Yii::$app->session->getFlash('success'),
    //             ];
    //         }

    //         return [
    //             'success' => false,
    //             'message' => 'Failed to change password.',
    //             'errors' => $model->getErrors(),
    //         ];
    //     }

    //     Yii::$app->response->format = Response::FORMAT_HTML;

    //     return $this->renderAjax('_form_changepassword', [
    //         'model' => $model,
    //     ]);
    // }
}