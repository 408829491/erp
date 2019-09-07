<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityProfile;
use app\models\Model;
use app\models\Purchase;
use app\models\PurchaseAudit;
use app\models\PurchaseBuyer;
use app\models\PurchaseDetail;
use app\models\PurchaseProvider;
use app\models\StockIn;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;

class PurchaseForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $delivery_date;
    public $plan_date;
    public $user;
    public $source;
    public $create_time;
    public $purchase_type;
    public $status_text = [
        1 => '待采购',
        2 => '部分收货',
        3 => '全部收货',
        4 => '已关闭',
    ];
    public $purchase_text = [
        0 => '市场自采',
        1 => '供应商供货'
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 1,],
            [['source',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['plan_date',], 'default', 'value' => ''],
            [['purchase_type',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询订单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = Purchase::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'purchase_no', $this->keyword],
                ['like', 'agent_name', $this->keyword],
            ]);
        }
        if ($this->plan_date) {
            $query->andwhere([
                'and',
                [
                    '>=', 'plan_date',
                    $this->dateFormat($this->plan_date, 0)['begin_date']
                ],
                [
                    '<=', 'plan_date',
                    $this->dateFormat($this->plan_date, 0)['end_date']
                ],
            ]);
        }
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }

        if (!empty($this->source)) {
            $query->andWhere([
                'source' => $this->source
            ]);
        }

        if ($this->purchase_type === '1') {
            $query->andWhere([
                'purchase_type' => $this->purchase_type
            ]);
        } else if ($this->purchase_type === '0') {
            $query->andWhere([
                'purchase_type' => 0
            ]);
        };
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as $k => &$v) {
            $v['status_text'] = $this->status_text[$v['status']];
            $v['purchase_type_text'] = isset($this->purchase_text[$v['purchase_type']]) ? $this->purchase_text[$v['purchase_type']] : '';
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 更新订单状态
     * @param $purchase_id
     * @param $status
     * @return mixed
     */
    public function updatePurchaseStatus($purchase_id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = Purchase::findOne($purchase_id);
            $model->procured_num = $model->purchase_num;
            $model->purchase_price = $model->price;
            $model->status = $status;
            if ($model->save()) {
                $this->confirmReceive($purchase_id);//生成入库单
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return $model->getErrors();
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval, $timestamp = 1)
    {
        $date_interval = explode(' - ', $date_interval);
        return [
            'begin_date' => $timestamp ? strtotime($date_interval[0]) : $date_interval[0],
            'end_date' => $timestamp ? strtotime($date_interval[1]) : $date_interval[1]
        ];
    }


    /**
     * 保存订单
     * @return mixed
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $purchase = ($id) ? Purchase::findOne($post['id']) : new Purchase();
        $purchase->attributes = $post;//属性赋值
        $purchase->purchase_type = explode(',', $post['purchase_ids'])[0];
        $purchase->agent_id = explode(',', $post['purchase_ids'])[1];
        if(!$id){
            $purchase = $this->initPurchaseData($purchase);
        }
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $purchase->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$purchase->validate() || !$purchase->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $purchase->getErrors()];
        }
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase->price = $this->getSummary($purchase->commodity_list);
        if ($res = $purchase->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($purchase->commodity_list, $purchase, $id);
            if ($res_detail['code'] == 400) {
                $t->rollBack();
                return ['code' => 400, 'msg' => '商品数据保存失败', 'data' => $res_detail['data']];
            }
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $t->commit();//提交事务
        return $res;
    }


    /**
     * 初始化订单数据
     * @param $model
     * @return mixed
     */
    private function initPurchaseData($model)
    {
        $status = 1;
        $model->purchase_no = $this->getPurchaseNo();
        $model->status = $status;
        $model->author = Yii::$app->user->identity['username'];
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算订单总额
     * @param array $commodity_list
     * @return mixed
     */
    private function getSummary($commodity_list = [])
    {
        $total = 0;
        foreach ($commodity_list as $v) {
            $total += $v['purchase_price'] * (int)$v['purchase_num'];
        }
        return $total;
    }

    /**
     * 插入订单商品数据
     * @param $commodity_list商品数据
     */
    private function insertData($commodity_list, $purchase, $id = 0)
    {
        $purchaseDetail = new PurchaseDetail();
        $purchaseDetail->updateAll(['is_delete' => 1], ['purchase_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $purchaseDetail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'purchase_num' => $attribute['purchase_num'],
                    'purchase_price' => $attribute['purchase_price'],
                    'total_price' => $attribute['purchase_num'] * $attribute['purchase_price'],
                    'remark' => $attribute['remark'],
                    'is_delete' => 0,
                    'update_at' => time(),
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->pic = isset(explode(',', $attribute['pic'])[0]) ? explode(',', $attribute['pic'])[0] : $attribute['pic'];
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->purchase_id = $purchase->id;
            $_model->purchase_total_price = $_model->purchase_price * $_model->purchase_num;
            if (!$_model->save()) {
                throw new Exception($_model->getErrors());
            }
        }
    }

    /**
     * 生成订单号
     * @return null|string
     */
    public function getPurchaseNo()
    {
        $purchase_no = null;
        while (true) {
            $purchase_no = 'CG' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_purchase_no = Purchase::find()->where(['purchase_no' => $purchase_no])->exists();
            if (!$exist_purchase_no) {
                break;
            }
        }
        return $purchase_no;
    }

    /**
     * 获取供应商/采购员数据
     * @return mixed
     */
    public function getPurchaseType()
    {
        $purchaseType = [
            '0' => '市场自采',
            '1' => '供应商供货'
        ];
        $purchaseBuyer = PurchaseBuyer::find()
            ->select('id,nickname as name')
            ->where(['type' => 4])
            ->asArray()
            ->all();
        $purchaseProvider = PurchaseProvider::find()
            ->where(['is_delete' => 0])
            ->select('id,name')
            ->asArray()
            ->all();
        $list = [];
        foreach ($purchaseType as $k => $v) {
            $list[$k]['id'] = $k;
            $list[$k]['name'] = $v;
            $list[$k]['children'] = ($k == 0) ? $purchaseBuyer : $purchaseProvider;
        }
        return json_encode($list);
    }

    /**
     * 获取打印数据
     * @return array
     */
    public function getPrintData($id, $type = 0)
    {
        $query = Purchase::find()->where(['id' => $id])->one();
        $data = $query->toArray();
        $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
        $detail = $query->getDetails()->asArray()->all();
        if($type == 0){
            foreach($detail as &$v){
                $v['purchase_price'] = '';
                $v['purchase_total_price'] = '';
                $v['total_price'] = '';
                $v['num'] = '';
            }
        }
        return ['item' => $detail, 'order' => $data];
    }


    /**
     * 初始化商品数据
     * @param $model
     * @return mixed
     */
    private function initCommodityData($model)
    {
        $commodity_list = [];
        $info = array_column($model->commodity_list, 'total_num', 'commodity_id');
        $unit = array_column($model->commodity_list,'unit','commodity_id');
        $priceInfo = array_column($model->commodity_list,'price','commodity_id');
        $ids = array_keys($info);
        $query = Commodity::find()->select('bn_commodity.id,bn_commodity.name,bn_commodity.type_id,bn_commodity.type_first_tier_id,bn_commodity.name,bn_commodity.unit,bn_commodity.pic,price,bn_commodity_category.name as type_name')->leftJoin('bn_commodity_category', 'bn_commodity_category.id=bn_commodity.type_id')->where(['in', 'bn_commodity.id', $ids])->asArray()->all();
        foreach ($query as $k => $v) {
            $price = $priceInfo[$v['id']];
            $num = $info[$v['id']];
            $commodity_list[$k]['commodity_id'] = $v['id'];
            $commodity_list[$k]['name'] = $v['name'];
            $commodity_list[$k]['commodity_name'] = $v['name'];
            $commodity_list[$k]['price'] = $price;
            $commodity_list[$k]['unit'] = $unit[$v['id']];
            $commodity_list[$k]['purchase_num'] = $num;
            $commodity_list[$k]['type_name'] = $v['type_name'];
            $commodity_list[$k]['type_id'] = $v['type_id'];
            $commodity_list[$k]['type_first_tier_id'] = $v['type_first_tier_id'];
            $commodity_list[$k]['num'] = $num;
            $commodity_list[$k]['purchase_price'] = $price;
            $commodity_list[$k]['purchase_total_price'] = $price * $num;
            $commodity_list[$k]['total_price'] = $price * $num;
            $commodity_list[$k]['pic'] = $v['pic'];
            $commodity_list[$k]['remark'] = '订单汇总';
        }
        $model->commodity_list = $commodity_list;
        return $model;
    }


    /**
     * 根据订单商品采购类型生成采购单
     * @return mixed
     */
    public function OrderToPurchase()
    {
        $post = Yii::$app->request->post();
        $commodity_list = $post['commodity_list'];
        if ($commodity_list) {
            $c_data = $data = array();
            $orderIds = [];
            foreach ($commodity_list as $k => $v) {
                $c_data[$v['channel_type'] . ',' . $v['agent_id'] . ',' . $v['agent_name']][] = $v;
                if(explode(',',$v['order_ids'])){
                    $orderIds = array_unique(array_merge($orderIds,explode(',',$v['order_ids'])));
                }

            }
            foreach ($c_data as $k => $v) {
                $data['order_ids'] = join(',',$orderIds);
                $data['purchase_ids'] = $k;
                $data['agent_name'] = explode(',', $k)[2];
                $data['plan_date'] = $post['plan_date'];
                $data['commodity_list'] = $v;
                $this->OrderToPurchaseSave($data);
            }
            return true;
        }
        return false;
    }


    /**
     * 订单汇总生成采购单
     * @return mixed
     */
    public function OrderToPurchaseSave($post = [])
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = $post ? $post : Yii::$app->request->post();
        $purchase = new Purchase();
        $purchase->attributes = $post;
        $purchase->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        $purchase->purchase_no = $this->getPurchaseNo();
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase = $this->initCommodityData($purchase);
        $purchase = $this->initPurchaseData($purchase);
        $purchase->purchase_type = explode(',', $post['purchase_ids'])[0];
        $purchase->agent_id = explode(',', $post['purchase_ids'])[1];
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase->price = $this->getSummary($purchase->commodity_list);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $purchase->save();
            $this->insertData($purchase->commodity_list, $purchase);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 确认收货生成入库单
     * @param $id ：采购单号
     */
    public function confirmReceive($id)
    {
        $model = new StockInForm();
        $audit = new PurchaseAuditForm();
        $purchase = Purchase::find()->with('details')->asArray()->where(['id' => $id])->one();
        $details = $purchase['details'];
        foreach ($details as &$v) {
            $v['diff_num'] = $v['purchase_num'];
            $v['diff_price'] = $v['purchase_price'];
            $v['diff_total_price'] = $v['total_price'] = $v['purchase_num'] * $v['purchase_price'];
        }
        $post['about_no'] = $purchase['purchase_no'];
        $post['purchase_no'] = $purchase['purchase_no'];
        $post['purchase_id'] = $purchase['id'];
        $post['plan_date'] = $purchase['plan_date'];
        $post['agent_id'] = $purchase['agent_id'];
        $post['agent_name'] = $purchase['agent_name'];
        $post['in_time'] = time();
        $post['store_id_name'] = '默认仓库';
        $post['type'] = 1;//入库类型：采购入库
        $post['status'] = 1; //入库状态：已入库
        $post['commodity_list'] = $details;
        $trans = Yii::$app->db->beginTransaction();
        try{
            $model->createStock($post);//创建入库单
            $audit->save($post);//创建对账单
            $this->updateInPrice($details);//更新商品进价
            $model->updateCommodityStockNum($details);
            $trans->commit();
        }catch (Exception $exception){
            $trans->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * 批量更新商品进货价
     * @param $details
     * @throws Exception
     */
    public function updateInPrice($details){
        try{
            foreach($details as $v){
                CommodityProfile::updateAll(['in_price' => $v['purchase_price']],['commodity_id' => $v['commodity_id'],'name' => $v['unit']]);
            }
        }catch (\yii\db\Exception  $exception){
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * 获取采购单信息
     * @return mixed
     */
    public function getPurchase($id){
        $query = Purchase::find();
        $query->where(['id'=>$id]);
       // $query['id']=self::authCode($query['id'],'encode');
        return $query->with('details')->asArray()->one();
    }

    /**
     * 获取进价历史数据
     * @param $id
     * @return array
     */
    public function getPurchasePriceHistory($id){
        $query = PurchaseDetail::find();
        $list = $query->where(['commodity_id'=>$id])
            ->asArray()
            ->select('create_time,num,price')
            ->orderBy('id desc')
            ->all();
        $count = $query->count();
        array_walk($list,function(&$data){
            $data['create_time'] = date("Y-m-d H:i:s",$data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
        ];
    }


    /**
     * 导出采购单
     * @param $id
     */
    public function exportPurchase($id){
        $purchase = $this->getPurchase($id);
        $excel = new PHPExcel();
        $excel->getProperties()
            ->setTitle("采购单")
            ->setSubject("采购单2");
        //合并单元格
        $excel->getActiveSheet()->mergeCells('A1:M1');
        $excel->getActiveSheet()->mergeCells('A2:B2');
        $excel->getActiveSheet()->mergeCells('C2:E2');
        $excel->getActiveSheet()->mergeCells('F2:G2');
        $excel->getActiveSheet()->mergeCells('H2:M2');
        $excel->getActiveSheet()->mergeCells('A3:M3');
        //设置样式
        $excel->getDefaultStyle()->getFont()->setName('微软雅黑');
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);         //第一行字体大小
        // 设置行高度
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23); //设置默认行高
        $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);    //第一行行高
        // 设置垂直居中
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $excel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('F2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('H2:M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格颜色
        $excel->getActiveSheet()->getStyle('A1:M1:A4:M4')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $excel->getActiveSheet()->getStyle('A4:M4')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $excel->getActiveSheet()->getStyle('A2:B2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $excel->getActiveSheet()->getStyle('F2:G2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));

        //设置表头
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', '采购单')
            ->setCellValue('A2', '采购单号:')
            ->setCellValue('C2', $purchase['purchase_no'])
            ->setCellValue('F2', '创建时间：')
            ->setCellValue('H2', date('Y-m-d H:i:s',$purchase['create_time']))
            ->setCellValue('A4', '序号')
            ->setCellValue('B4', '商品编码')
            ->setCellValue('C4', '商品名称')
            ->setCellValue('D4', '描述')
            ->setCellValue('E4', '单位')
            ->setCellValue('F4', '建议采购价')
            ->setCellValue('G4', '待采购量')
            ->setCellValue('H4', '库存')
            ->setCellValue('I4', '收货数量')
            ->setCellValue('J4', '未收数量')
            ->setCellValue('K4', '收货单价')
            ->setCellValue('L4', '收货金额')
            ->setCellValue('M4', '备注');
        //赋值
        $row =  5;
        foreach($purchase['details'] as $v){
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $v['id'])
                ->setCellValue('B'.$row, $v['commodity_id'])
                ->setCellValue('C'.$row, $v['commodity_name'])
                ->setCellValue('D'.$row, $v['remark'])
                ->setCellValue('E'.$row, $v['unit'])
                ->setCellValue('F'.$row, $v['price'])
                ->setCellValue('G'.$row, $v['purchase_num'])
                ->setCellValue('H'.$row, '')
                ->setCellValue('I'.$row, $v['num'])
                ->setCellValue('J'.$row, 0)
                ->setCellValue('K'.$row, $v['purchase_num'])
                ->setCellValue('L'.$row, $v['purchase_total_price'])
                ->setCellValue('M'.$row, '');
            ++ $row;
        }

        //设置单元格宽度
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('k')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        //设置标题
        $excel->getActiveSheet()->setTitle('采购单');
        $excel->setActiveSheetIndex(0);

        //设置表尾
        $excel->getActiveSheet()->mergeCells('A'.($row+1).':B'.($row+1));
        $excel->getActiveSheet()->mergeCells('C'.($row+1).':K'.($row+1));
        $excel->getActiveSheet()->mergeCells('A'.($row+2).':B'.($row+2));
        $excel->getActiveSheet()->mergeCells('C'.($row+2).':F'.($row+2));
        $excel->setActiveSheetIndex(0)->setCellValue('A'.($row+1), '备注：');
        $excel->setActiveSheetIndex(0)->setCellValue('C'.($row+1), $purchase['remark']);
        $excel->setActiveSheetIndex(0)->setCellValue('A'.($row+2), '供应商/采购员：');
        $excel->setActiveSheetIndex(0)->setCellValue('C'.($row+2), $purchase['agent_name']);

        //加边框
        $styleThinBlackBorderOutline = array('borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),),);
        $excel->getActiveSheet()->getStyle( 'A1:M'.($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        //外边框加粗
        $styleThinBlackBorderOutline = array('borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK),),);
        $excel->getActiveSheet()->getStyle( 'A1:M'.($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        // 设置输出
        $tableName = '采购单'.date('YmdHis',time());
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $tableName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        ob_end_flush();
        ob_clean();
    }

    /**对字符串进行加密解密
     * @param $txt
     * @param string $type
     * @return mixed
     */
    public static function authCode($txt,$type = 'encode')
    {
        $key='bn-tech';
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789=+-";
        if($type === 'encode'){
            $nh = rand(0,64);
            $ch = $chars[$nh];
            $mdKey = md5($key.$ch);
            $mdKey = substr($mdKey,$nh%8, $nh%8+7);
            $txt = base64_encode($txt);
            $tmp = '';
            $i=0;$j=0;$k = 0;
            for ($i=0; $i<strlen($txt); $i++) {
                $k = $k == strlen($mdKey) ? 0 : $k;
                $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
                $tmp .= $chars[$j];
            }
            return urlencode($ch.$tmp);
        }else if($type === 'decode'){
            $txt = urldecode($txt);
            $ch = $txt[0];
            $nh = strpos($chars,$ch);
            $mdKey = md5($key.$ch);
            $mdKey = substr($mdKey,$nh%8, $nh%8+7);
            $txt = substr($txt,1);
            $tmp = '';
            $i=0;$j=0; $k = 0;
            for ($i=0; $i<strlen($txt); $i++) {
                $k = $k == strlen($mdKey) ? 0 : $k;
                $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
                while ($j<0) $j+=64;
                $tmp .= $chars[$j];
            }
            return base64_decode(str_replace(" ","+",$tmp));
        }else{
            return 'param error';
        }
    }

}
