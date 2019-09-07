<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_commodity_category".
 *
 * @property int $id ID
 * @property string $store_id 门店id
 * @property string $name 分类名称
 * @property int $pid 父类ID
 * @property int $level 层级
 * @property int $is_recommend 是否推荐
 * @property string $is_standard 是否基本
 * @property string $pic_category 分类图片
 * @property string $pic_path_big 分类大图
 * @property int $sequence 排序
 * @property string $source_from 来源
 * @property int $is_delete 是否删除
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusCommodityCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_commodity_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'level', 'is_recommend', 'sequence', 'is_delete'], 'integer'],
            [['name'], 'required'],
            [['is_standard'], 'string'],
            [['pid'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name', 'pic_category', 'pic_path_big', 'source_from'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '门店id',
            'name' => '分类名称',
            'pid' => '父类ID',
            'level' => '层级',
            'is_recommend' => '是否推荐',
            'is_standard' => '是否基本',
            'pic_category' => '分类图片',
            'pic_path_big' => '分类大图',
            'sequence' => '排序',
            'source_from' => '来源',
            'is_delete' => '是否删除',
            'create_datetime' => '创建日期时间',
            'modify_datetime' => '修改日期时间',
        ];
    }
}
