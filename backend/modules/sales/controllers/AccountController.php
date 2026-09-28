<?php


namespace backend\modules\sales\controllers;

use Yii;

use common\modules\sales\models\Account;
use common\modules\sales\models\AccountSearch;
use common\modules\sales\models\ContactSearch;
use common\modules\sales\models\AccountAddressSearch;
use common\modules\sales\models\AccountAttachment;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
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

    //     $model = new Account();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all Account models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Account model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new ContactSearch();
        $searchModel->account_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $addressSearchModel = new AccountAddressSearch();
        $addressSearchModel->account_id = $id;
        $addressDataProvider = $addressSearchModel->search($this->request->queryParams);

        $attachmentDataProvider = new ActiveDataProvider([
            'query' => AccountAttachment::find()->where(['account_id' => $model->id])->with('createdBy'),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20, 'pageParam' => 'attachment-page'],
        ]);

        return $this->render('view', [
            'model'       => $model,
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            'addressSearchModel' => $addressSearchModel,
            'addressDataProvider'=> $addressDataProvider,
            'attachmentDataProvider' => $attachmentDataProvider,
        ]);
    }

    /**
     * Upload one document to an account. Called by the Kartik FileInput on the
     * Account view (one AJAX multipart POST per file: `file`, `description`), so
     * it answers in Kartik's format: `{}` on success, `{error: "..."}` on failure.
     */
    public function actionUploadattachment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\MethodNotAllowedHttpException('Upload must be a POST request.');
        }

        $account = $this->findModel($id);
        $attachment = new AccountAttachment(['account_id' => $account->id]);
        $attachment->description = Yii::$app->request->post('description');
        $attachment->file = UploadedFile::getInstanceByName('file');

        if ($attachment->upload()) {
            return ['success' => true, 'id' => $attachment->id];
        }

        $errors = $attachment->getFirstErrors();
        return ['error' => $errors ? reset($errors) : 'Upload failed.'];
    }

    /**
     * Stream a document. Read-only roles get this through viewApplication.
     */
    public function actionDownloadattachment($id)
    {
        $attachment = $this->findAttachment($id);
        $path = $attachment->getFilePath();
        if (!is_file($path)) {
            throw new NotFoundHttpException('The file is missing on the server.');
        }

        return Yii::$app->response->sendFile($path, $attachment->file_name, [
            'mimeType' => $attachment->mime_type ?: null,
            'inline' => false,
        ]);
    }

    public function actionDeleteattachment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\MethodNotAllowedHttpException('Delete must be a POST request.');
        }

        $attachment = $this->findAttachment($id);
        $name = $attachment->file_name;
        $attachment->delete();

        return ['success' => true, 'message' => 'Document "' . $name . '" deleted.'];
    }

    protected function findAttachment($id)
    {
        if (($model = AccountAttachment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested document does not exist.');
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new Account(['scenario' => 'insertData']);
        $model = new Account();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $model->save();
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Account created successfully.',
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
                Yii::$app->session->setFlash('success', 'Account created successfully.');
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
     * Updates an existing Account model.
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
                        'message' => 'Account updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Account updated successfully.');
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
     * Deletes an Account: its contacts, addresses and documents first, then the
     * account itself (Account::deleteWithDependents). Refused, with the reason,
     * while it still has opportunities, quotations, sales orders, invoices or
     * activities.
     * @param int $id ID
     * @return \yii\web\Response|array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $blocker = $model->deleteBlockers();
        if ($blocker !== null) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => $blocker];
            }
            Yii::$app->session->setFlash('error', $blocker);
            return $this->redirect(['index']);
        }

        $removed = $model->deleteWithDependents();
        $parts = [];
        foreach (['contacts' => 'contact', 'addresses' => 'address', 'documents' => 'document'] as $key => $label) {
            if ($removed[$key] > 0) {
                $parts[] = $removed[$key] . ' ' . ($removed[$key] === 1 ? $label : $key);
            }
        }
        $message = 'Account "' . $model->name . '" deleted' . ($parts ? ', with ' . implode(', ', $parts) : '') . '.';

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => $message];
        }

        Yii::$app->session->setFlash('success', $message);
        return $this->redirect(['index']);
    }

    /*
    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Account        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Account activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Account activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Account        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Account non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Account non activate successfully.');
        return $this->redirect(['index']);
    }
    */

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
