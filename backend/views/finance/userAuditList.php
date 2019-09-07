
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="pay_way">
                                <option value="">支付方式</option>
                                <option value="0">货到付款</option>
                                <option value="1">在线支付</option>
                                <option value="2">余额支付</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="delivery_date" class="layui-input"  id="test-laydate-range-date" placeholder="请选择发货日期">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="is_audit">
                                <option value="">请选择对账状态</option>
                                <option value="1">已对账</option>
                                <option value="0">未对账</option>
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
                        <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
                        {{#  if(d.is_audit == 0){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="audit" style="background-color: #77CF20">对账</a>
                        {{#  } }}
                        {{#  if(d.is_pay == 'N'){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="settlement" style="background-color: #77CF20">结算</a>
                        {{#  } }}
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
                ,url: '/admin/finance/get-user-audit-list'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'order_no', title:'业务单号', width:205}
                    ,{field:'nick_name', title:'客户名称', width:230,sort: true}
                    ,{field:'price', title:'应收金额', width:100, sort: true}
                    ,{field:'pay_price', title:'已收金额', width:100, sort: true}
                    ,{field:'need_pay', title:'未收金额', width:100, sort: true}
                    ,{field:'delivery_date', title:'发货日期',width:130}
                    ,{field:'settlement_type', title:'单据类型', width:100, sort: true}
                    ,{field:'line_name', title:'线路', width:100}
                    ,{field:'driver_name', title:'司机', width:100}
                    ,{field:'audit_text', title:'对账状态', width:100,templet:function(e){
                            if(e.audit_text === '未对账')
                                return '<div style="color:red">'+e.audit_text+'</div>';
                            else
                                return '<div style="color:green">'+e.audit_text+'</div>';
                        }}
                    ,{field:'settlement_text', title:'结算状态', width:100,templet:function(e){
                             if(e.settlement_text === '未结算')
                                 return '<div style="color:red">'+e.settlement_text+'</div>';
                             else
                                 return '<div style="color:green">'+e.settlement_text+'</div>';
                    }}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo', unresize: true,width:160}
                ]]
                ,page: true
            });

            //监听行工具事件
            table.on('tool(order-list)', function(obj){
                switch(obj.event){
                    case 'detail':
                        var window_detail = layer.open({
                            type: 2,
                            content: '/admin/finance/view?id='+ obj.data.id,
                            area: ['800px', '830px'],
                            title:'单据详情',
                            maxmin: true,
                            btn: ['返回']
                        });
                        layer.full(window_detail);
                        break;
                    case 'settlement':
                        var windows_settlement = layer.open({
                            type: 2,
                            content: '/admin/finance/settlement?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title:'结算',
                            maxmin: true,
                            btn: ['确认收款', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows_settlement);
                        break;
                    case 'audit':
                        var windows_audit = layer.open({
                            type: 2,
                            content: '/admin/finance/audit?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title:'对账',
                            maxmin: true,
                            btn: ['确定', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows_audit);
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

            //监听Tab页
            element.on('tab(component-tabs)', function(){
                var _this = this;
                //数据重载
                table.reload('order-list', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    ,where: {
                        status:_this.getAttribute('lay-data')
                    }
                });
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