<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/layuiadmin/layui/css/layui.css',
        'plugins/layuiadmin/style/admin.css',
        'resources/css/iconfont.css',
        'plugins/awesome/css/font-awesome.min.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
     //定义按需加载JS方法，注意加载顺序在最后  
    public static function addScript($view, $jsfile) {  
        $view->registerJsFile($jsfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);  
    }  
      
   //定义按需加载css方法，注意加载顺序在最后  
    public static function addCss($view, $cssfile) {  
        $view->registerCssFile($cssfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);  
    }  
}
