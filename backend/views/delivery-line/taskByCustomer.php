<div class="layui-row">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="delivery_line">
                            <option value="">选择线路</option>
                            <option value="1">1号线</option>
                            <option value="2">2号线</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name='create_time' class="layui-input" id="test-send-date-range-date"
                               placeholder="请选择下单日期">
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
                , url: '/admin/order/get-order-state-data'
                , toolbar: '#order-list-toolbarDemo'
                , title: '用户数据表'
                , cols: [[
                    {field: 'user_id', title: 'ID', width: 80, fixed: 'left', unresize: true, sort: true}
                    , {field: 'line_name', title: '线路名称'}
                    , {field: 'nick_name', title: '客户名称', width: 290}
                    , {field: 'mobile', title: '手机号', sort: true}
                    , {field: 'address_detail', title: '配送地址', sort: true}
                    , {field: 'money', title: '总价', sort: true}
                ]]
                , page: true
            });


            //监听行工具事件
            table.on('tool(order-list)', function (obj) {
                switch (obj.event) {
                    case 'detail':
                        layer.open({
                            type: 2,
                            content: '/admin/order/user-order-list?id=' + obj.data.user_id,
                            area: ['1200px', '820px'],
                            title: '客户订单列表',
                            maxmin: true
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

        });
    </script>