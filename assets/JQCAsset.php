<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * jQuery countdown plugin.
 */
class JQCAsset extends AssetBundle
{

    public $js = [
        'js/jquery.plugin.js',
        'js/jquery.countdown.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
