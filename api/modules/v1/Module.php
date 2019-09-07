<?php

namespace api\modules\v1;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }



}
