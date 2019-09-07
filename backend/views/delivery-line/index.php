<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li lay-data="1" class="layui-this">线路列表</li>
                    <li>线路订单列表</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <input type="text" readonly name="delivery_date" class="layui-input"
                                           id="laydate-create-date" placeholder="发货日期">
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-card">
                            <div class="layui-card-body">
                                <table class="layui-hide" id="list" lay-filter="list"></table>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <iframe src="/admin/delivery-line/task-by-order" name="sortRateIframe" width="100%"
                                height="800px"
                                style="border: none"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="switchTpl">
    <input type="checkbox" name="is_check" value="{{d.lock_status}}" lay-skin="switch" lay-text="启用|冻结"
           lay-filter="switch-check" {{ d.lock_status== 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="order-list-toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-list" lay-event="getCheckData">新增</button>
    </div>
</script>
<script type="text/html" id="order-list-barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>

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
            elem: '#list'
            , url: '/admin/delivery-line/get-index-data'
            , toolbar: '#order-list-toolbarDemo'
            , title: '线路列表'
            , cols: [[
                {field: 'id', title: 'ID', width: 80, fixed: 'left', unresize: true, sort: true}
                , {field: 'name', title: '线路名称'}
                , {field: 'total_user', title: '下单客户数', sort: true}
                , {field: 'total_order_count', title: '总单数', sort: true}
                , {field: 'total_price', title: '订单金额'}
                , {field: 'driver_name', title: '司机'}
                , {field: 'driver_tel', title: '司机电话'}
                , {field: 'create_time', title: '开通时间'}
                , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo'}
            ]]
            , page: true
            , done: function () {
                element.render();
            }
        });

        //头工具栏事件
        table.on('toolbar(list)', function (obj) {
            switch (obj.event) {
                case 'getCheckData':
                    layer.open({
                        type: 2,
                        content: '/admin/delivery-line/create',
                        area: ['800px', '430px'],
                        title: '新增线路',
                        maxmin: true,
                        btn: ['保存', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    break;
            }
        });

        //监听行工具事件
        table.on('tool(list)', function (obj) {
            switch (obj.event) {
                case 'edit':
                    layer.open({
                        type: 2,
                        content: '/admin/delivery-line/update?id=' + obj.data.id,
                        area: ['800px', '430px'],
                        title: '编辑',
                        maxmin: true,
                        btn: ['保存', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    break;
                case 'detail':
                    layer.open({
                        type: 2,
                        content: '/admin/delivery-line/view?code=' + obj.data.invitation_code,
                        area: ['1080px', '830px'],
                        title: '详情',
                        maxmin: true,
                        btn: ['返回']
                    });
                    break;
                case 'del':
                    layer.confirm('确认要删除该线路吗?', function (index) {
                        $.get({
                            type: "POST",
                            url: "/admin/delivery-line/del?id=" + obj.data.id,
                            dataType: "json",
                            success: function (data) {
                                if (data.code == '200') {
                                    table.reload('list');
                                    layer.msg('删除成功！', {icon: 1});
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

        element.on('tab', function (data) {
            if (data.index == 1) {
                var iframeWindow = window['sortRateIframe'];
                iframeWindow.layui.table.reload('list');
            } else {
                var iframeWindow = window['sortRateByUserIframe'];
                iframeWindow.layui.table.reload('list');
            }
        });

        //清除查询条件
        form.on('submit(search-clear)', function (obj) {
            form.val("layui-form", {
                'keyword': ''
            })
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            var field = data.field;
            //执行重载
            table.reload('list', {
                where: field
            });
        });

        //日期范围
        laydate.render({
            elem: '#laydate-create-date'
            , done: function () {
                $('[lay-filter = search]').trigger('click');
            }
        })

    });
</script>