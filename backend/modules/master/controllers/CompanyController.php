<?php

namespace backend\modules\master\controllers;

use Yii;
use yii\web\Response;

use common\modules\master\models\Company;
use common\modules\master\models\CompanySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;


/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
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
                        if (\Yii::$app->user->can($route) || \Yii::$app->user->can($parents) || \Yii::$app->user->can("root")) {
                             return true;
                        }
                        }
                 ],
             ],
        ];

        return $behaviors;
    }

    /**
     * Lists all Company models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Company model.
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

    public function actionValidate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Company();

        if ($model->load(Yii::$app->request->post())) {
            return \yii\widgets\ActiveForm::validate($model);
        }

        return [];
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Company();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate() && $model->save()) {
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Company created successfully.',
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
                Yii::$app->session->setFlash('success', 'Company created successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('create', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
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
                        'message' => 'Company updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Company updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('update', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    // // Contoh LoggableBehavior via controller
    // public function actionUpdate($id)
    // {
    //     $model = Company::findOne($id);
    //     $old = $model->attributes;

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         // override log dengan remarks khusus
    //         $log = new LogActivity();
    //         $log->controller_action = 'update';
    //         $log->model_name = 'company';
    //         $log->record_id = $model->id;
    //         $log->action_by = Yii::$app->user->id;
    //         $log->created_at = date('Y-m-d H:i:s');
    //         $log->ip_address = Yii::$app->request->userIP;
    //         $log->user_agent = Yii::$app->request->userAgent;
    //         $log->request_url = Yii::$app->request->url;
    //         $log->before_data = json_encode($old);
    //         $log->after_data = json_encode($model->attributes);
    //         $log->status = 'success';
    //         $log->remarks = 'Custom update: status changed by admin';
    //         $log->save(false);

    //         return $this->redirect(['view', 'id' => $model->id]);
    //     }

    //     return $this->render('update', ['model' => $model]);
    // }

    /**
     * Deletes an existing Company model.
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
                'message' => 'Company deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Company deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionTogglestatus($id)
    {
        $model = $this->findModel($id);
        $oldStatus = $model->status_id;
        $model->status_id = ($oldStatus == 1) ? 2 : 1;

        if ($model->save()) {
            $text = ($oldStatus == 1)
                ? 'Company has been deactivated.'
                : 'Company has been activated.';

            Yii::$app->session->setFlash('success', $text);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update status.');
        }

        return $this->redirect(['index']);
    }

    public function actionUploadattachment()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new Company();
        $imageLink = Yii::$app->params['uploadDomain'] . Yii::$app->params['uploadAttachment'];
        $targetDir = Yii::$app->params['uploadPathAttachment'];

        $outData = $model->doUpload('Company', $targetDir, $imageLink);
        echo json_encode($outData);
        exit(); // terminate
    }

    public function actionDeleteattachment()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $key = Yii::$app->request->post('key');
        $keys = explode('###', $key);
        $id = $keys[0];
        $destination = $keys[1];

        $model = $this->findModel($id);

        $source = explode('**', $model->sign_image);
        $result = array_diff($source,array($destination));

        $photo = "";
        $new_photo = "";
        if (empty($result)) {
            $new_photo = NULL;
        }else{
            $new_photo = implode('**', $result);
        }

        $model->sign_image = $new_photo;
        if($model->save()){
            @unlink(Yii::$app->params['uploadPathAttachment'] . $destination);
            return [];
        }else{
            echo 'Attachment cannot be blank.';
            return [];
            //return json_encode(array());
        }
    }


    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
