<?php


namespace backend\modules\sales\controllers;

use Yii;

use common\modules\sales\models\Lead;
use common\modules\sales\models\LeadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * LeadController implements the CRUD actions for Lead model.
 */
class LeadController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        $route = 'backend.'.str_replace('/','.',$this->getRoute());
                        $parents = strstr($route, strrchr ($route,'.'),true).'.*';
                        if (\Yii::$app->user->can($route) || \Yii::$app->user->can($parents) || \Yii::$app->user->can("root")){
                            return true;
                        }
                    }
                ],
            ],
        ];
        
        return $behaviors;
    }

    // public function actionValidate()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $model = new Lead();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all Lead models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lead model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new Lead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new Lead(['scenario' => 'insertData']);
        $model = new Lead();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $model->save();
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Lead created successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => ActiveForm::validate($model),
                ];
            }

            $formToken = $model->getBehavior('tokenProtection')->generateToken();
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Lead created successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Updates an existing Lead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // $model->scenario = 'updateData';

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Lead updated successfully.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $model->getErrors(),
                ];
            }

            $formToken = $model->getBehavior('tokenProtection')->generateToken(); // GET pertama kali buka modal → generate token baru
            return $this->renderAjax('_form', [
                'model'     => $model,
                'formToken' => $formToken,
            ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $model->getBehavior('tokenProtection')->consumeToken();
                Yii::$app->session->setFlash('success', 'Lead updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Deletes an existing Lead model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Lead deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Lead deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionConvert($id)
    {
        $model = $this->findModel($id);

        if ($model->is_converted) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'This lead has already been converted.',
                ];
            }
            Yii::$app->session->setFlash('warning', 'This lead has already been converted.');
            return $this->redirect(['index']);
        }

        $userId = Yii::$app->user->id;

        try {
            $db = Yii::$app->db;

            // Jalankan stored procedure dengan OUT parameter
            $db->createCommand('CALL sp_convert_lead_to_customer(:lead_id, :user_id, @new_account_id, @new_contact_id, @new_opportunity_id)')
                ->bindValue(':lead_id', $id)
                ->bindValue(':user_id', $userId)
                ->execute();

            // Ambil OUT parameter
            $result = $db->createCommand('SELECT @new_account_id AS account_id, @new_contact_id AS contact_id, @new_opportunity_id AS new_opportunity_id')
                ->queryOne();

            $newAccountId = $result['account_id'] ?? null;
            $newContactId = $result['contact_id'] ?? null;
            $newOpportunityId = $result['new_opportunity_id'] ?? null;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success'    => true,
                    'message'    => "Lead \"{$model->company_name}\" has been successfully converted to Account and Contact.",
                    'account_id' => $newAccountId,
                    'contact_id' => $newContactId,
                    'opportunity_id' => $newOpportunityId,
                ];
            }

            Yii::$app->session->setFlash('success', "Lead \"{$model->company_name}\" converted successfully.");
            return $this->redirect(['index']);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Tangkap pesan dari SIGNAL SP
            if (str_contains($errorMessage, 'Lead not found or already converted')) {
                $errorMessage = 'This lead has already been converted or does not exist.';
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }

            Yii::$app->session->setFlash('danger', $errorMessage);
            return $this->redirect(['index']);
        }
    }

    /*
    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Lead        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Lead activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Lead activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Lead        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Lead non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Lead non activate successfully.');
        return $this->redirect(['index']);
    }
    */

    /**
     * Finds the Lead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lead::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
