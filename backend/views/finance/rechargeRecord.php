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
            <div class="layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width: 200px">
                        客户账号:  <?=$username?>
                    </div>
                    <div class="layui-input-inline" style="width: 200px">
                        客户名称:  <?=$nickname?>
                    </div>
                    <div class="layui-input-inline" style="width: 200px">
                        店铺名称:  <?=$shop_name?>
                    </div>
                    <div class="layui-input-inline" style="width: 200px">
                        注册时间:  <?=date('Y-m-d H:i:s',$created_at)?>
                    </div>
                </div>

                <div class="layui-form-item layui-hide">
                    <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
            </div>
        </div>

        <script type="text/html" id="actual_priceTpl">
            <input type="text" name="actual_price" placeholder="" autocomplete="off" class="layui-input order_c" id="purchase_price" lay-filter="actual_price" style="height: 28px" value="{{d.need_pay}}">
        </script>
        <script type="text/html" id="reduction_priceTpl">
            <input type="text" name="reduction_price" placeholder="" autocomplete="off" class="layui-input order_c" id="small_price" lay-filter="reduction_price" style="height: 28px" value="0">
        </script>
        <script type="text/html" id="remarkTpl">
            <input type="text" name="remark" placeholder="" autocomplete="off" class="layui-input" style="height: 28px" value="">
        </script>

        <script type="text/html" id="imgTpl">
            <img style="display: inline-block; width: 50%; height: 100%;" src= '{{d.pic}}?x-oss-process=image/resize,h_50'>
        </script>

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
                        ,{field: 'recharge_no', title: '流水号', unresize: true}
                        ,{field: 'amount', title: '充值记录', minWidth: 120}
                        ,{field: 'current_balance', title: '变动后余额'}
                        ,{field: 'create_time', title: '操作时间', unresize: true}
                        ,{field: 'remark', title: '备注', unresize: true}
                    ]]
                    , url: '/admin/finance/get-recharge-data?id='+<?=$id?>
                    , page: true
                    , toolbar: true
                });


            });

        </script>