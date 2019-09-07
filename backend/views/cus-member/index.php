<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="is_pay">
                                <option value="">会员来源</option>
                                <option value="0">门店</option>
                                <option value="1">小程序</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入用户姓名/手机号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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

                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>


                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
                    </script>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="order-list-toolbarDemo">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-list" lay-event="add">新增</button>
            <button class="layui-btn layui-btn-list" lay-event="syncData">同步</button>
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
                elem: '#order-list'
                ,url: '/admin/cus-member/get-index-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'nickname', title:'会员名称',sort: true}
                    ,{field:'username', title:'会员账号', sort: true}
                    ,{field:'mobile', title:'电话号码', sort: true}
                    ,{field:'balance', title:'余额',sort: true}
                    ,{field:'integral', title:'积分',sort: true}
                    ,{field:'source', title:'来源',sort: true}
                    ,{field:'created_at', title:'注册日期'}
                    /*,{fixed: 'right', title:'操作',width:120, toolbar: '#order-list-barDemo'}*/
                ]]
                ,page: true
            });

            //头工具栏事件
            table.on('toolbar(order-list)', function(obj){
                switch(obj.event) {
                    case 'add':
                        var windows = layer.open({
                            type: 2,
                            content: '/admin/cus-member/create',
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
                    case 'syncData':
                        admin.req({
                            type: "post"
                            , url: '/admin/purchase/saves'
                            , dataType: "json"
                            , cache: false
                            , data: []
                            , done: function () {
                                layui.table.reload('order-list'); // 重载表格
                            }
                        });
                        break;
                }
            });

            //监听行工具事件
            table.on('tool(order-list)', function(obj){
                switch(obj.event){
                    case 'recharge_record':
                        layer.open({
                            type: 2,
                            content: '/admin/finance/recharge-record?id=' + obj.data.id,
                            area: ['1200px', '750px'],
                            title:'充值记录'
                        });
                        break;
                    case 'balance_record':
                        layer.open({
                            type: 2,
                            content: '/admin/finance/balance-record?id='+ obj.data.id,
                            area: ['1200px', '750px'],
                            title:'收支记录'
                        });
                        break;
                    case 'recharge':
                        layer.open({
                            type: 2,
                            content: '/admin/finance/recharge?id=' + obj.data.id,
                            area: ['630px', '530px'],
                            title:'充值',
                            maxmin: true,
                            btn: ['提交', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        break;
                    case 'withhold':
                        layer.open({
                            type: 2,
                            content: '/admin/finance/withhold?id=' + obj.data.id,
                            area: ['630px', '480px'],
                            title:'扣款',
                            maxmin: true,
                            btn: ['提交', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
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
                table.reload('order-list', {
                    where: field
                });
            });

        });
    </script>