<div class="layui-fluid">
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
    }).use(['index', 'table'], function(){
        var table = layui.table;
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
            ,url: '/admin/order/get-user-order-list-data?user_id=<?=$id?>'
            ,toolbar: '#order-list-toolbarDemo'
            ,title: '用户数据表'
            ,cols: [[
                 {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                ,{field:'order_no', title:'订单号', width:235}
                ,{field:'price', title:'下单金额', sort: true,width:120}
                ,{field:'pay_price', title:'发货金额', sort: true,width:120}
                ,{field:'create_time', title:'下单日期'}
            ]]
            ,page: true
        });

    });
</script>