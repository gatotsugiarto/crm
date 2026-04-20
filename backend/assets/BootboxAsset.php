<?php

namespace backend\assets;

use yii\web\AssetBundle;

class BootboxAsset extends AssetBundle
{
    public $sourcePath = '@npm/bootbox/dist';
    public $js = [
        'bootbox.all.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
