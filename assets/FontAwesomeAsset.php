<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Description of FontAwesomeAsset
 *
 * @author root
 */
class FontAwesomeAsset extends AssetBundle{

    public $sourcePath = '@bower/font-awesome';
    public $css = [
        'css/all.css',
    ];
    
}
