<?php

namespace app\models\form;

use app\models\CusComment;
use app\models\CusDeliverymanComment;
use app\models\CusGroupOrder;
use app\models\CusOrder;
use app\utils\ReplaceUtils;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;


class CusCommentForm extends \yii\base\Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $commodity_id;
    public $status;
    public $user;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['commodity_id',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 1,],
        ];
    }


    /**
     * 评论列表
     * @return array
     */

    public function search($commodity_id = 0)
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return [];
        }
        $query = CusComment::find();
        $query->where(['commodity_id' => $commodity_id]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->select('bn_cus_comment.*,bn_cus_member.username,bn_cus_member.nickname,head_pic')
            ->leftJoin('bn_cus_member', 'bn_cus_member.id=bn_cus_comment.user_id')
            ->orderBy('bn_cus_comment.id DESC')
            ->asArray()
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y.m.d', $v['create_time']);
            $v['nickname'] = ReplaceUtils::replace($v['nickname']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 保存评论数据
     * @param $data
     * @return array|bool
     */
    public function save()
    {
        $data = Yii::$app->request->post();
        if(!is_array($data)||!isset($data['delivery'])||empty($data['order_id'])){
            return ['code' => 400, 'msg' => '参数错误', 'data' => []];
        }
        $user_id=$this->user['id'];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //配送员评价
            $deliveryComment = new CusDeliverymanComment();
            $deliveryComment->user_id = $user_id;
            $deliveryComment->deliveryman_id = $data['delivery']['deliveryman_id'];
            $deliveryComment->order_id = $data['order_id'];
            $deliveryComment->rank = $data['delivery']['rank'];
            /*$deliveryComment->content = $data['delivery']['content'];*/
            $deliveryComment->create_time = time();
            $deliveryComment->save();
            //商品评价
            foreach($data['commodity'] as $v){
                $comment = new CusComment();
                $comment->attributes = $v;//属性赋值
                $comment->order_id = $data['order_id'];//属性赋值
                $comment->user_id = $user_id;
                $comment->create_time = time();
                $comment->save();
            }
            //更新订单状态
            $order = ($data['type'] == 1)?new CusGroupOrderForm(): new CusOrderForm();
            $order->updateOrderStatus($data['order_id'],6);
            $transaction->commit();
        }catch (Exception $e){
            $transaction->rollBack();
            throw $e;
        }
        return true;
    }


}
