<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'fonts/fontawesome-free-6.6.0-web/css/all.min.css',
        'css/light-bootstrap-dashboard.css?v=2.0.0',
        
        // Tambahkan di baris paling bawah:
        'css/custom-app.css?v=1.2',  // beri versi supaya browser update
    ];

    public $js = [
        'js/core/popper.min.js',
        'js/plugins/bootstrap-switch.js',
        'js/plugins/chartist.min.js',
        'js/plugins/bootstrap-notify.js',
        'js/light-bootstrap-dashboard.js?v=2.0.0',
        'js/demo.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
