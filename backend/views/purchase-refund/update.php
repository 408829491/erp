<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header">基本信息<div style="float:right" class="layui-hide"><button class="layui-btn layui-btn-xs layui-btn-normal" id="copy-order" data-type="copyOrder">复制已有订单</button></div>
        </div>
        <div class="layui-card-body">
            <div class="layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">采购类型：</label>
                        <div class="layui-inline" id="purchase_type"><?php echo $purchase_type==0?'市场自采':'供应商供货'?></div>
                        <input type="hidden" name="purchase_type" value="<?=$purchase_type?>">
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label" style="width: 200px;">采购员/供应商：</label>
                        <div class="layui-inline" id="buyer"><?=$agent_name?></div>
                        <input type="hidden" name="agent_name" value="<?=$agent_name?>">
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label" style="width: 200px;">源采购单号：</label>
                        <div class="layui-inline" id="purchase_no"><?=$purchase_no?></div>
                        <input type="hidden" name="purchase_no" value="<?=$purchase_no?>">
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-hide">
                <input type="hidden" name="id" value="<?=$id?>">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">采购退回单清单</div>
        <div class="layui-card">
            <table class="layui-hide" id="subList" lay-filter="subList"></table>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-inline">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-inline">
                        <input type="text" name="remark" id="remark" placeholder="请输入备注" autocomplete="off" class="layui-input" style="width:850px" value="<?=$remark?>">
                        <input type="hidden" id="id" name="id" value="<?php if(Yii::$app->request->get('type')!='copy'){echo $id;} else echo 0;?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="refund_priceTpl">
        <input type="text" name="refund_price" placeholder="" autocomplete="off" class="layui-input order_c"
               date-id="{{d.id}}" lay-filter="refund_price" style="height: 28px" value="{{d.refund_price}}">
    </script>
    <script type="text/html" id="refund_numTpl">
        <input type="text" name="refund_num" placeholder="" autocomplete="off" class="layui-input order_c"
               date-id="{{d.id}}" lay-filter="refund_num" style="height: 28px" value="{{d.refund_num}}">
    </script>
    <script type="text/html" id="total_refund_priceTpl">
        <input type="text" name="total_refund_price" placeholder="" autocomplete="off" class="layui-input order_c"
               date-id="{{d.id}}" lay-filter="total_refund_price" style="height: 28px" value="{{d.total_refund_price}}">
    </script>
    <script type="text/html" id="imgTpl">
        <img style="display: inline-block; width: 50%; height: 100%;" src='{{d.pic}}?x-oss-process=image/resize,h_50'>
    </script>

    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','form','element','yutons_sug','selectN','selectY'], function(){
            var $ = layui.$
                ,admin = layui.admin
                ,table = layui.table
                ,laydate = layui.laydate
                ,form = layui.form
                ,element = layui.element;

            element.render();

            //table设置默认参数
            table.set({
                page: true
                , parseData: function (res) {
                    return {
                        "code": 0,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.list
                    }
                }
                , request: {
                    pageName: 'pageNum',
                    limitName: 'pageSize'
                }
                , response: {
                    statusCode: 0
                }
                , toolbar: true
                , limit: 50
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            //展示已知数据
            var subList = <?=json_encode($details)?>;
            table.render({
                elem: '#subList'
                ,cols: [[ //标题栏
                    {
                        field: 'id',
                        title: 'ID',
                        width: 80,
                        fixed: 'left',
                        unresize: true,
                        sort: true,
                        totalRowText: '合计:'
                    }
                    , {field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                    , {field: 'name', title: '商品名称', minWidth: 100}
                    , {field: 'type_name', title: '分类', minWidth: 120}
                    , {field: 'unit', title: '单位', width: 80}
                    , {field: 'refund_num', title: '退货数量', unresize: true, templet: '#refund_numTpl'}
                    , {field: 'refund_price', title: '退货单价', unresize: true,templet: '#refund_priceTpl'}
                    , {field: 'total_refund_price', title: '退货小计', unresize: true,templet: '#total_refund_priceTpl',totalRow:true}
                    , {title: '操作', minWidth: 100, align: 'center', fixed: 'right', toolbar: '#operation'}
                ]]
                , data: subList
                , totalRow: true
                , page: false
                , toolbar: false
                , limit: Number.MAX_VALUE
                , title: "列表"
                ,done: function(res, curr, count){
                    tableDataTemp = res;
                }
            });

            // 监听提交
            form.on('submit(layuiadmin-app-form-submit)', function (data) {
                var field = data.field; // 获取提交的字段
                field.commodity_list = layui.table.cache['subList'];
                var subListIndex = 0;
                field.commodity_list.forEach(function (e) {
                    if (!$.isEmptyObject(e)) {
                        e.commodity_id = e.id;
                        subListIndex += 1;
                    }
                });
                var index = parent.layer.getFrameIndex(window.name);
                admin.req({
                    type: "post"
                    , url: '/admin/purchase-refund/save'
                    , dataType: "json"
                    , cache: false
                    , data: field
                    , done: function () {
                        parent.layui.table.reload('order-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }
                });
            });


            //绑定回车
            $('body').on('keypress blur input', '.order_c', function (event) {
                var field = $(this).attr('lay-filter');
                var value = $(this).val();
                var index = $(this).parents('tr').index();
                if (event.keyCode === 13) {
                    changeData(field, value, index);
                }
            });

            /**
             * 改变表格指定行列数据
             * @param index
             * @param field
             * @param value
             */
            function changeData(field, value, index) {
                tempData = layui.table.cache['subList'];
                var row = tempData[index];
                row[field] = value;
                if (field === 'total_refund_price') {
                    row.refund_price = (row.total_refund_price / row.refund_num).toFixed(2);
                } else {
                    row.total_refund_price = (row.refund_num * row.refund_price).toFixed(2);
                }
                table.reload('subList', {
                    url:'',
                    data: tempData
                });
            }

            //监听工具条
            table.on('tool(subList)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    obj.del();
                }
            });


            //发货日期
            laydate.render({
                elem: '#test-laydate-range-date',
                min:minDate()
            });

            $('.layui-btn').on('click', function(e){
                var type = $(this).data('type');
                action[type] && action[type].call(this);
                console.log(e);
            });


        });

        //格式化价格
        function toDecimal2(x) {
            var f = parseFloat(x);
            if (isNaN(f)) {
                return false;
            }
            var s = f.toString();
            var rs = s.indexOf('.');
            if (rs < 0) {
                rs = s.length;
                s += '.';
            }
            while (s.length <= rs + 2) {
                s += '0';
            }
            return s;
        }

        // 设置最小可选的日期
        function minDate(){
            var now = new Date();
            now.setDate(now.getDate()+1);
            return now.getFullYear()+"-" + (now.getMonth()+1) + "-" + (now.getDate());
        }

    </script>