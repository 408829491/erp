<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_recharge_rule".
 *
 * @property string $id
 * @property double $money 充值金额
 * @property double $give_money 赠送金额
 * @property string $create_time 创建时间
 * @property string $modify_time 更新时间
 */
class CusRechargeRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_recharge_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money', 'give_money'], 'number'],
            [['create_time', 'modify_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'money' => 'Money',
            'give_money' => 'Give Money',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
        ];
    }
}
