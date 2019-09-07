<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="c_type">
                                <option value="">客户类型</option>
                                <?php foreach ($c_type as $item) : ?>
                                    <option value="<?=$item->name?>"><?=$item->name?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="area_name">
                                <option value="">所属区域</option>
                                <?php foreach ($area as $item) : ?>
                                    <option value="<?=$item->area_name?>"><?=$item->area_name?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入客户名称/手机号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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

                    <table class="layui-hide" id="customer-list" lay-filter="customer-list"></table>

                    <script type="text/html" id="order-list-toolbarDemo">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-list" lay-event="getCheckData">新增</button>
                        </div>
                    </script>

                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                    </script>
                </div>
            </div>
        </div>
    </div>
    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_check" value="{{d.is_check}}" lay-skin="switch" lay-text="启用|关闭" lay-filter="switch-check" {{ d.is_check == 1 ? 'checked' : '' }}>
    </script>
    <script type="text/html" id="progressTpl">
        <div class="layui-progress" style="margin-top:12px;">
            <div class="layui-progress-bar" lay-percent="{{#  if(d.status == 3){ }}100%{{#  } }}{{#  if(d.status == 2){ }}50%{{#  } }}{{#  if(d.status == 1){ }}20%{{#  } }}"></div>
        </div>
    </script>

    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','element','form'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,form = layui.form
                , admin = layui.admin;

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
                elem: '#customer-list'
                ,url: '/admin/customer/get-index-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'username', title:'客户账号'}
                    ,{field:'nickname', title:'客户名称',sort: true}
                    ,{field:'shop_name', title:'店铺名称', sort: true}
                    ,{field:'c_type_id', title:'客户类型',templet:function (d) {
                        if(d.c_type_id === '2'){
                            return '门店';
                        }else{
                            return 'B端客户';
                        }
                    }}
                    ,{field:'line_name', title:'配送线路'}
                    ,{field:'mobile', title:'联系电话'}
                    ,{field:'created_at', title:'注册时间'}
                    ,{field:'is_check', title:'状态',templet: '#switchTpl',width:110}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo'}
                ]]
                ,page: true
                ,done: function(){
                    element.render();
                }
            });

            //头工具栏事件
            table.on('toolbar(customer-list)', function(obj){
                switch(obj.event) {
                    case 'getCheckData':
                        var windows = layer.open({
                            type: 2,
                            content: '/admin/customer/create',
                            area: ['800px', '830px'],
                            title: '新增客户',
                            maxmin: true,
                            btn: ['保存', '取消'], yes: function (index, layero) {
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows);
                        break;
                }
            });

            //监听行工具事件
            table.on('tool(customer-list)', function(obj){
                switch(obj.event){
                    case 'edit':
                        var windows_update = layer.open({
                            type: 2,
                            content: '/admin/customer/update?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title:'编辑',
                            maxmin: true,
                            btn: ['保存', '取消'],
                            yes: function(index, layero){
                                var iframeWindow = window['layui-layer-iframe' + index];

                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");

                                //监听提交
                                iframeWindow.layui.form.on('submit(layuiadmin-app-form-submit)', function (data1) {

                                    var field = data1.field; //获取提交的字段
                                    var lineName = field['line_name'].split(":;");
                                    field['line_id'] = lineName[0];
                                    field['line_name'] = lineName[1];

                                    //提交 Ajax 成功后，静态更新表格中的数据
                                    admin.req({
                                        type: "post"
                                        , url: layui.setter.reqUrlBase + '/admin/customer/save?id=' + obj.data.id
                                        , dataType: "json"
                                        , cache: false
                                        , data: field
                                        , done: function () {
                                            obj.update({
                                                nickname: field.nickname
                                                , shop_name: field.shop_name
                                                , c_type: field.c_type
                                                , mobile: field.mobile
                                            }); //数据更新

                                            form.render();
                                            layer.close(index); //关闭弹层
                                        }
                                    });
                                });

                                submit.click();
                            }
                        });
                        layer.full(windows_update);
                        break;
                    case 'detail':
                        var window_detail = layer.open({
                            type: 2,
                            content: '/admin/customer/view?id='+ obj.data.id,
                            area: ['800px', '830px'],
                            title:'详情',
                            maxmin: true,
                            btn: ['返回']
                        });
                        layer.full(window_detail);
                        break;
                    case 'del':
                        layer.confirm('确认要删除该客户吗?', function(index){
                            $.get({
                                type:"POST",
                                url:"/admin/customer/del?id="+obj.data.id,
                                dataType:"json",
                                success:function(data){
                                    if(data.code=='200'){
                                        table.reload('customer-list');
                                        layer.msg('删除成功！', {icon: 1});
                                    }else{
                                        layer.msg(data.msg, {icon: 2});
                                    }
                                }
                            });
                            layer.close(index);
                        });
                        break;
                }
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date'
                ,range: true
                ,theme: 'molv'

            });

            laydate.render({
                elem: '#test-send-date-range-date'
                ,range: true
                ,theme: 'molv'

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
                table.reload('customer-list', {
                    where: field
                });
            });

            //监听指定开关
            form.on('switch(switch-check)', function(data){
                var selectIfKey=data.othis;
                var parentTr = selectIfKey.parents("tr");
                var id = $(parentTr).find("td:eq(0)").find(".layui-table-cell").text();

                    $.get({
                        type: "get",
                        url: "/admin/customer/change-status?id=" + id,
                        dataType: "json",
                        success: function (data) {
                            if (data.code == '200') {
                                layer.msg('操作成功！', {icon: 1});
                            } else {
                                layer.msg(data.msg, {icon: 2});
                            }
                        }
                    });
            });

        });
    </script>