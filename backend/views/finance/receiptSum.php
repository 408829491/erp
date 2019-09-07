<div class="layui-fluid">
    <div class="layui-row">

        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li lay-data="1" class="layui-this">应收账款汇总</li>
                    <li>客户结算流水</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">

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
                                        <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date" placeholder="请选择日期">
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
                            </div>
                        </div>

                    </div>
                    <div class="layui-tab-item" style="padding:0">
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
                                        <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date" placeholder="请选择日期">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <input type="text" name="keyword" placeholder="输入客户名称/联系人/手机号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search2">
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
                                <table class="layui-hide" id="order-list2" lay-filter="order-list2"></table>
                            </div>
                        </div>
                    </div>

                </div>
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
                , limit: 13
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            table.render({
                elem: '#order-list'
                ,url: '/admin/finance/get-receipt-sum-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'user_id', title:'客户编码', width:120, fixed: 'left', unresize: true, sort: true}
                    ,{field:'nick_name', title:'客户名称',sort: true}
                    ,{field:'amount', title:'应收金额', sort: true}
                    ,{field:'pay_amount', title:'已收金额', sort: true}
                    ,{field:'will_pay_amount', title:'未收金额', sort: true}
                    ,{field:'receive_name', title:'联系人'}
                    ,{field:'receive_tel', title:'手机'}
                ]]
                ,page: true
            });


            table.render({
                elem: '#order-list2'
                ,url: '/admin/finance/get-balance-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'pay_user', title:'客户名称',sort: true}
                    ,{field:'recharge_no', title:'结算单号',width:300}
                    ,{field: 'type', title: '收支类型', unresize: true,templet:function(d){
                        if(d.type === '0')
                            return '充值';
                        else if(d.type === '1')
                            return '扣款';
                        else
                            return '订单支付';
                    },width:200}
                    ,{field:'amount', title:'实收金额', sort: true,width:200}
                    ,{field:'refer_no', title:'原始单号',width:200}
                    ,{field:'create_time', title:'单据日期', sort: true,width:200}
                ]]
                ,page: true
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

            form.on('submit(search2)', function(data){
                var field = data.field;
                //执行重载
                table.reload('order-list2', {
                    where: field
                });
            });


        });
    </script>