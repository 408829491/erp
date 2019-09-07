<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%commodity_source}}".
 *
 * @property int $id ID
 * @property int $inspect_date 监测日期
 * @property string $inspect_from 送检机构
 * @property string $inspect_man 检测员
 * @property string $inspect_organization 检测机构
 * @property int $provider_id 供应商ID
 * @property string $provider_name 供应商
 * @property int $status 状态
 * @property int $is_delete 是否删除
 * @property string $trace_report_arr 检测报告
 * @property int $modify_time 更新时间
 * @property int $create_time 创建时间
 */
class CommoditySource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    private $titel;
    public static function tableName()
    {
        return '{{%commodity_source}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspect_date', 'provider_id', 'status', 'is_delete', 'modify_time', 'create_time'], 'integer'],
            [['inspect_from', 'inspect_man', 'inspect_organization', 'provider_name'], 'required'],
            [['trace_report_arr'], 'string'],
            [['inspect_from', 'inspect_organization', 'provider_name'], 'string', 'max' => 255],
            [['inspect_man'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inspect_date' => '监测日期',
            'inspect_from' => '送检机构',
            'inspect_man' => '检测员',
            'inspect_organization' => '检测机构',
            'provider_id' => '供应商ID',
            'provider_name' => '供应商',
            'status' => '状态',
            'is_delete' => '是否删除',
            'trace_report_arr' => '检测报告',
            'modify_time' => '更新时间',
            'create_time' => '创建时间',
        ];
    }
}
