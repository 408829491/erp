<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-data="0">供应商供货</li>
                    <li >市场自采</li>
                </ul>
            </div>
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="请输入商品编码\名称\助记码\别名" autocomplete="off"
                               class="layui-input" style="width: 300px;">
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
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-xs5">
                            商品汇总
                            <hr>
                            <div class="grid-demo grid-demo-bg1">
                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                            </div>
                        </div>
                        <div class="layui-col-xs7">
                            商品明细
                            <hr>
                            <div class="grid-demo">
                                <table class="layui-hide" id="order-list2" lay-filter="order-list2"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'laydate', 'element', 'form', 'yutons_sug'], function () {
        var table = layui.table
            , laydate = layui.laydate
            , element = layui.element
            , yutons_sug = layui.yutons_sug
            , form = layui.form;

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

        table.render({
            elem: '#order-list'
            , url: '/admin/order/order-commodity-list-data'
            , title: '商品汇总'
            , cols: [[
                {field: 'name', title: '商品'}
                , {field: 'unit', title: '单位'}
                , {field: 'num', title: '收货数量'}
                , {field: 'total_price', title: '收货金额'}
            ]]
            , toolbar: false
            , page: false
        });

        table.render({
            elem: '#order-list2'
            , title: '商品明细'
            , url: '/admin/order/order-commodity-detail-data'
            , cols: [[
                {field: 'order_no', title: '采购单号', width: 200}
                , {field: 'delivery_date', title: '采购日期'}
                , {field: 'name', title: '商品'}
                , {field: 'unit', title: '单位', sort: true}
                , {field: 'num', title: '收货数量'}
                , {field: 'num', title: '收货金额'}
                , {field: 'refund_num', title: '退货数量', sort: true}
                , {field: 'refund_num', title: '退货金额', sort: true}
                , {field: 'total_price', title: '实际金额'}
            ]]
            , toolbar: false
            , page: false
        });

        //头工具栏事件
        table.on('toolbar(order-list)', function (obj) {
            var checkStatus = table.checkStatus(obj.config.id);
            switch (obj.event) {
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
        table.on('tool(order-list)', function (obj) {
            switch (obj.event) {
                case 'edit':
                    var windows_update = layer.open({
                        type: 2,
                        content: '/admin/order/update?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title: '编辑订单',
                        maxmin: true,
                        btn: ['保存订单', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_update);
                    break;
                case 'detail':
                    var window_detail = layer.open({
                        type: 2,
                        content: '/admin/order/view?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title: '订单详情',
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
                        title: '退货',
                        maxmin: true,
                        btn: ['保存', '取消'], yes: function (index, layero) {
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
                        title: '核算订单',
                        maxmin: true,
                        btn: ['确定', '取消'], yes: function (index, layero) {
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
                        title: '复制订单',
                        maxmin: true,
                        btn: ['复制保存订单', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows_copy);
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
            elem: '#test-send-date-range-date'
            , range: true
            , theme: 'molv'

        });

        //监听Tab页
        element.on('tab(component-tabs)', function () {
            var _this = this;
            //数据重载
            table.reload('order-list', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                , where: {
                    status: _this.getAttribute('lay-data')
                }
            });
        });

        //监听行点击
        table.on('row(order-list)', function (obj) {
            var data = obj.data;
            table.reload('order-list2', {
                where: data
            });
            //标注选中样式
            obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
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

        yutons_sug.render({
            id: "nickname", //设置容器唯一id
            height: "200",
            width: "400",
            cols: [
                [{
                    field: 'nickname',
                    title: '客户名称'
                },
                    {
                        field: 'username',
                        title: '联系电话'
                    },
                    {
                        field: 'address',
                        title: '地址'
                    }]
            ], //设置表头
            params: [
                {
                    name: 'nickname',
                    field: 'nickname'
                },
                {
                    name: 'username',
                    field: 'username'
                }, {
                    name: 'address',
                    field: 'address'
                }],//设置字段映射，适用于输入一个字段，回显多个字段
            type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
            url: '/admin/user/get-user-list?keyword=' //设置异步数据接口,url为必填项,params为字段名
        });

        //获取客户筛选数据
        table.on('row(yutons_sug_nickname)', function (obj) {
            var data = obj.data;
            $("#nickname").val(data.nickname);
            $("#yutons_sug_nickname").next().hide().html("");
            table.reload('order-list2', {
                where: []
            });
            table.reload('order-list', {
                page: {
                    curr: 1 //重新从第 1 页开始
                }
                , where: {
                    id: data.id
                }
            });
        });


    });
</script>