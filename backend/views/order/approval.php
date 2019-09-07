<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
<div class="layui-card">
    <div class="layui-card-header">基本信息</div>
    <div class="layui-card-body">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">客户:</label>
                    <div class="layui-input-inline">
                        <?=$nick_name?>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">发货日期:</label>
                    <div class="layui-input-inline">
                        <?=$delivery_date ?>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">发货时间</label>
                    <div class="layui-input-inline">
                        <?=$delivery_time_detail ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">收货信息</div>
        <div class="layui-card-body">
            <div id="user-info">收货人：<?=$receive_name ?>  <?=$user_name ?>  <?=$address_detail?></div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">订购商品清单</div>
    </div>
    <div class="layui-card">
        <table class="layui-hide" id="subList" lay-filter="subList"></table>
        <script type="text/html" id="operation">
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
        </script>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">备注</div>
        <div class="layui-card-body">
            <div class="layui-inline">
                <?=$remark ?>
            </div>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="hidden" id="id" name="id" value="<?php if(Yii::$app->request->get('type')!='copy'){echo $id;} else echo 0;?>">
        <input type="hidden" id="user_id" name="user_id" value="<?=$user_id?>">
        <input type="hidden" id="user_name" name="user_name" value="<?=$user_name?>">
        <input type="hidden" id="address_detail" name="address_detail" value="<?=$address_detail?>">
        <input type="hidden" id="receive_name" name="receive_name" value="<?=$receive_name?>">
        <input type="hidden" id="receive_tel" name="receive_tel" value="<?=$receive_tel?>">
        <input type="hidden" id="nick_name" name="nick_name" value="<?=$nick_name?>">
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
    </div>


    <script type="text/html" id="priceTpl">
        <input type="text" name="price" placeholder="" autocomplete="off" class="layui-input" style="height: 28px" value="{{d.price}}">
    </script>

    <script type="text/html" id="imgTpl">
        <img style="display: inline-block; width: 50%; height: 100%;" src= '{{d.pic}}'>
    </script>
</div>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','form','element'], function(){
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
                        "code": res.code,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.list,
                    }
                }
                , request: {
                    pageName: 'pageNum',
                    limitName: 'pageSize'
                }
                , response: {
                    statusCode: 200
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
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                    ,{field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                    ,{field: 'commodity_name', title: '商品名称', minWidth: 100}
                    ,{field: 'notice', title: '描述', minWidth: 120}
                    ,{field: 'unit', title: '单位', width: 80}
                    ,{field: 'num', title: '下单数量',width: 120, unresize: true}
                    ,{field: 'price', title: '下单单价（元）', width: 150,edit: 'text',templet: '#priceTpl',unresize: true}
                    ,{field: 'total_price', title: '金额小计（元）', width: 190,totalRow: true}
                    ,{field: 'remark', title: '备注', width: 180, unresize: true}
                ]]
                , data: subList
                , page: false
                , toolbar: false
                , totalRow: true
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
                    , url: '/admin/order/save'
                    , dataType: "json"
                    , cache: false
                    , data: field
                    , done: function () {
                        parent.layui.table.reload('order-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }
                });
            });

            //监听工具条
            table.on('tool(subList)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    obj.del();
                }
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date'
                ,range: true
                ,theme: 'molv'
            });

            laydate.render({
                elem: '#test-send-date-range-date'
                ,range: true
                ,theme: 'molv'
            });

            var action = {
                copyOrder: function(){
                    top.layui.admin.popupRight({
                        id: 'LAY_adminPopupLayerTest'
                        ,success: function(){
                            $('#'+ this.id).html('<div style="padding: 20px;">放入内容</div>');
                        }
                    });
                },
                addProductList: function(){
                    var create_window = layer.open({
                        id:'main',
                        type: 2,
                        content: '/admin/commodity-list/list',
                        area: ['1000px', '630px'],
                        title:'请选择商品',
                        maxmin: false,
                        btn: ['确定', '取消'],yes: function(index, layero){
                            var sonTable = layero.find('iframe')[0].contentWindow.layui.table.checkStatus('list').data;
                            table.reload('subList', {
                                data: layui.table.cache['subList'].concat(sonTable)
                            });
                            layer.close(index);
                        }
                    });
                },
                addProductSingle: function(){
                    alert('ok');
                },
                saveOrder:function(){
                    //保存订单数据
                }
            };


            $('.layui-btn').on('click', function(e){
                var type = $(this).data('type');
                action[type] && action[type].call(this);
                console.log(e);
            });

        });

        function test(obj)
        {
            layui.table.reload('test-table-data',obj);
        }

    </script>