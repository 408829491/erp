<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class LayuiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/layuiadmin/layui/css/layui.css',
		'resources/css/main.css',
		'plugins/layuiadmin/style/admin.css',
		'resources/css/iconfont.css',
		'plugins/awesome/css/font-awesome.min.css',
    ];
    public $js = [
		'plugins/layuiadmin/layui/layui.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
     //定义按需加载JS方法，注意加载顺序在最后  
    public static function addScript($view, $jsfile) {  
        $view->registerJsFile($jsfile, [AppAsset::className(), 'depends' => 'backend\assets\LayuiAsset']);  
    }  
      
   //定义按需加载css方法，注意加载顺序在最后  
    public static function addCss($view, $cssfile) {  
        $view->registerCssFile($cssfile, [AppAsset::className(), 'depends' => 'backend\assets\LayuiAsset']);  
    }  
}
