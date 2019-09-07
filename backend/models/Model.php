<?php
/**
 * Created by IntelliJ IDEA.
 * User: mark
 * Date: 2019/4/18
 * Time: 9:36
 */

namespace app\models;
use Yii;

class Model extends \yii\base\Model
{
    public $store;
    /**
     * 软删除：已删除
     */
    const IS_DELETE_TRUE = 1;

    /**
     * 软删除：未删除
     */
    const IS_DELETE_FALSE = 0;

    /**
     * 手机号正则表达式
     */
    const MOBILE_PATTERN = "/\+?\d[\d -]{8,12}\d/";

    public function init()
    {
        parent::init();
    }


    /**
     * Get model error response
     * @param Model $model
     * @return
     */
    public function getErrorResponse($model = null)
    {
        if (!$model) {
            $model = $this;
        }
        return $model->errors;
    }

    /**
     * 获取model操作sql
     * @param $query
     * @return mixed
     */
    public function getLastSql($model = null){
        if (!$model) {
            $model = $this;
        }
        return $model->createCommand()->getRawSql();
    }

}
