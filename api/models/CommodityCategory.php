<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_commodity_category".
 *
 * @property int $id ID
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
 * @property int $create_time 创建时间
 */
class CommodityCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['pid', 'level', 'is_recommend', 'sequence', 'is_delete', 'create_time'], 'integer'],
            [['is_standard'], 'string'],
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
            'name' => 'Name',
            'pid' => 'Pid',
            'level' => 'Level',
            'is_recommend' => 'Is Recommend',
            'is_standard' => 'Is Standard',
            'pic_category' => 'Pic Category',
            'pic_path_big' => 'Pic Path Big',
            'sequence' => 'Sequence',
            'source_from' => 'Source From',
            'is_delete' => 'Is Delete',
            'create_time' => 'Create Time',
        ];
    }
}
