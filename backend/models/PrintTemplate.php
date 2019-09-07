<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%print_template}}".
 *
 * @property int $id ID
 * @property string $type 类型
 * @property string $name 名称
 * @property int $page_height 页高
 * @property int $page_width 页宽
 * @property int $page_left 页左
 * @property int $page_top 页上
 * @property int $print_direct 是否直接打印
 * @property int $printer_margin_type 间隔类型
 * @property int $row_length 行高
 * @property string $tpl_data 模板数据
 * @property string $memo 备注
 * @property string $paper 纸张
 * @property string $background_image 背景图片
 * @property int $is_fill_height 是否自适应高度
 * @property int $is_show_border 是否显示边框
 * @property int $is_show_page_num 是否显示页码
 * @property int $is_show_sign 底部显示收货人姓名
 * @property int $is_show_header 是否显示表头
 * @property int $is_show_goods_header 是否显示商品表头
 * @property int $it_can_delete 是否允许删除
 * @property int $update_time 更新时间
 */
class PrintTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%print_template}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_height', 'page_width', 'page_left', 'page_top', 'print_direct', 'printer_margin_type', 'row_length', 'is_fill_height', 'is_show_border', 'is_show_page_num', 'is_show_sign', 'is_show_header', 'is_show_goods_header', 'it_can_delete', 'update_time','tpl_style'], 'integer'],
            [['tpl_data'], 'string'],
            [['type', 'name'], 'string', 'max' => 50],
            [['memo', 'background_image'], 'string', 'max' => 255],
            [['paper'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'page_height' => 'Page Height',
            'page_width' => 'Page Width',
            'page_left' => 'Page Left',
            'page_top' => 'Page Top',
            'print_direct' => 'Print Direct',
            'printer_margin_type' => 'Printer Margin Type',
            'row_length' => 'Row Length',
            'tpl_data' => 'Tpl Data',
            'memo' => 'Memo',
            'paper' => 'Paper',
            'background_image' => 'Background Image',
            'is_fill_height' => 'Is Fill Height',
            'is_show_border' => 'Is Show Border',
            'is_show_page_num' => 'Is Show Page Num',
            'is_show_sign' => 'Is Show Sign',
            'is_show_header' => 'Is Show Header',
            'is_show_goods_header' => 'Is Show Goods Header',
            'it_can_delete' => 'It Can Delete',
            'update_time' => 'Update Time',
        ];
    }
}
