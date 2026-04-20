<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\User;
use common\models\Member;
use common\modules\payroll\models\PayrollDetailL1;
use common\modules\master\models\ApplicationSetting;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['login', 'error', 'verify', 'memberverify'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'profile', 'documentation', 'test'],
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $bebanPerusahaan = PayrollDetailL1::find()
        //     ->select(['period_code', 'label', 'amount'])
        //     ->where(['report_item_id' => 1])
        //     ->orderBy(['period_code' => SORT_DESC, 'label' => SORT_DESC])
        //     ->limit(6)
        //     ->asArray()
        //     ->all();

        // $statusPegawai = PayrollDetailL1::find()
        //     ->select(['period_code', 'label', 'amount'])
        //     ->where(['report_item_id' => 2])
        //     ->orderBy(['period_code' => SORT_DESC, 'label' => SORT_DESC])
        //     ->limit(12)
        //     ->asArray()
        //     ->all();

        // $overtime = PayrollDetailL1::find()
        //     ->select(['period_code', 'label', 'amount'])
        //     ->where(['report_item_id' => 3])
        //     ->orderBy(['period_code' => SORT_DESC, 'label' => SORT_DESC])
        //     ->limit(6)
        //     ->asArray()
        //     ->all();

        // $thp = PayrollDetailL1::find()
        //     ->select(['period_code', 'label', 'amount'])
        //     ->where(['report_item_id' => 4])
        //     ->orderBy(['period_code' => SORT_DESC, 'label' => SORT_DESC])
        //     ->limit(6)
        //     ->asArray()
        //     ->all();

        // $late = PayrollDetailL1::find()
        //     ->select(['period_code', 'label', 'amount'])
        //     ->where(['report_item_id' => 5])
        //     ->orderBy(['period_code' => SORT_DESC, 'label' => SORT_DESC])
        //     ->limit(6)
        //     ->asArray()
        //     ->all();    

        // return $this->render('index', [
        //     'statusPegawai' => $statusPegawai,
        //     'bebanPerusahaan' => $bebanPerusahaan,
        //     'overtime' => $overtime,
        //     'thp' => $thp,
        //     'late' => $late,
        // ]);

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProfile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;

        $model = User::findOne(Yii::$app->user->id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('User not found.');
        }

        // gunakan renderAjax biar hanya isi body modal
        return $this->renderAjax('@app/views/site/profile', [
            'model' => $model,
        ]);
    }

    public function actionVerify($token)
    {
        $user = User::findOne(['verification_token' => $token, 'status' => [0,9]]);

        if ($user) {
            $user->status = 1;
            $user->verification_token = null;
            $user->save(false);
            Yii::$app->session->setFlash('success', 'Akun Anda telah aktif.');
        } else {
            Yii::$app->session->setFlash('error', 'Token tidak valid atau akun sudah aktif.');
        }

        return $this->redirect(['site/login']);
    }

    public function actionMemberverify($token)
    {
        $member = Member::findOne(['verification_token' => $token, 'status' => [0,9]]);

        if ($member) {
            $member->status = 1;
            $member->verification_token = null;
            $member->save(false);
            Yii::$app->session->setFlash('success', 'Akun Anda telah aktif.');
        } else {
            Yii::$app->session->setFlash('error', 'Token tidak valid atau akun sudah aktif.');
        }

        // return $this->redirect(['site/login']);
        return $this->redirect('http://localhost:8085/site/login');
    }

    public function actionDocumentation()
    {
        return $this->render('documentation');
    }

    // public function actionResendMember($id)
    // {
    //     $member = Member::findOne($id);
    //     if ($member && $member->resendVerificationEmail()) {
    //         Yii::$app->session->setFlash('success', 'Email verifikasi member telah dikirim ulang.');
    //     } else {
    //         Yii::$app->session->setFlash('error', 'Gagal kirim ulang email.');
    //     }

    //     return $this->redirect(['view', 'id' => $id]);
    // }

    public function actionTest()
    {
        $passwordDefaultApp = Yii::$app->params['user.passwordDefault'];
        $model = ApplicationSetting::findOne(1);
        $passwordDefault = $model ? $model->default_password : $passwordDefaultApp;
        // print $passwordDefault;
    }
}