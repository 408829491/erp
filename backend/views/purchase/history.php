<style>
    .image_div {
        width:100px;
        height: 100px;
        display: flex;
        justify-items: center;
        align-items: center;
        margin-left: 20px;
    }
    .image_div_img {
        width:100px;
        height: 100px;
    }
    .image_div_close_icon {
        position: absolute;
        margin-top: -40px;
        margin-left: 80px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">

        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
            </div>
        </div>

        <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
        <script>
            layui.config({
                base: '/admin/plugins/layuiadmin/' //静态资源所在路径
            }).extend({
                index: 'lib/index' //主入口模块
            }).use(['index', 'table', 'laydate','form'], function(){
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
                        {field: 'create_time', title: '进货时间',}
                        ,{field: 'num', title: '进货数量'}
                        ,{field: 'price', title: '进货价格'}
                    ]]
                    , url: '/admin/purchase/get-purchase-price-history-data?id=<?=Yii::$app->request->get("id")?>'
                    , page: false
                    , toolbar: false
                });


            });

        </script>