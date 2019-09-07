<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_store_slideshow".
 *
 * @property string $id
 * @property string $store_id 店铺id
 * @property string $img_url 图片地址
 * @property string $info 简介
 * @property string $skip_url 跳转信息
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusStoreSlideshow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_store_slideshow';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id'], 'integer'],
            [['info'], 'string'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['img_url'], 'string', 'max' => 500],
            [['skip_url'], 'string', 'max' => 999],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'img_url' => 'Img Url',
            'info' => 'Info',
            'skip_url' => 'Skip Url',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
