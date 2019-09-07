<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_config".
 *
 * @property string $id 配置ID
 * @property string $name 配置名称
 * @property string $title 配置说明
 * @property string $value 配置值
 * @property string $remark 配置说明
 * @property int $sort 排序
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 100],
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
            'title' => 'Title',
            'value' => 'Value',
            'remark' => 'Remark',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
