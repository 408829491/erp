<?php

namespace app\models\form;

use app\models\CommodityPriceSetting;
use app\models\CommodityProfile;
use app\models\CommodityProfileDetail;
use app\models\CustomerType;
use app\models\Model;
use Yii;

class SetPriceForm extends Model
{
    /**
     * 获取客户类型价
     * @param $id
     */
    public function getPriceSetting($id, $unit)
    {
        $model = CommodityPriceSetting::find();
        $query = $model->where(['commodity_id' => $id, 'unit' => $unit])->asArray()->all();
        $setting = array_column($query, null, 'c_type');
        $customerTypeModel = CustomerType::find();
        $customerType = $customerTypeModel->select('id,name')->asArray()->all();
        foreach ($customerType as &$v) {
            $v['add_value'] = isset($setting[$v['id']]['add_value']) ? $setting[$v['id']]['add_value'] : 0;
            $v['type'] = isset($setting[$v['id']]['type']) ? $setting[$v['id']]['type'] : 0;
            $v['recent_price'] = isset($setting[$v['id']]['recent_price']) ? $setting[$v['id']]['recent_price'] : 0;
        }
        return $customerType;
    }

    /**
     * 保存价格设置信息
     * @return bool
     */
    public function settingSave()
    {
        $post = Yii::$app->request->post();
        $model = CommodityPriceSetting::find()->where(['commodity_id' => $post['commodity_id'], 'unit' => $post['unit'], 'c_type' => $post['c_type']])->one();
        if (!$model) {
            $model = new CommodityPriceSetting();
        }
        $model->attributes = $post;
        $model->recent_price = $this->calculatePrice($model->in_price, $model->add_value, $model->type);
        $model->create_time = time();
        $model->option_user = Yii::$app->user->identity['nickname'];
        if ($model->save()) {
            $commodity = CommodityProfile::findOne(['commodity_id' => $post['commodity_id'], 'name' => $post['unit']]);
            $commodity->is_setting_formula = 1;
            $commodity->save();
        }


        return true;
    }

    /**
     * 同步商品价格
     * @return bool
     */
    public function syncCommodityPrice()
    {
        $setting = CommodityPriceSetting::find()->asArray()->all();
        foreach ($setting as $v) {
            $commodityProfile = CommodityProfile::find()
                ->select('id,in_price')
                ->where(['name'=>$v['unit'],'commodity_id'=>$v['commodity_id']])
                ->one();
            $commodityProfileDetail =CommodityProfileDetail::findOne([
                'commodity_profile_id'=>$commodityProfile['id'],
                'type_id'=>$v['c_type']
            ]);
            if($commodityProfileDetail){
                $commodityProfileDetail->price = $this->calculatePrice($commodityProfile->in_price,$v['add_value'],$v['type']);
                $commodityProfileDetail->save();
            }
        }
        return true;
    }

    /**
     * 根据公式类型计算价格
     * @param $in_price
     * @param $add_value
     * @param $type
     * @return float|int
     */
    public function calculatePrice($in_price, $add_value, $type)
    {
        $price = 0;
        switch ($type) {
            case '1':
                $price = round($in_price + $add_value, 2);
                break;
            case '2':
                $price = round($in_price + $in_price * ($add_value / 100), 2);
                break;
            case '3':
                $price = round($in_price / (1 - $add_value / 100), 2);
                break;
        }
        return $price;
    }

}
