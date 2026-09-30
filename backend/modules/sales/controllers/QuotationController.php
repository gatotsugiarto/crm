<?php


namespace backend\modules\sales\controllers;

use Yii;

use common\modules\sales\models\Quotation;
use common\modules\sales\models\QuotationSearch;
use common\modules\sales\models\QuotationItemSearch;
use common\modules\sales\models\Opportunity;
use common\modules\master\models\QuotationLayout;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * QuotationController implements the CRUD actions for Quotation model.
 */
class QuotationController extends Controller
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

    //     $model = new Quotation();
    //     $model->scenario = 'insertData';

    //     if ($model->load(Yii::$app->request->post())) {
    //         return \yii\widgets\ActiveForm::validate($model);
    //     }

    //     return [];
    // }

    /**
     * Lists all Quotation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuotationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quotation model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new QuotationItemSearch();
        $searchModel->quotation_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model'       => $model,
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider,
            // 'discountSearchModel'=> $discountSearchModel,
            // 'discountProvider'=> $discountProvider,
        ]);
    }

    /**
     * Creates a new Quotation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // $model = new Quotation(['scenario' => 'insertData']);
        $model = new Quotation();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if ($model->validate()) {
                    $model->save();
                    $model->getBehavior('tokenProtection')->consumeToken();

                    return [
                        'success' => true,
                        'message' => 'Quotation created successfully.',
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
                Yii::$app->session->setFlash('success', 'Quotation created successfully.');
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
     * Updates an existing Quotation model.
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
                        'message' => 'Quotation updated successfully.',
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
                Yii::$app->session->setFlash('success', 'Quotation updated successfully.');
                return $this->redirect(['index']);
            }
        }

        $formToken = $model->getBehavior('tokenProtection')->generateToken();
        return $this->render('_form', [
            'model'     => $model,
            'formToken' => $formToken,
        ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        // ❌ sudah approve
        if ($model->status === 'Approved') {
            Yii::$app->session->setFlash('warning', 'Quotation already approved');
            return $this->redirect(['view', 'id' => $id]);
        }

        // ❗ validasi item
        if (empty($model->quotationItems)) {
            Yii::$app->session->setFlash('error', 'Quotation has no items');
            return $this->redirect(['view', 'id' => $id]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            // 🔥 ONLY THIS → trigger akan handle semuanya
            $model->status = 'Approved';

            if (!$model->save(false)) {
                throw new \Exception('Failed to approve quotation');
            }

            $transaction->commit();

            // 🔍 ambil SO hasil trigger (optional UX)
            $so = \common\modules\sales\models\SalesOrder::find()
                ->where(['quotation_id' => $model->id])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            Yii::$app->session->setFlash('success', 'Quotation approved successfully');

            if ($so) {
                return $this->redirect(['/sales/salesorder/view', 'id' => $so->id]);
            }

            return $this->redirect(['view', 'id' => $id]);

        } catch (\Throwable $e) {

            $transaction->rollBack();

            Yii::$app->session->setFlash('error', $e->getMessage());

            return $this->redirect(['view', 'id' => $id]);
        }
    }

    /**
     * Deletes an existing Quotation model.
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
                'message' => 'Quotation deleted successfully.'
            ];
        }

        Yii::$app->session->setFlash('success', 'Quotation deleted successfully.');
        return $this->redirect(['index']);
    }

    /*
    public function actionReactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Quotation        $model->status_id = 1;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Quotation activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Quotation activate successfully.');
        return $this->redirect(['index']);
    }

    public function actionNonactive($id)
    {
        $model = $this->findModel($id);
        // Non behavior token protection
        $model->detachBehavior('tokenProtection');

        // Update Quotation        $model->status_id = 2;
        $model->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'message' => 'Quotation non activate successfully.',
                'errors' => $model->errors,
            ];
        }

        // fallback non-AJAX
        Yii::$app->session->setFlash('success', 'Quotation non activate successfully.');
        return $this->redirect(['index']);
    }
    */

    /**
     * The quotation letter (SPH) as a PDF, opened in the browser (print / save).
     * Letterhead logo and the coloured footer repeat on every page.
     */
    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        $layout = QuotationLayout::current();

        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $idDate = function ($ymd) use ($months) {
            $t = strtotime($ymd) ?: time();
            return date('j', $t) . ' ' . $months[(int) date('n', $t)] . ' ' . date('Y', $t);
        };

        $tempDir = Yii::getAlias('@runtime/mpdf');
        \yii\helpers\FileHelper::createDirectory($tempDir, 0775, true);
        $pdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 20, 'margin_right' => 20,
            'margin_top' => 38, 'margin_bottom' => 28,
            'margin_header' => 10, 'margin_footer' => 8,
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
        ]);
        $pdf->SetTitle('Proposal Penawaran Harga ' . $model->quotation_number);
        $pdf->SetAuthor($layout->company_name);

        $logo = $layout->hasLogo()
            ? '<img src="' . $layout->getLogoPath() . '" style="height:18mm">'
            : '<span style="font-size:14pt;font-weight:bold;color:#1a3a6e">' . \yii\helpers\Html::encode($layout->company_name) . '</span>';
        $pdf->SetHTMLHeader('<div style="padding-left:2mm">' . $logo . '</div>');

        $footerLine = '<b>' . \yii\helpers\Html::encode($layout->company_name) . '</b>'
            . ($layout->company_address ? ' | ' . \yii\helpers\Html::encode($layout->company_address) : '')
            . '<br>' . \yii\helpers\Html::encode(trim($layout->company_phone . ($layout->company_website ? ' | ' . $layout->company_website : ''), ' |'));
        $pdf->SetHTMLFooter(
            '<table width="100%" style="border-collapse:collapse;margin-bottom:2mm"><tr>'
            . '<td style="height:1.4mm;background:#1b2a57;width:40%"></td>'
            . '<td style="height:1.4mm;background:#d81f26;width:35%"></td>'
            . '<td style="height:1.4mm;background:#f2c300;width:17%"></td>'
            . '<td style="height:1.4mm;background:#1f8f3a;width:8%"></td>'
            . '</tr></table>'
            . '<div style="font-size:7.5pt;color:#333">' . $footerLine . '</div>'
        );

        $pdf->WriteHTML($this->renderPartial('_pdf_sph', [
            'model' => $model,
            'layout' => $layout,
            'idDate' => $idDate,
        ]));

        $fileName = 'SPH ' . str_replace('/', '-', $model->quotation_number) . ' - ' . ($model->account->name ?? '') . '.pdf';
        return Yii::$app->response->sendContentAsFile($pdf->Output('', \Mpdf\Output\Destination::STRING_RETURN), $fileName, [
            'mimeType' => 'application/pdf',
            'inline' => true,
        ]);
    }

    /**
     * Creates Draft quotations from an opportunity, one per business line of its
     * products (Opportunity::createQuotations), and opens it when there is one. POST, from the
     * "Create Quotation" button on the opportunity view.
     * @param int $id opportunity id
     */
    public function actionCreateFromOpportunity($id)
    {
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\MethodNotAllowedHttpException('Use the Create Quotation button.');
        }
        $opportunity = Opportunity::findOne($id);
        if ($opportunity === null) {
            throw new NotFoundHttpException('The requested opportunity does not exist.');
        }

        try {
            $quotations = $opportunity->createQuotations();
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['/sales/opportunity/view', 'id' => $opportunity->id]);
        }

        if (count($quotations) === 1) {
            $quotation = $quotations[0];
            $count = $quotation->getQuotationItems()->count();
            Yii::$app->session->setFlash('success', "Quotation {$quotation->quotation_number} created with {$count} " . ($count == 1 ? 'item' : 'items') . ' from the opportunity. Review it, then set it to Sent.');
            return $this->redirect(['view', 'id' => $quotation->id]);
        }

        // products from several business lines: one quotation (SPH) per line
        $numbers = implode(', ', array_map(fn($q) => $q->quotation_number, $quotations));
        Yii::$app->session->setFlash('success', count($quotations) . " quotations created, one per business line: {$numbers}. Review each, then set them to Sent.");
        return $this->redirect(['/sales/opportunity/view', 'id' => $opportunity->id]);
    }

    /**
     * Finds the Quotation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Quotation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quotation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
