<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入销售员姓名" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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
                        <a class="layui-btn layui-btn-xs" lay-event="detail">已推广客户</a>
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
                    </script>
                </div>
            </div>
        </div>
    </div>
    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_check" value="{{d.lock_status}}" lay-skin="switch" lay-text="启用|冻结" lay-filter="switch-check" {{ d.lock_status == 1 ? 'checked' : '' }}>
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
                ,url: '/admin/salesman/get-index-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'name', title:'销售姓名'}
                    ,{field:'account_tel', title:'销售帐号',sort: true}
                    ,{field:'invitation_code', title:'邀请码', sort: true}
                    ,{field:'num', title:'推广人数'}
                    ,{field:'sale_num', title:'销售业绩(订单数)'}
                    ,{field:'create_time', title:'创建时间'}
                    ,{field:'lock_status', title:'状态',templet: '#switchTpl',width:110}
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
                        layer.open({
                            type: 2,
                            content: '/admin/salesman/create',
                            area: ['800px', '430px'],
                            title: '新增销售员',
                            maxmin: true,
                            btn: ['保存', '取消'], yes: function (index, layero) {
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        break;
                }
            });

            //监听行工具事件
            table.on('tool(list)', function(obj){
                switch(obj.event){
                    case 'edit':
                        layer.open({
                            type: 2,
                            content: '/admin/salesman/update?id=' + obj.data.id,
                            area: ['800px', '430px'],
                            title:'编辑',
                            maxmin: true,
                            btn: ['保存', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        break;
                    case 'detail':
                        layer.open({
                            type: 2,
                            content: '/admin/salesman/view?code='+ obj.data.invitation_code,
                            area: ['1080px', '830px'],
                            title:'推广客户列表',
                            maxmin: true,
                            btn: ['返回']
                        });
                        break;
                    case 'del':
                        layer.confirm('确认要删除该销售员吗?', function(index){
                            $.get({
                                type:"POST",
                                url:"/admin/salesman/del?id="+obj.data.id,
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


            //清除查询条件
            form.on('submit(search-clear)', function(obj){
                form.val("layui-form", {
                    'keyword': ''
                })
            });

            //监听搜索
            form.on('submit(search)', function(data){
                var field = data.field;
                //执行重载
                table.reload('list', {
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
                    url: "/admin/salesman/change-status?id=" + id,
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