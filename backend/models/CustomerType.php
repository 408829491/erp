<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer_type}}".
 *
 * @property int $id ID
 * @property string $name 类型名称
 * @property int $sys_operating_time_id 运营时间ID
 * @property string $sys_operating_time 运营时间
 * @property int $create_time 创建时间
 */
class CustomerType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['sys_operating_time_id', 'create_time','is_delete','id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['sys_operating_time'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '类型名称',
            'sys_operating_time_id' => '运营时间ID',
            'sys_operating_time' => '运营时间',
            'create_time' => '创建时间',
        ];
    }
}
