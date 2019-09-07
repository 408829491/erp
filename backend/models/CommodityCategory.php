<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%commodity_category}}".
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
        return '{{%commodity_category}}';
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
            'create_time' => '创建时间',
        ];
    }

    // 查询第一层级的数据
    public function findFirstTierData($select) {
        return self::find()->select($select)->where(['pid'=>0])->asArray()->all();
    }
}
