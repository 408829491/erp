<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-data="0">出库管理</li>
                    <li lay-data="1">出库查询</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                            <div class="layui-form-item">

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" readonly class="layui-input" name="out_time"/>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="status">
                                            <option value="">出库状态</option>
                                            <option value="0">待出库</option>
                                            <option value="1">已出库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="type">
                                            <option value="">出库类型</option>
                                            <option value="1">销售出库</option>
                                            <option value="2">其它出库</option>
                                            <option value="3">调货出库</option>
                                            <option value="4">采购退货</option>
                                            <option value="5">规格转换</option>
                                            <option value="6">报损出库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <input type="text" name="searchText" placeholder="输入单号/制单人" autocomplete="off"
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
                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <iframe src="/admin/stock-out/stock-out-by-commodity" name="stockOutByCommodityIframe" width="100%" height="800px" style="border: none"></iframe>
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

            var monthStartDate = getNearMonth();
            var currentDate = getCurrentDate();
            function getCurrentDate() {
                var now = new Date();

                return formatDate(now);
            }

            function getCurrentMondayStart() {
                // 获取当前月的第一天
                var lastMonthDate = new Date();
                lastMonthDate.setDate(1);

                return formatDate(lastMonthDate);
            }

            function getNearMonth() {
                var now = new Date();
                var nowTime = now.getTime();
                var oneDayLong = 30 * 24 * 60 * 60 * 1000;
                var yesterdayTime = nowTime - oneDayLong;
                var yesterday = new Date(yesterdayTime);
                return formatDate(yesterday);
            }

            function formatDate(date) {
                var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
                var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                return date.getFullYear() + '-' + month + '-' + day;
            }

            //日期范围
            laydate.render({
                elem: '[name = out_time]'
                ,range: '到'
                , value: monthStartDate + ' 到 ' + currentDate
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

                var outTime = field.out_time.split(' 到 ');
                field.startDate = outTime[0];
                field.endDate = outTime[1];

                var field1 = {};
                field1.filterProperty=JSON.stringify(field);

                //执行重载
                table.reload('order-list', {
                    where: field1
                });
            });

            table.render({
                elem: '#order-list'
                , url: '/admin/stock-out/list'
                , toolbar: '#order-list-toolbarDemo'
                , title: '用户数据表'
                , cols: [[
                    {field: 'id', title: 'ID', width: 80, fixed: 'left', unresize: true, sort: true}
                    , {field: 'out_no', title: '出库单号', width: 230}
                    , {field: 'total_price', title: '单据金额', width: 150, sort: true}
                    , {field: 'out_time', title: '出库时间', sort: true}
                    , {field: 'type_name', title: '类型'}
                    , {field: 'about_no', title: '关联单号', width: 230}
                    , {field: 'user_name', title: '客户名称'}
                    , {field: 'operator', title: '制单人'}
                    , {field: 'status_name', title: '状态', width: 110}
                    , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo', width: 110}
                ]]
                ,where: {filterProperty: JSON.stringify({startDate: monthStartDate, endDate: currentDate})}
                , page: true
            });

            //头工具栏事件
            table.on('toolbar(order-list)', function (obj) {

                switch (obj.event) {
                    case 'getCheckData':
                        var windows = layer.open({
                            type: 2,
                            content: '/admin/stock-out/create',
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
                            content: '/admin/stock-out/update?id=' + obj.data.id,
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
                            content: '/admin/stock-out/view?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title: '详情',
                            maxmin: true,
                            btn: ['返回']
                        });
                        layer.full(window_detail);
                        break;
                }
            });

        });
    </script>