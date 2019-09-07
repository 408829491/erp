<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\db\Exception;

/**
 * This is the model class for table "bn_seckill".
 *
 * @property int $id
 * @property string $name 活动名称
 * @property string $seckill_commoditys_name 抢购商品名称
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property int $buy_num 下单数量
 * @property int $is_close 是否主动关闭
 * @property int $is_limit_buy_num 是否限制购买数量
 * @property int $closer_id 关闭人ID
 * @property string $close_name 关闭人姓名
 * @property string $close_time 关闭时间
 * @property string $create_time 创建时间
 * @property string $modify_time 更新时间
 */
class Seckill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_seckill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seckill_commoditys_name'], 'string'],
            [['start_time', 'end_time', 'close_time', 'create_time', 'modify_time'], 'safe'],
            [['buy_num', 'is_close', 'is_limit_buy_num', 'closer_id'], 'integer'],
            [['name', 'close_name'], 'string', 'max' => 255],
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
            'seckill_commoditys_name' => 'Seckill Commoditys Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'buy_num' => 'Buy Num',
            'is_close' => 'Is Close',
            'is_limit_buy_num' => 'Is Limit Buy Num',
            'closer_id' => 'Closer ID',
            'close_name' => 'Close Name',
            'close_time' => 'Close Time',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
        ];
    }

    // 获取分页list数据
    public function findPage($pageNum=0,$pageSize=10,$filterProperty,$select)
    {
        $pageNum -= 1;
        $query = self::find();

        if ($select != null) {
            $query->select([$select]);
        }

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty);
            $typeId = $json->typeId;
            if ($typeId != null) {
                $query->andWhere(['type_first_tier_id' => $typeId]);
            }
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $data['list'] = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return $data;
    }

    // 保存数据
    public function saveData($model, $subList) {
        $transaction  = Seckill::getDb()->beginTransaction();

        try {

            if (!$model->validate()) {
                throw new Exception(implode(",", $model->errors));
            };
            $model->save();

            if (sizeof($subList) != 0) {
                foreach ($subList as $value) {
                    $model1 = new SeckillCommodity();
                    $model1->attributes = $value;
                    $model1->seckill_id = $model->id;
                    if (!$model1->validate()) {
                        throw new Exception(implode(",", $model1->errors));
                    };
                    $model1->save();
                }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 修改数据
    public function editData($model, $subList) {
        $transaction  = Seckill::getDb()->beginTransaction();

        try {

            if (!$model->validate()) {
                throw new Exception(implode(",", $model->errors));
            };
            $model->save();

            // 删除子表
            SeckillCommodity::deleteAll(['seckill_id' => $model->id]);
            // 保存子表
            self::saveSubList($model, $subList);

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function saveSubList($model, $subList) {
        if (sizeof($subList) != 0) {
            foreach ($subList as $value) {
                $model1 = new SeckillCommodity();
                $model1->attributes = $value;
                $model1->seckill_id = $model->id;
                if (!$model1->validate()) {
                    throw new Exception(implode(",", $model1->errors));
                };

                $model1->save();
            }
        }
    }

    // 获取当天的活动
    public function findOneByToday() {
        $model = Seckill::find()
            ->andWhere('0', 'is_close')
            ->andWhere(' <= ', 'start_time', date('Y-m-d H:i:s'), false)
            ->andWhere(' >= ', 'end_time', date('Y-m-d H:i:s'), false)
            ->orderBy('id DESC')
            ->limit('1')
            ->one();
        return $model;
    }
}
