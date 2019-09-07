<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-data="0">按客户发货出库</li>
                    <li lay-data="1">按订单发货出库</li>
                    <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">
                        <button class="layui-btn layui-btn-sm layui-btn-normal" data-type="allSend" style="background-color: #77CF20">
                            一键发货出库
                        </button>
                    </div>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                            <div class="layui-form-item">

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" readonly name="delivery_date" class="layui-input" id="deliveryDate" placeholder="发货日期">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="orderStatus">
                                            <option value="">全部状态</option>
                                            <option value="0">未发货</option>
                                            <option value="1">已发货</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="line_id">
                                            <option value="">全部线路</option>
                                            <?php foreach ($lineData as $item) : ?>
                                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <input type="text" name="searchText" placeholder="输入客户名称/联系人/电话" autocomplete="off"
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
                                <table class="layui-hide" id="list" lay-filter="list"></table>
                                <script type="text/html" id="sortStatus">
                                    {{#  if(d.sortedNum == 0){ }}
                                    <span style="color:red">未分拣</span>
                                    {{#  } else if (parseInt(d.sortedNum) < parseInt(d.totalNum)) { }}
                                    <span style="color:#ec971f">分拣中</span>
                                    {{#  } else { }}
                                    <span style="color:green">已分拣</span>
                                    {{#  } }}
                                </script>
                                <script type="text/html" id="orderStatus">
                                    {{#  if(parseInt(d.status) == 1){ }}
                                    <span style="color:red">未发货</span>
                                    {{#  } else { }}
                                    <span style="color:green">已发货</span>
                                    {{#  } }}
                                </script>
                                <script type="text/html" id="unit">
                                    {{#  if(d.is_basics_unit == 1){ }}
                                    {{ d.unit }}
                                    {{#  } else { }}
                                    {{ d.unit }}（{{ d.base_self_ratio }}{{ d.base_unit }}）
                                    {{#  } }}
                                </script>
                                <script type="text/html" id="reserve">
                                    {{= d.num}} {{= d.unit}}
                                </script>
                                <script type="text/html" id="actualNum">
                                    {{= d.actual_num}} {{= d.unit}}
                                </script>
                                <script type="text/html" id="totalPrice">
                                    {{= (d.actual_num * d.price).toFixed(2) }}
                                </script>
                                <script type="text/html" id="sortStatus2">
                                    {{#  if(d.is_sorted == 0){ }}
                                    <span style="color:red">未分拣</span>
                                    {{#  } else { }}
                                    <span style="color:green">已分拣</span>
                                    {{#  } }}
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="layui-tab-item">
                        <iframe src="/admin/store-room/out-of-stock-by-order" name="sendByOrderIframe" width="100%" height="800px" style="border: none"></iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/html" id="order-list-barDemo">
        {{#  if( parseInt(d.status) == 1 || parseInt(d.sortedNum) < parseInt(d.totalNum)){ }}
        <a class="layui-btn layui-btn-xs" lay-event="sendOut" style="background-color:#1e9fff">发货出库</a>
        {{#  } }}
        <a class="layui-btn layui-btn-xs" lay-event="edit" style="background-color: #77CF20">打印</a>
    </script>

    <script type="text/html" id="triangle">
        <div>
            <i class="layui-icon layui-icon-triangle-r"></i>
        </div>
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
                , form = layui.form
                , admin = layui.admin
                , $ = layui.$;

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
                , limit: 14
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            var tomorrow = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);

            // 格式化日期
            function formatDate(date) {
                var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
                var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                return date.getFullYear() + '-' + month + '-' + day;
            }

            table.render({
                elem: '#list'
                , url: '/admin/store-room/out-of-stock-data'
                , toolbar: '#order-list-toolbarDemo'
                , title: '按客户发货出库'
                , cols: [[
                    {
                        field: 'triangle',
                        title: '',
                        width: 50,
                        unresize: true,
                        templet: '#triangle',
                        event: 'showTable',
                        style: 'cursor: pointer;'
                    }
                    , {field: 'user_id', title: 'ID', width: 80, unresize: true, sort: true}
                    , {field: 'nick_name', title: '客户名称', width: 235}
                    , {field: 'receive_name', title: '联系人', width: 230}
                    , {field: 'receive_tel', title: '电话'}
                    , {field: 'address_detail', title: '地址'}
                    , {field: 'line_name', title: '路线'}
                    , {field: 'sendPrice', title: '发货金额'}
                    , {title: '打印次数'}
                    , {title: '分拣状态', templet: "#sortStatus"}
                    , {field: 'status',title: '状态', width: 110, templet: "#orderStatus"}
                    , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo', width: 140}
                ]]
                , where: {filterProperty: JSON.stringify({delivery_date: formatDate(tomorrow)})}
                , page: true
            });

            // 清除查询条件
            form.on('submit(search-clear)', function (obj) {
                form.val("layui-form", {
                    'delivery_date': tomorrow,
                    'orderStatus': '',
                    'line_id': '',
                    'searchText': ''
                })
            });

            // 监听搜索
            form.on('submit(search)', function (data) {
                var field = {};
                field.filterProperty=JSON.stringify(data.field);

                // 执行重载
                table.reload('list', {
                    where: field
                });
            });

            // 日期范围
            laydate.render({
                elem: '#deliveryDate'
                ,value:new Date(tomorrow)
                , done: function () {
                    $('[lay-filter = search]').trigger('click');
                }
            });

            // 监听工具条
            table.on('tool(list)', function (obj) {
                var data = obj.data;
                var tableId = 'tableIn_' + obj.tr[0].dataset.index;
                if (obj.event === 'showTable') {
                    $(this).attr('lay-event', 'delTable').html("<div class='layui-table-cell laytable-cell-1-0-0'><i class='layui-icon layui-icon-triangle-d'></i> </div>");
                    var colCount = obj.tr.find('td').length;
                    $(this).parent().after("<tr class='table-item" + tableId + "'><td colspan='" + colCount + "' style='padding:5px;'><table id='" + tableId + "'></table></td></tr>");
                    table.render({
                        elem: '#' + tableId
                        , id: 'table' + tableId
                        , url: '/admin/store-room/out-of-stock-data-view?userId=' + data.user_id + '&deliveryDate=' + data.delivery_date
                        , title: '发货出库详情'
                        , cols: [[
                            {field: 'commodity_id', title: '商品编码', sort: true}
                            , {field: 'commodity_name', title: '商品名称'}
                            , {field: 'notice', title: '描述'}
                            , {title: '预定量', templet: '#reserve'}
                            , {field: 'actual_num', title: '实际量', templet: '#actualNum'}
                            , {field: 'price', title: '发货单价'}
                            , {title: '小计', templet: "#totalPrice"}
                            , {field: 'notice', title: '状态', templet: "#sortStatus2"}
                        ]]
                        , page: false
                        , toolbar: false
                    });
                } else if (obj.event === 'delTable') {
                    $(this).attr('lay-event', 'showTable').html("<div class='layui-table-cell laytable-cell-1-0-0'><i class='layui-icon layui-icon-triangle-r'></i> </div>");
                    console.log(".table-item".tableId);
                    $(".table-item" + tableId).remove();
                } else if (obj.event === 'sendOut') {
                    // 发货出库
                    layer.confirm('是否确认发货？', {icon: 3, title:'提示'}, function (index) {
                        admin.req({
                            type: "get"
                            , url: '/admin/store-room/send-out-by-date-and-user-id'
                            , dataType: "json"
                            , cache: false
                            , data: {
                                date: data.delivery_date,
                                userId: data.user_id
                            }
                            , done: function (res) {
                                layer.closeAll();
                                if (res.data.isSendOut) {
                                    // 发货成功
                                    layer.msg('发货成功');
                                    // 更新状态
                                    obj.update({
                                        status: "2"
                                    });
                                    // 隐藏发货出库按钮
                                    obj.tr.find('[lay-event = sendOut]').remove();
                                } else {
                                    // 发货失败
                                    layer.open({
                                        type: 1
                                        , title: ['提示', 'font-size: 14px;font-weight: 700;']
                                        , content: '<table class="layui-hide" id="unSendList" lay-filter="unSendList"></table>'
                                        , maxmin: true
                                        , area: ['40%', '80%']
                                        , btn: ['关闭']
                                        , success: function () {

                                            table.render({
                                                elem: '#unSendList'
                                                , data: res.data.listData
                                                , title: '未分拣完发货失败'
                                                , cols: [[
                                                    {field: 'id', title: 'ID', unresize: true, sort: true}
                                                    , {field: 'commodity_name', title: '商品名称'}
                                                    , {field: 'unit', title: '单位', templet: "#unit"}
                                                    , {title: '状态', templet: "#sortStatus2"}
                                                ]]
                                                , page: false
                                                , toolbar: false
                                            });

                                        }
                                    });
                                }
                            }
                        });
                    });
                }
            });

            var active = {
                allSend:function(){
                    layer.confirm('是否确定批量发货？', {icon: 3, title:'提示'}, function (index) {
                        admin.req({
                            type: "get"
                            , url: '/admin/store-room/send-out-by-date'
                            , dataType: "json"
                            , cache: false
                            , data: {
                                date: $('[name = delivery_date]').val()
                            }
                            , done: function (res) {
                                if (res.data.isSendOut) {
                                    // 发货成功
                                    layer.msg('发货成功');
                                    // 更新状态
                                    table.reload('list');
                                    var iframeWindow = window['sendByOrderIframe'];
                                    iframeWindow.layui.table.reload('list');
                                    layer.close();
                                } else {
                                    // 发货失败
                                    layer.open({
                                        type: 1
                                        , title: ['提示', 'font-size: 14px;font-weight: 700;']
                                        , content: '<table class="layui-hide" id="unSendList" lay-filter="unSendList"></table>'
                                        , maxmin: true
                                        , area: ['40%', '80%']
                                        , btn: ['关闭']
                                        , success: function () {

                                            table.render({
                                                elem: '#unSendList'
                                                , data: res.data.listData
                                                , title: '未分拣完发货失败'
                                                , cols: [[
                                                    {field: 'id', title: 'ID', unresize: true, sort: true}
                                                    , {field: 'commodity_name', title: '商品名称'}
                                                    , {field: 'unit', title: '单位', templet: "#unit"}
                                                    , {title: '状态', templet: "#sortStatus2"}
                                                ]]
                                                , page: false
                                                , toolbar: false
                                            });

                                        }
                                    });
                                }
                            }
                        });
                    });
                },
                exports:function(){
                    layer.msg('export');
                }
            };

            $('.layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] && active[type].call(this);
            });

        });
    </script>