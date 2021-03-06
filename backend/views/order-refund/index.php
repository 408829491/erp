<div class="layui-fluid">
    <div class="layui-row">
            <div class="layui-card">
                <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                    <ul class="layui-tab-title">
                        <li lay-data="0">全部</li>
                        <li class="layui-this" lay-data="1">待处理</li>
                        <li lay-data="3">已完成</li>
                        <li lay-data="4">已关闭</li>
                    </ul>
                </div>
                <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date" placeholder="请选择申请日期">
                            </div>
                        </div>

                        <div class="layui-inline">
                                <input type="text" name="keyword" placeholder="请输入退货单号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                            </button>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit  lay-filter="search-clear">
                                清除查询条件
                            </button>
                        </div>
                    </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">

                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>

                    <script type="text/html" id="order-list-toolbarDemo">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-list" lay-event="getCheckData">新增</button>
                        </div>
                    </script>

                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
                        {{#  if(d.status == 1){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="approval">审核</a>
                        {{#  } }}
                        <a class="layui-btn layui-btn-xs tpl_print_view_order" data-id="{{d.id}}" lay-event="print" style="background-color: #77CF20">打印</a>
                        {{#  if(d.status == 2){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="finish">完成</a>
                        {{#  } }}
                    </script>
                </div>
            </div>
        </div>
    </div>

    <script src="/admin/plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/pFuncs.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/printCommon.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/publicPrinter.js"></script>
    <script src="/admin/plugins/cdsPrint/js/print/order.js"></script>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'laydate','element','form'], function(){
        var table = layui.table
            ,laydate = layui.laydate
            ,element = layui.element
            ,form = layui.form;

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
            , limit: 15
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });
        table.render({
            elem: '#order-list'
            ,url: '/admin/order-refund/order-refund-list'
            ,toolbar: '#order-list-toolbarDemo'
            ,title: '用户数据表'
            ,cols: [[
                 {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                ,{field:'refund_no', title:'退货单号', width:235}
                ,{field:'apply_price', title:'退货金额', width:130, sort: true}
                ,{field:'price', title:'应退金额', width:130, sort: true}
                ,{field:'user_name', title:'客户名称', width:230,sort: true}
                ,{field:'order_no', title:'源订单', width:100, sort: true}
                ,{field:'remark', title:'退货原因', width:110}
                ,{field:'stock_in_no', title:'关联入库单'}
                ,{field:'status', title:'售后状态'}
                ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo',width:210}
            ]]
            ,page: true
        });

        //头工具栏事件
        table.on('toolbar(order-list)', function(obj){
            var checkStatus = table.checkStatus(obj.config.id);
            switch(obj.event) {
                case 'getCheckData':
                    var windows = layer.open({
                        type: 2,
                        content: '/admin/order/create',
                        area: ['800px', '830px'],
                        title: '新增手工订单',
                        maxmin: true,
                        btn: ['保存并继续新增', '保存返回列表页', '取消'], yes: function (index, layero) {
                            //点击确认触发 iframe 内容中的按钮提交
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows);
                    break;
            }
        });

        //监听行工具事件
        table.on('tool(order-list)', function(obj){
            switch(obj.event){
                case 'edit':
                    var windows_update = layer.open({
                        type: 2,
                        content: '/admin/order/update?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title:'编辑订单',
                        maxmin: true,
                        btn: ['保存订单', '取消'],yes: function(index, layero){
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_update);
                    break;
                case 'detail':
                    var window_detail = layer.open({
                        type: 2,
                        content: '/admin/order/view?id='+ obj.data.id,
                        area: ['800px', '830px'],
                        title:'订单详情',
                        maxmin: true,
                        btn: ['返回']
                    });
                    layer.full(window_detail);
                    break;
                case 'refund':
                    layer.open({
                        type: 2,
                        content: '/admin/order/refund?id=' + obj.data.id,
                        area: ['800px', '630px'],
                        title:'退货',
                        maxmin: true,
                        btn: ['保存', '取消'],yes: function(index, layero){
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    break;
                case 'approval':
                    var windows_approval = layer.open({
                        type: 2,
                        content: '/admin/order/approval?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title:'核算订单',
                        maxmin: true,
                        btn: ['确定', '取消'],yes: function(index, layero){
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_approval);
                    break;
                case 'copy':
                    var windows_copy = layer.open({
                        type: 2,
                        content: '/admin/order/update?type=copy&id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title:'复制订单',
                        maxmin: true,
                        btn: ['复制保存订单', '取消'],yes: function(index, layero){
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_copy);
                    break;
                case 'finish':
                    layer.confirm('确认要完成该订单吗?', function(index){
                        $.get({
                            type:"POST",
                            url:"/admin/order/finish?order_id="+obj.data.id,
                            dataType:"json",
                            success:function(data){
                                if(data.code=='200'){
                                    table.reload('order-list');
                                    layer.msg('订单已完成！', {icon: 1});
                                }else{
                                    layer.msg(data.msg, {icon: 2});
                                }
                            }
                        });
                        layer.close(index);
                    });
                    break;
                    break;
                case 'close':
                    layer.confirm('确认要关闭该订单吗?', function(index){
                        $.get({
                            type:"POST",
                            url:"/admin/order/close-order?order_id="+obj.data.id,
                            dataType:"json",
                            success:function(data){
                                if(data.code=='200'){
                                    table.reload('order-list');
                                    layer.msg('关闭成功！', {icon: 1});
                                }else{
                                    layer.msg(data.msg, {icon: 2});
                                }
                            }
                        });
                        layer.close(index);
                    });
                    break;
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

        //监听Tab页
         element.on('tab(component-tabs)', function(){
             var _this = this;
             //数据重载
             table.reload('order-list', {
                 page: {
                     curr: 1 //重新从第 1 页开始
                 }
                 ,where: {
                     status:_this.getAttribute('lay-data')
                 }
             });
         });

        //清除查询条件
        form.on('submit(search-clear)', function(obj){
            form.val("layui-form", {
                'delivery_date': '',
                'is_pay': '',
                'create_time': '',
                'keyword': '',
                'source':''
            })
        });

        //监听搜索
        form.on('submit(search)', function(data){
            var field = data.field;

            //执行重载
            table.reload('order-list', {
                where: field
            });
        });

    });
</script>