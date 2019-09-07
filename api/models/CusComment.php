<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_comment".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property int $order_id 订单ID
 * @property int $user_id 用户ID
 * @property string $nickname 用户昵称
 * @property int $rank 评价等级 0不错意 1一般 2满意
 * @property string $content 评论内容
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 * @property int $type 类型
 */
class CusComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'order_id', 'user_id', 'rank', 'is_delete', 'create_time','type'], 'integer'],
            [['nickname'], 'string', 'max' => 50],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commodity_id' => '商品ID',
            'order_id' => '订单ID',
            'user_id' => '用户ID',
            'nickname' => '用户昵称',
            'rank' => '评价等级 0不错意 1一般 2满意',
            'content' => '评论内容',
            'is_delete' => '是否删除',
            'type' => '类型',
            'create_time' => '创建时间',
        ];
    }
}
