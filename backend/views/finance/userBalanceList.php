<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="is_pay">
                                <option value="">客户类型</option>
                                <option value="0">商超</option>
                                <option value="1">菜摊</option>
                                <option value="2">水果店</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="source">
                                <option value="">所属业务员</option>
                                <option value="1">业务员一</option>
                                <option value="2">业务员二</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入客户名称/联系人/手机号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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
                        <a class="layui-btn layui-btn-xs" lay-event="recharge_record">充值记录</a>
                        <a class="layui-btn layui-btn-xs" lay-event="balance_record">收支记录</a>
                        <a class="layui-btn layui-btn-xs" lay-event="recharge" style="background-color: #77CF20">充值</a>
                        <a class="layui-btn layui-btn-xs" lay-event="withhold" style="background-color: #77CF20">扣款</a>

                    </script>
                </div>
            </div>
        </div>
    </div>


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
                ,url: '/admin/finance/balance'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'nickname', title:'客户名称',sort: true}
                    ,{field:'username', title:'客户账号', sort: true}
                    ,{field:'shop_name', title:'店铺名称', sort: true}
                    ,{field:'balance', title:'余额',sort: true}
                    ,{field:'invite_code', title:'业务员/邀请码'}
                    ,{fixed: 'right', title:'操作',width:280, toolbar: '#order-list-barDemo'}
                ]]
                ,page: true
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