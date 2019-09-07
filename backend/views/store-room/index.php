<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-data="0">入库管理</li>
                    <li lay-data="1">入库查询</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="status">
                                            <option value="">全部状态</option>
                                            <option value="0">未入库</option>
                                            <option value="1">已入库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="type">
                                            <option value="">全部类型</option>
                                            <option value="1">采购入库</option>
                                            <option value="2">其它入库</option>
                                            <option value="4">订单退货</option>
                                            <option value="5">期初入库</option>
                                            <option value="6">报溢入库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" name="in_time" class="layui-input"
                                               id="test-laydate-range-date" placeholder="请选择入库日期">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <input type="text" name="keyword" placeholder="输入客户名称/订单号/手机号" autocomplete="off"
                                           class="layui-input" id="commodity" style="width: 300px;">
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                    </button>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="search-clear">
                                        清除查询条件
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-card">
                            <div class="layui-card-body">
                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form2">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="type_first_tier_id">
                                            <option value="">商品分类</option>
                                            <?php foreach ($commodityCategory as $item) : ?>
                                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="type">
                                            <option value="">入库类型</option>
                                            <option value="1">采购入库</option>
                                            <option value="2">其它入库</option>
                                            <option value="4">订单退货</option>
                                            <option value="5">期初入库</option>
                                            <option value="6">报溢入库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" name="in_time" class="layui-input"
                                               id="in_time" placeholder="请选择入库日期">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <input type="text" name="keyword" placeholder="输入商品名称/关联单号" autocomplete="off"
                                           class="layui-input" id="commodity" style="width: 300px;">
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search2">
                                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                    </button>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="search-clear">
                                        清除查询条件
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-card">
                            <div class="layui-card-body">
                                <table class="layui-hide" id="customer-list" lay-filter="customer-list"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="order-list-toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-list" lay-event="getCheckData">新增</button>
    </div>
</script>

<script type="text/html" id="order-list-barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
    {{#  if(d.status == 0){ }}
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    {{#  } }}
    <a class="layui-btn layui-btn-xs tpl_print_view_order" data-id="{{d.id}}" lay-event="print"
       style="background-color: #77CF20">打印</a>
</script>
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
    }).use(['index', 'table', 'laydate', 'element', 'form'], function () {
        var table = layui.table
            , laydate = layui.laydate
            , element = layui.element
            , form = layui.form;

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
            , url: '/admin/stock-in/list'
            , toolbar: '#order-list-toolbarDemo'
            , title: '入库管理'
            , cols: [[
                {field: 'id', title: 'ID', width: 80, fixed: 'left', unresize: true, sort: true}
                , {field: 'in_no', title: '入库单号', width: 235}
                , {field: 'total_price', title: '单据金额', sort: true}
                , {field: 'store_id_name', title: '仓库', width: 130}
                , {
                    field: 'provider_name', title: '采购员/供应商', width: 160, sort: true, templet: function (d) {
                        var type = (d.purchase_type === '2') ? '供应商' : '采购员';
                        if (d.purchase_type === '0') {
                            return '手工单';
                        }
                        return d.provider_name + '(' + type + ')';
                    }
                }
                , {field: 'type_name', title: '类型', width: 110}
                , {field: 'about_no', title: '关联单号', sort: true, width: 235}
                , {field: 'operator', title: '制单人'}
                , {
                    field: 'in_time', title: '入库时间', width: 160, sort: true, templet: function (d) {
                        if (d.in_time === '0') {
                            return '暂无';
                        }
                        return d.in_time;
                    }
                }
                , {field: 'status_name', title: '入库状态', width: 110}
                , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo', width: 180}
            ]]
            , page: true
        });


        table.render({
            elem: '#customer-list'
            , url: '/admin/stock-in/commodity-search'
            , title: '入库查询'
            , cols: [[
                {field: 'id', title: 'ID', width: 80, fixed: 'left', unresize: true, sort: true}
                , {field: 'commodity_name', title: '商品名称', width: 235}
                , {field: 'unit', title: '单位', sort: true,width: 130}
                , {field: 'store_id_name', title: '仓库', width: 130}
                , {field: 'type_name', title: '入库类型', width: 110}
                , {field: 'in_no', title: '关联单号', sort: true, width: 235}
                , {field: 'in_time', title: '入库时间', width: 150, sort: true}
                , {field: 'num', title: '入库数量'}
                , {field: 'price', title: '入库单价'}
                , {field: 'total_price', title: '入库金额'}
            ]]
            , page: true
        });

        //头工具栏事件
        table.on('toolbar(order-list)', function (obj) {
            var checkStatus = table.checkStatus(obj.config.id);
            switch (obj.event) {
                case 'getCheckData':
                    var windows = layer.open({
                        type: 2,
                        content: '/admin/stock-in/create',
                        area: ['800px', '830px'],
                        title: '新增手工单',
                        maxmin: true,
                        btn: ['保存', '取消'], yes: function (index, layero) {
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
        table.on('tool(order-list)', function (obj) {
            switch (obj.event) {
                case 'edit':
                    var windows_update = layer.open({
                        type: 2,
                        content: '/admin/stock-in/update?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title: '编辑',
                        maxmin: true,
                        btn: ['保存', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_update);
                    break;
                case 'detail':
                    var window_detail = layer.open({
                        type: 2,
                        content: '/admin/stock-in/view?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title: '详情',
                        maxmin: true,
                        btn: ['返回']
                    });
                    layer.full(window_detail);
                    break;
                case 'finish':
                    layer.confirm('确认要完成该订单吗?', function (index) {
                        $.get({
                            type: "POST",
                            url: "/admin/order/finish?order_id=" + obj.data.id,
                            dataType: "json",
                            success: function (data) {
                                if (data.code == '200') {
                                    table.reload('order-list');
                                    layer.msg('订单已完成！', {icon: 1});
                                } else {
                                    layer.msg(data.msg, {icon: 2});
                                }
                            }
                        });
                        layer.close(index);
                    });
                    break;
                    break;
                case 'close':
                    layer.confirm('确认要关闭该订单吗?', function (index) {
                        $.get({
                            type: "POST",
                            url: "/admin/order/close-order?order_id=" + obj.data.id,
                            dataType: "json",
                            success: function (data) {
                                if (data.code == '200') {
                                    table.reload('order-list');
                                    layer.msg('关闭成功！', {icon: 1});
                                } else {
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
            , range: true
            , theme: 'molv'

        });

        laydate.render({
            elem: '#in_time'
            , range: true
            , theme: 'molv'

        });


        //清除查询条件
        form.on('submit(search-clear)', function (obj) {
            form.val("layui-form", {
                'delivery_date': '',
                'is_pay': '',
                'create_time': '',
                'keyword': '',
                'source': ''
            })
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            var field = data.field;

            //执行重载
            table.reload('order-list', {
                where: field
            });
        });


        //监听搜索
        form.on('submit(search2)', function (data) {
            var field = data.field;

            //执行重载
            table.reload('customer-list', {
                where: field
            });
        });

    });
</script>