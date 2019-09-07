<?php

namespace backend\controllers;

use app\models\Config;
use app\models\CustomerType;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;

class ConfigController extends \yii\web\Controller
{
    public function actionIndex()
    {
        // 获取单位列表
        $model = Config::findOne("25");
        // 获取标签列表
        $model2 = Config::findOne("26");
        return $this->render('index',['unitList' => explode(":;", $model->value), 'tagList' => explode(":;", $model2->value)]);
    }

    // 添加单位
    public function actionAddUnit() {
        $unitName = \Yii::$app->request->post('unitName');
        if ($unitName == null && $unitName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        $model = Config::findOne("25");

        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        // 不能添加重复值
        if (strpos($model->value, $unitName)) {
            return new ApiResponse(ApiCode::CODE_ERROR, '不能添加重复的单位');
        }
        $model->value = $model->value.':;'.$unitName;
        $model->save();

        return new ApiResponse();
    }

    // 添加标签
    public function actionAddTag() {
        $tagName = \Yii::$app->request->post('tagName');
        if ($tagName == null && $tagName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        $model = Config::findOne("26");

        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        // 不能添加重复值
        if (strpos($model->value, $tagName)) {
            return new ApiResponse(ApiCode::CODE_ERROR, '不能添加重复的单位');
        }
        $model->value = $model->value.':;'.$tagName;
        $model->save();

        return new ApiResponse();
    }

    // 修改单位
    public function actionUpdateUnit() {
        $oldUnitName = \Yii::$app->request->post('oldUnitName');
        $unitName = \Yii::$app->request->post('unitName');
        if ($unitName == null || $unitName == '' || $oldUnitName == null || $oldUnitName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        $model = Config::findOne("25");

        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        $model->value = str_replace($oldUnitName, $unitName, $model->value);
        $model->save();

        return new ApiResponse();
    }

    // 修改标签 热销与当季无法修改
    public function actionUpdateTag() {
        $oldTagName = \Yii::$app->request->post('oldTagName');
        $tagName = \Yii::$app->request->post('tagName');
        if ($oldTagName == null || $oldTagName == '' || $tagName == null || $tagName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        if ($oldTagName == '热销' || $oldTagName == '当季') {
            return new ApiResponse(ApiCode::CODE_ERROR, '热销与当季无法修改');
        }
        $model = Config::findOne("26");

        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        $model->value = str_replace($oldTagName, $tagName, $model->value);
        $model->save();

        return new ApiResponse();
    }

    // 删除单位
    public function actionDeleteUnit() {
        $unitName = \Yii::$app->request->post('unitName');
        if ($unitName == null || $unitName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        $model = Config::findOne("25");
        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        $result = strpos($model->value, $unitName);
        if (false === $result) {
            return new ApiResponse(ApiCode::CODE_ERROR, '需要删除的单位不存在');
        } else if ($result == 0) {
            // 第一个位置的删除
            $deleteStr = $unitName.':;';
        } else {
            $deleteStr = ':;'.$unitName;
        }
        $model->value = str_replace($deleteStr, '', $model->value);
        $model->save();

        return new ApiResponse();
    }

    // 删除标签
    public function actionDeleteTag() {
        $tagName = \Yii::$app->request->post('tagName');
        if ($tagName == null || $tagName == '') {
            return new ApiResponse(ApiCode::CODE_ERROR, '名称必填');
        }
        if ($tagName == '热销' || $tagName == '当季') {
            return new ApiResponse(ApiCode::CODE_ERROR, '热销与当季无法删除');
        }
        $model = Config::findOne("26");
        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR);
        }
        $result = strpos($model->value, $tagName);
        if (false === $result) {
            return new ApiResponse(ApiCode::CODE_ERROR, '需要删除的标签不存在');
        } else if ($result == 0) {
            // 第一个位置的删除
            $deleteStr = $tagName.':;';
        } else {
            $deleteStr = ':;'.$tagName;
        }
        $model->value = str_replace($deleteStr, '', $model->value);
        $model->save();

        return new ApiResponse();
    }

    // 获取单位列表
    public function actionUnitList() {
        $model = Config::findOne("25");
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', explode(":;", $model->value));
    }

    // 获取标签列表
    public function actionTagList() {
        $model = Config::findOne("26");
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', explode(":;", $model->value));
    }

    // 查询所有的客户类型
    public function actionFindCustomType() {
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', CustomerType::find()->asArray()->all());
    }
}
