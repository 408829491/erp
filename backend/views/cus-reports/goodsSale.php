<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select lay-filter="type" name="typeId">
                                <option value="">商品分类</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="store">
                                <option value="0">有销量</option>
                                <option value="1">无销量</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <div class="layui-input-inline" style="width:300px;">
                            <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date" placeholder="" style="width:300px;">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入商品名称/条码/关键字" autocomplete="off" class="layui-input" id="commodity" style="width:300px;">
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
                    <script type="text/html" id="triangle">
                        <i class="layui-icon layui-icon-triangle-r"></i>
                    </script>

                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="order-list-barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="detail">趋势分析</a>
    </script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','element','form','admin'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,admin = layui.admin
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
                , limit: 30
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            table.render({
                elem: '#order-list'
                ,url: '/admin/cus-reports/get-goods-sale-data'
                ,title: '订单汇总表'
                ,cols: [[
                    {field:'commodity_id', title:'ID', sort: true,width:180}
                    ,{field:'commodity_name', title:'商品', sort: true,width:180}
                    ,{field:'unit', title:'单位',width:70}
                    ,{field:'product_code', title:'商品条码'}
                    ,{field:'type_name', title:'分类'}
                    ,{field:'total_num', title:'销售数量', sort: true}
                    ,{field:'sell_stock', title:'库存量', sort: true}
                    ,{field:'total_price', title:'商品总价'}
                    ,{field:'total_price', title:'实收金额', sort: true}
                    ,{field:'total_profit', title:'利润', sort: true}
                    ,{field:'profit_ratio', title:'利润率', sort: true}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo',width:85}
                ]]
                ,page: true
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date',
                value: '<?=date("Y-m-d",time())?>' + ' 00:00:00 - ' + '<?=date("Y-m-d",time() + 1)?>' + ' 23:59:59',
                type:'datetime',
                range: true
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
                        ,id:'table'+tableId
                        , url: '/admin/order/order-list'
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
                    console.log(obj);
                }
            });

        });
    </script>