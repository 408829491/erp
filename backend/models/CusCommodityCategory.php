<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cus_commodity_category".
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
 * @property int $is_show 是否显示
 * @property int $is_create_by_self 是否自己创建
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
            [['store_id', 'pid', 'level', 'is_recommend', 'sequence', 'is_delete', 'is_show', 'is_create_by_self'], 'integer'],
            [['name'], 'required'],
            [['is_standard'], 'string'],
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
            'store_id' => 'Store ID',
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
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }

    // 查询第一层级的数据
    public function findFirstTierData($select) {
        $query = self::find()->select($select)->asArray()
            ->where(['pid'=>0]);

        $storeId = Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->andWhere(['store_id' => $storeId]);
        } else {
            $query->andWhere(['store_id' => Yii::$app->user->identity['store_id']]);
        }

        $data = $query->all();
        return $data;
    }
}
