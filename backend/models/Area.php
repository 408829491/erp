<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_area".
 *
 * @property int $id 区域ID
 * @property string $area_name 区域名称
 * @property int $create_time 创建时间
 * @property int $is_delete 是否删除
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area_name', 'create_time'], 'required'],
            [['create_time', 'is_delete'], 'integer'],
            [['area_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '区域ID',
            'area_name' => '区域名称',
            'create_time' => '创建时间',
            'is_delete' => '是否删除',
        ];
    }
}
