<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchase_offer}}".
 *
 * @property int $id
 */
class PurchaseOffer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%purchase_offer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
}
