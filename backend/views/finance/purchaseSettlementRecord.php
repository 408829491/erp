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
                        {field:'id', title:'ID', width:80 ,fixed: 'left', unresize: true, sort: true}
                        ,{field: 'refer_no', title: '原始单号', unresize: true}
                        ,{field: 'settle_no', title: '结算单号'}
                        ,{field: 'create_user', title: '制单人'}
                        ,{field: 'pay_user', title: '交款人'}
                        ,{field: 'pay_way_text', title: '付款方式'}
                        ,{field: 'actual_price', title: '实收金额'}
                        ,{field: 'reduction_price', title: '抹零金额'}
                        ,{field: 'create_time', title: '结算日期', unresize: true}
                        ,{field: 'remark', title: '备注', unresize: true}
                    ]]
                    , url: '/admin/finance/get-purchase-settlement-record?refer_no=<?=Yii::$app->request->get("refer_no")?>'
                    , page: true
                    , toolbar: true
                });


            });

        </script>