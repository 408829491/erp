<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">

    <div class="layui-card">
        <table class="layui-hide" id="subList" lay-filter="subList"></table>
    </div>

    <div class="layui-form-item layui-hide">
        <input type="hidden" id="id" name="id" value="<?php if(Yii::$app->request->get('type')!='copy'){echo $id;} else echo 0;?>">
        <input type="hidden" id="user_id" name="user_id" value="<?=$user_id?>">
        <input type="hidden" id="user_name" name="user_name" value="<?=$user_name?>">
        <input type="hidden" id="address_detail" name="address_detail" value="<?=$address_detail?>">
        <input type="hidden" id="receive_name" name="receive_name" value="<?=$receive_name?>">
        <input type="hidden" id="receive_tel" name="receive_tel" value="<?=$receive_tel?>">
        <input type="hidden" id="nick_name" name="nick_name" value="<?=$nick_name?>">
        <input type="hidden" id="status" name="status" value="<?=$status?>">
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
    </div>
</div>

<script type="text/html" id="test-table-countTpl">
    <input type="text" name="order_count" placeholder="" autocomplete="off" class="layui-input order_c" id="order_count" lay-filter="order_count" style="height: 28px" value="{{d.refund_num}}">
</script>

<script type="text/html" id="test-table-commentTpl">
    <input type="text" name="remark" placeholder="" autocomplete="off" class="layui-input" style="height: 28px" value="{{d.remark}}">
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
    }).use(['index', 'table', 'laydate','form','element','yutons_sug'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,table = layui.table
            ,laydate = layui.laydate
            ,form = layui.form
            ,element = layui.element
            ,yutons_sug  = layui.yutons_sug
            ,table_name = []
            ,table_username = [];

        element.render();

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
            , limit: 50
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        //展示已知数据
        var subList = <?=json_encode($details)?>;
        table.render({
            elem: '#subList'
            ,cols: [[ //标题栏
                {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                ,{field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                ,{field: 'name', title: '商品名称', minWidth: 100}
                ,{field: 'unit', title: '单位', width: 80}
                ,{field: 'price', title: '订购单价（元）', width: 150}
                ,{field: 'num', title: '预定数量',width: 120, unresize: true}
                ,{field: 'refund_num', title: '退货数量',width: 120,templet: '#test-table-countTpl', unresize: true,edit: 'text',event: 'setCount'}
            ]]
            , data: subList
            , page: false
            , toolbar: false
            , title: "商品列表"
            ,done: function(res, curr, count){
                tableDataTemp = res;
            }
        });

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段
            field.commodity_list = layui.table.cache['subList'];
            var subListIndex = 0;
            field.commodity_list.forEach(function (e) {
                if (!$.isEmptyObject(e)) {
                    e.commodity_id = e.id;
                    subListIndex += 1;
                }
            });
            var index = parent.layer.getFrameIndex(window.name);
            admin.req({
                type: "post"
                , url: '/admin/order/save'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('order-list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
                }
            });
        });


        //监听工具条
        table.on('tool(subList)', function (obj) {
            var data = obj.data;
            if (obj.event === 'del') {
                obj.del();
            }
        });


        //发货日期
        laydate.render({
            elem: '#test-laydate-range-date',
            min:minDate()
        });




        //获取商品筛选数据
        table.on('row(yutons_sug_name)', function(obj) {
            var data = obj.data;
            table_name = data;
            $("#name").val(data.name);
            $("#yutons_sug_name").next().hide().html("");
        });


        //获取客户筛选数据
        table.on('row(yutons_sug_nickname)', function(obj) {
            var data = obj.data;
            $("#nickname").val(data.nickname);
            $("#nick_name").val(data.nickname);
            $("#address_detail").val(data.address);
            $("#user_name").val(data.username);
            $("#user_id").val(data.id);
            $("#receive_name").val(data.contact_name);
            $("#receive_tel").val(data.username);
            $("#yutons_sug_nickname").next().hide().html("");
            $('#user-info').html('收货人：' + data.contact_name + ' ' + data.username + ' ' + data.address);
        });


        //监听单元格编辑
        table.on('edit(subList)', function(obj){
            var value = obj.value;//得到修改后的值
            obj.update({
                total_price : (obj.data.price * value).toFixed(2)
            });
            table.reload('subList',{
                data:table.cache[subList]
            });
        });

    });

    //格式化价格
    function toDecimal2(x) {
        var f = parseFloat(x);
        if (isNaN(f)) {
            return false;
        }
        var s = f.toString();
        var rs = s.indexOf('.');
        if (rs < 0) {
            rs = s.length;
            s += '.';
        }
        while (s.length <= rs + 2) {
            s += '0';
        }
        return s;
    }

    // 设置最小可选的日期
    function minDate(){
        var now = new Date();
        now.setDate(now.getDate()+1);
        return now.getFullYear()+"-" + (now.getMonth()+1) + "-" + (now.getDate());
    }

</script>