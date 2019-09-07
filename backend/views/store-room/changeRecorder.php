<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list">
        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
            </div>
        </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'laydate','form','element'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,table = layui.table;


        //table设置默认参数
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
            , limit: 10
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        //展示已知数据
        table.render({
            elem: '#subList'
            ,cols: [[ //标题栏
                {field: 'recharge_no', title: '变更时间', unresize: true,width:200}
                ,{field: 'refer_no', title: '操作人', unresize: true,width:200}
                ,{field: 'amount', title: '变更价格', minWidth: 120}
                ,{field: 'current_balance', title: '变更后价格'}
            ]]
            , url:'/admin/finance/get-balance-data?id='+95
            , page: true
            , toolbar: false
        });



    });


</script>

