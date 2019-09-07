<?php

namespace api\modules\v3;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v3\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $config = require(__DIR__.'/config.php');
        // 获取应用程序的组件
        $components = \Yii::$app->getComponents();

        // 遍历子模块独立配置的组件部分，并继承应用程序的组件配置
        foreach( $config['components'] AS $k=>$component ){
            if( isset($component['class']) && isset($components[$k]) == false ) continue;
            $config['components'][$k] = array_merge($components[$k], $component);
        }
        \Yii::configure(\Yii::$app, $config);

    }

}
