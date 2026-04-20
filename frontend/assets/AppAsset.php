<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fonts/fontawesome-free-6.6.0-web/css/all.min.css',
        'css/bootstrap.min.css',
        'css/light-bootstrap-dashboard.css?v=2.0.0',
        'css/demo.css',
        'css/modal.css',
        'css/form.css',
        'css/gridview.css',
        'css/login.css',
    ];

    public $js = [
        // 'js/core/jquery.3.2.1.min.js',
        'js/core/popper.min.js',
        'js/core/bootstrap.min.js',
        'js/plugins/bootstrap-switch.js',
        // 'https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE',
        'js/plugins/chartist.min.js',
        'js/plugins/bootstrap-notify.js',
        'js/light-bootstrap-dashboard.js?v=2.0.0',
        'js/demo.js',
        // 'js/preventDoubleSubmit.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
