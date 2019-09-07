<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
    <div class="layui-card-header">基本信息<div style="float:right" class="layui-hide"><button class="layui-btn layui-btn-xs layui-btn-normal" id="copy-order" data-type="copyOrder">复制已有订单</button></div>
    </div>
    <div class="layui-card-body">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">客户</label>
                    <div class="layui-input-inline">
                        <input type="text" name="nickname" id="nickname" placeholder="输入客户名称/订单号/手机号" autocomplete="off" class="layui-input" style="width:210px" lay-verify="required" >
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">发货日期</label>
                    <div class="layui-input-inline">
                        <input type="text" name='delivery_date' class="layui-input" id="test-laydate-range-date" placeholder="请选择发货日期" lay-verify="required">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">发货时间</label>
                    <div class="layui-input-inline">
                        <select name="delivery_time_detail">
                            <option value="06:00-07:00">06:00-07:00</option>
                            <option value="07:00-08:00">07:00-08:00</option>
                        </select>
                    </div>
                </div>
            </div>
         </div>
        <div class="layui-form-item layui-hide">
            <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
            <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
        </div>
    </div>
    </div>

<div class="layui-card">
    <div class="layui-card-header">收货信息</div>
    <div class="layui-card-body">
        <div id="user-info">收货人：</div>
    </div>
</div>
<div class="layui-card">
    <div class="layui-card-header">订购商品清单</div>
    <div class="layui-card-body">
        <div class="layui-inline">
            <label class="layui-form-label">商品</label>
            <div class="layui-input-inline">
                <input type="text" name='name' class="layui-input" id="name" placeholder="请输入商品名称/编码/关键字" style="width: 230px;">
            </div>
        </div>
        <div class="layui-input-inline">
            <button class="layui-btn layui-input-inline" id="product-add-list" data-type="addProductList"><i class="layui-icon">&#xe654;</i></button>
        </div>

        <div class="layui-inline">
            <label class="layui-form-label">数量</label>
            <div class="layui-input-inline">
                <input type="text" name='count' class="layui-input" id="count" placeholder="" style="width: 60px;" value="1">
            </div>

            <div class="layui-input-inline">
                <button class="layui-btn layui-input-inline" id="product-add-list" data-type="addProductSingle">添加</button>
            </div>
    </div>
</div>
<div class="layui-card">
    <table class="layui-hide" id="subList" lay-filter="subList"></table>
    <script type="text/html" id="operation">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
    </script>
</div>
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-inline">
                <label class="layui-form-label">订单备注</label>
                <div class="layui-input-inline">
                    <input type="text" name="remark" id="remark" placeholder="输入订单备注" autocomplete="off" class="layui-input" style="width:850px">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="hidden" id="user_id" name="user_id">
        <input type="hidden" id="user_name" name="user_name">
        <input type="hidden" id="address_detail" name="address_detail">
        <input type="hidden" id="receive_name" name="receive_name">
        <input type="hidden" id="receive_tel" name="receive_tel">
        <input type="hidden" id="nick_name" name="nick_name">
    </div>
</div>

<script type="text/html" id="test-table-countTpl">
    <input type="text" name="order_count" placeholder="" autocomplete="off" class="layui-input order_c" id="order_count" lay-filter="order_count" style="height: 28px" value="{{d.num}}">
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
        var subList = [];
        table.render({
            elem: '#subList'
            ,cols: [[ //标题栏
                {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                ,{field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                ,{field: 'name', title: '商品名称', minWidth: 100}
                ,{field: 'type_name', title: '分类', minWidth: 120}
                ,{field: 'unit', title: '单位', width: 80}
                ,{field: 'num', title: '订购数',width: 120,templet: '#test-table-countTpl', unresize: true,edit: 'text',event: 'setCount'}
                ,{field: 'price', title: '订购单价（元）', width: 150}
                ,{field: 'total_price', title: '订购金额小计（元）', width: 190,totalRow: true}
                ,{field: 'remark', title: '备注', width: 180,templet: '#test-table-commentTpl', unresize: true,edit: 'text'}
                ,{title: '操作', minWidth: 100, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , data: subList
            , totalRow: true
            , page: false
            , toolbar: false
            , limit: Number.MAX_VALUE
            , title: "列表"
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


        var action = {
            copyOrder: function(){
                top.layui.admin.popupRight({
                    id: 'LAY_adminPopupLayerTest'
                    ,success: function(){
                        $('#'+ this.id).html('<div style="padding: 20px;">放入内容</div>');
                    }
                });
            },
            addProductList: function(){
                var create_window = layer.open({
                    id:'main',
                    type: 2,
                    content: '/admin/commodity-list/list',
                    area: ['1000px', '630px'],
                    title:'请选择商品',
                    maxmin: false,
                    btn: ['确定', '取消'],yes: function(index, layero){
                        var sonTable = layero.find('iframe')[0].contentWindow.layui.table.checkStatus('list').data;
                        sonTable.forEach(function (e) {
                            e.total_price = toDecimal2(e.num * e.price)
                        });
                        table.reload('subList', {
                            data: layui.table.cache['subList'].concat(sonTable)
                        });
                        layer.close(index);
                    }
                });
            },
            addProductSingle: function(){
                table_name.num=$('#count').val();
                table_name.total_price = table_name.num * table_name.price;
                table.reload('subList', {
                    data: layui.table.cache['subList'].concat(table_name)
                });
            },
            saveOrder:function(){
                //保存订单数据
            }
        };


        $('.layui-btn').on('click', function(e){
            var type = $(this).data('type');
            action[type] && action[type].call(this);
            console.log(e);
        });

        yutons_sug.render({
            id: "nickname", //设置容器唯一id
            height: "200",
            width:"400",
            cols: [
                [{
                    field: 'nickname',
                    title: '客户名称'
                },
                    {
                    field: 'username',
                    title: '联系电话'
                },
                    {
                    field: 'address',
                    title: '地址'
                }]
            ], //设置表头
            params: [
                {
                    name: 'nickname',
                    field: 'nickname'
                },
                {
                    name: 'username',
                    field: 'username'
                }, {
                    name: 'address',
                    field: 'address'
                }],//设置字段映射，适用于输入一个字段，回显多个字段
            type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
            url: '/admin/user/get-user-list?keyword=' //设置异步数据接口,url为必填项,params为字段名
        });

        //初始化姓名输入提示框
        yutons_sug.render({
            id: "name", //设置容器唯一id
            height: "200",
            width:"400",
            cols: [
                [{
                    field: 'name',
                    title: '商品名称'
                },
                    {
                        field: 'price',
                        title: '价格'
                    },
                    {
                        field: 'unit',
                        title: '单位'
                    }]
            ], //设置表头
            params: [
                {
                    name: 'name',
                    field: 'name'
                },
                {
                    name: 'price',
                    field: 'price'
                }, {
                    name: 'unit',
                    field: 'unit'
                }],//设置字段映射，适用于输入一个字段，回显多个字段
            type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
            url: '/admin/commodity-list/index-data?keyword=' //设置异步数据接口,url为必填项,params为字段名
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