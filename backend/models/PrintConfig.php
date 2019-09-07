<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_print_config".
 *
 * @property string $KEY
 * @property string $PATH
 * @property string $PRINTER_KEY
 * @property string $TYPE
 * @property string $TYPE_NAME
 */
class PrintConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_print_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PATH'], 'string'],
            [['KEY', 'PRINTER_KEY', 'TYPE', 'TYPE_NAME'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'KEY' => 'Key',
            'PATH' => 'Path',
            'PRINTER_KEY' => 'Printer Key',
            'TYPE' => 'Type',
            'TYPE_NAME' => 'Type Name',
        ];
    }
}
