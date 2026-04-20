<?php

namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;

class TokenProtectedFormBehavior extends Behavior
{
    public $tokenAttribute = 'form_token';
    public $sessionKey = 'form_token';

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'validateToken',
        ];
    }

    public function generateToken()
    {
        $token = uniqid('form_', true);
        Yii::$app->session->set($this->sessionKey, $token);
        return $token;
    }

    public function validateToken()
    {
        // 🔒 HANYA VALIDASI JIKA REQUEST POST FORM INI
        if (!Yii::$app->request->isPost) {
            return;
        }

        // 🔒 pastikan field token memang dikirim
        if (!Yii::$app->request->post($this->tokenAttribute)) {
            return;
        }

        $model = $this->owner;
        $token = Yii::$app->request->post($this->tokenAttribute);
        $sessionToken = Yii::$app->session->get($this->sessionKey);

        // 🔒 validasi token
        if (!$sessionToken || !hash_equals($sessionToken, $token)) {
            $model->addError(
                $this->tokenAttribute,
                'Invalid or duplicate submission.'
            );
        }
    }

    /**
     * Hapus token hanya setelah data berhasil disimpan.
     */
    public function consumeToken()
    {
        Yii::$app->session->remove($this->sessionKey);
    }
}

