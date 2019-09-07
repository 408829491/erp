<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
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

                    <table class="layui-hide" id="list" lay-filter="list"></table>

                    <script type="text/html" id="order-list-toolbarDemo">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-list" lay-event="getCheckData">新增</button>
                        </div>
                    </script>

                    <script type="text/html" id="order-list-barDemo">
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
                , limit: 15
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            table.render({
                elem: '#list'
                ,url: '/admin/sort-sorter/get-index-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'nickname', title:'姓名'}
                    ,{field:'username', title:'登录账号',sort: true}
                    ,{field:'stock_name', title:'所属仓库', sort: true}
                    ,{field:'role_name', title:'角色'}
                    ,{field:'created_at', title:'创建时间'}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo'}
                ]]
                ,page: true
                ,done: function(){
                    element.render();
                }
            });

            //头工具栏事件
            table.on('toolbar(list)', function(obj){
                switch(obj.event) {
                    case 'getCheckData':
                        var windows = layer.open({
                            type: 2,
                            content: '/admin/sort-sorter/create',
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
            table.on('tool(list)', function(obj){
                switch(obj.event){
                    case 'edit':
                        var windows_update = layer.open({
                            type: 2,
                            content: '/admin/sort-sorter/update?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title:'编辑',
                            maxmin: true,
                            btn: ['保存', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows_update);
                        break;
                    case 'detail':
                        var window_detail = layer.open({
                            type: 2,
                            content: '/admin/sort-sorter/view?id='+ obj.data.id,
                            area: ['800px', '830px'],
                            title:'详情',
                            maxmin: true,
                            btn: ['返回']
                        });
                        layer.full(window_detail);
                        break;
                    case 'del':
                        layer.confirm('确认要删除该分拣员吗?', function(index){
                            $.get({
                                type:"POST",
                                url:"/admin/sort-sorter/del?id="+obj.data.id,
                                dataType:"json",
                                success:function(data){
                                    if(data.code=='200'){
                                        table.reload('list');
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


        });
    </script>