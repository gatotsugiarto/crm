<?php

namespace backend\modules\auth\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\auth\models\Member;
use common\modules\auth\models\MemberSearch;
use common\modules\auth\models\ChangePasswordMember;

class MemberController extends Controller
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
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Member(); // untuk form create

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionValidatechangepassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $member = Yii::$app->user->identity;
        $model = new ChangePasswordMember($member);

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    public function actionValidate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Member();

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    public function actionCreate()
    {
        $model = new Member();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Member created successfully.',
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
                Yii::$app->session->setFlash('success', 'Member created successfully.');
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

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Member updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Member updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('update', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionChangepassword($id = null)
    {
        $member = Member::findOne($id ?? Yii::$app->user->id);
        if (!$member) {
            throw new NotFoundHttpException('Member not found.');
        }

        $model = new ChangePasswordMember($member);

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
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Member deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionSuspend($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Member suspend successfully.');
        return $this->redirect(['index']);
    }

    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        $model->status = 10;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Member reactive successfully.');
        return $this->redirect(['index']);
    }

    public function actionReset($id)
    {
        $model = $this->findModel($id);
        $model->resetPassword();
        if (!$model->save(false)) {
            Yii::error("Failed to reset password for member ID: {$model->id}", __METHOD__);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Save failed'];
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Member reset password successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested member does not exist.');
    }

    protected function findModelChangePassword($member)
    {
        return new ChangePasswordMember($member);
    }
}

