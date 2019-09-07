<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select lay-filter="type" name="type_first_tier_id">
                                <option value="">商品分类</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="delivery_date" class="layui-input" id="test-laydate-range-date"
                                   placeholder="">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="channel_type">
                                <option value="">采购类型</option>
                                <option value="0">市场自采</option>
                                <option value="1">供应商直供</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入商品名称/编码/别名/关键字" autocomplete="off"
                               class="layui-input" id="commodity" style="width:300px;">
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

<!--                    <div class="layui-inline">-->
<!--                        <div class="layui-input-block">-->
<!--                            <input type="checkbox" name="like1[write]" lay-skin="primary" title="是否计算库存" checked="">-->
<!--                        </div>-->
<!--                    </div>-->
                    
                    <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">
                        <button class="layui-btn  layui-btn-normal" data-type="create_purchase"
                                style="background-color: #77CF20" lay-submit lay-filter="create_purchase"
                                id="create_purchase">生成采购单
                        </button>

                    </div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">

                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>

                    <script type="text/html" id="triangle">
                        <i class="layui-icon layui-icon-triangle-r"></i>
                    </script>

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
        }).use(['index', 'table', 'laydate', 'element', 'form', 'admin'], function () {
            var table = layui.table
                , laydate = layui.laydate
                , element = layui.element
                , admin = layui.admin
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
                , limit: 10
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            table.render({
                elem: '#order-list'
                , url: '/admin/batch-summary/list'
                , title: '订单汇总表'
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
                    , {type: 'checkbox', LAY_CHECKED: true}
                    , {field: 'commodity_id', title: 'ID', sort: true, width: 100}
                    , {field: 'commodity_name', title: '商品', sort: true}
                    , {field: 'unit', title: '单位', width: 100}
                    , {field: 'order_counts', title: '订单数', sort: true}
                    , {field: 'total_num', title: '汇总量', sort: true}
                    , {field: 'stock_num', title: '库存量', sort: true}
                    , {field: 'is_purchase_num', title: '在途库存量', sort: true}
                    , {field: 'need_purchase_num', title: '待采购量', sort: true}
                ]]
                , page: false
                , done: function (res, curr, count) {
                    $(".layui-table-bool-self").append('<p>订单汇总表</p>');
                }
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


            // 获取分类
            var typeDataList;

            admin.req({
                type: "get"
                , url: '/admin/commodity-category/first-tier-data'
                , dataType: "json"
                , cache: false
                , data: {}
                , done: function (res) {
                    typeDataList = res.data;
                    for (var i = 0; i < typeDataList.length; i++) {
                        $("[name=type_first_tier_id]").append('<option value="' + typeDataList[i].id + '">' + typeDataList[i].name + '</option>');
                    }
                    form.render();
                }
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date',
                value: '<?= date("Y-m-d", strtotime("+1 day")) ?>',
                done: function (value) {
                    table.reload('order-list', {
                        where: {'delivery_date':value}
                    });
                }
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
            form.on('submit(create_purchase)', function (data) {
                var table_data = layui.table.checkStatus('order-list');
                var field = {}
                field.commodity_list = table_data['data'];
                field.purchase_ids = '1,8';
                field.plan_date = $('#test-laydate-range-date').val();
                layer.confirm('共 ' + table_data['data'].length + ' 条记录，按采购类型拆分成多个采购单 确定后，请相关人员尽快处理采购单', function (index) {
                    admin.req({
                        type: "post"
                        , url: '/admin/purchase/order-to-purchase'
                        , cache: false
                        , data: field
                        , done: function (res) {
                            if (res.code === 200) {
                                url = "/admin/purchase/index";
                                layer.msg('采购单生成成功');
                                newTab(url, '采购单')
                            } else {
                                layer.msg('采购单生成失败');
                            }
                        }
                    });
                    layer.close(index);
                });

            });

            function newTab(url, tit) {
                if (top.layui.index) {
                    var win = top.layui.index.openTabsPage(url, tit);
                    console.log(win);
                    win.layui.table.reload('order-list');
                } else {
                    window.open(url)
                }
            }

            //监听单元格事件
            table.on('tool(order-list)', function (obj) {
                var data = obj.data;
                var tableId = 'tableIn_' + obj.tr[0].dataset.index;
                if (obj.event === 'showTable') {
                    $(this).attr('lay-event', 'delTable').html("<div class='layui-table-cell laytable-cell-1-0-0'><i class='layui-icon layui-icon-triangle-d'></i> </div>");
                    var colCount = obj.tr.find('td').length;
                    $(this).parent().after("<tr class='table-item" + tableId + "'><td colspan='" + colCount + "' style='padding:5px;'><table id='" + tableId + "'></table></td></tr>");
                    table.render({
                        elem: '#' + tableId
                        , id: 'table' + tableId
                        , url: '/admin/order/order-list?order_ids=' + obj.data['order_ids']
                        , title: '订单'
                        , cols: [[
                            {field: 'order_no', title: '订单编号', width: 235}
                            , {field: 'nick_name', title: '客户名称', width: 230, sort: true}
                            , {field: 'price', title: '价格', sort: true}
                            , {field: 'delivery_date', title: '发货日期'}
                            , {field: 'pay_text', title: '实际量'}
                            , {field: 'line_name', title: '下单单价'}
                            , {field: 'line_name', title: '发货单价'}
                            , {field: 'line_name', title: '小计'}
                            , {field: 'line_name', title: '状态'}
                        ]]
                        , page: false
                        , toolbar: false
                    });
                } else if (obj.event === 'delTable') {
                    $(this).attr('lay-event', 'showTable').html("<div class='layui-table-cell laytable-cell-1-0-0'><i class='layui-icon layui-icon-triangle-r'></i> </div>");
                    console.log(".table-item".tableId);
                    $(".table-item" + tableId).remove();
                }
            });

        });
    </script>