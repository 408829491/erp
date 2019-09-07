
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="is_settlement">
                                <option value="">结算状态</option>
                                <option value="0">未结算</option>
                                <option value="2">部分结算</option>
                                <option value="1">已结算</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="create_time" class="layui-input"  id="test-laydate-range-date" placeholder="请选择创建日期">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入供应商名称搜索" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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
                        {{#  if(d.is_settlement == '0'){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="audit" style="background-color: #77CF20">对账</a>
                        <a class="layui-btn layui-btn-xs" lay-event="settlement" style="background-color: #77CF20">结算</a>
                        {{#  } }}
                        {{#  if(d.is_settlement == '2'){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="settlement" style="background-color: #77CF20">结算</a>
                        {{#  } }}
                        {{#  if(d.is_settlement == '1'){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="settleRecord" style="background-color: #1e9fff">结算记录</a>
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
        }).use(['table', 'laydate','element','form'], function(){
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
                ,url: '/admin/finance/purchase-list'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '采购结算'
                ,cols: [[
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'purchase_no', title:'业务单号'}
                    ,{field:'purchase_type_text', title:'采购类型',sort: true,width:110}
                    ,{field:'plan_date', title:'交货日期', width:120}
                    ,{field:'agent_name', title:'采购员/供应商', sort: true}
                    ,{field:'audit_type', title:'单据类型', width:100, sort: true,templet:function(e){
                        if(e.audit_type === '0')
                            return '采购收货';
                        else
                            return '采购退货';
                    }}
                    ,{field:'audit_price', title:'应付金额',width:90}
                    ,{field:'settle_price', title:'已付金额',width:90}
                    ,{field:'no_pay_price', title:'未付金额',width:90}
                    ,{field:'audit_no', title:'对账单号',width:90}
                    ,{field:'author', title:'对账人',width:90}
                    ,{field:'is_audit', title:'对账状态', width:100,templet:function(e){
                        if(e.is_audit === '0')
                            return '<div style="color:red">未对账</div>';
                        else
                            return '<div style="color:green">已对账</div>';
                    }}
                    ,{field:'is_settlement', title:'结算状态', width:100,templet:function(e){
                        if(e.is_settlement === '0')
                            return '<div style="color:red">未结算</div>';
                        else if(e.is_settlement === '2')
                            return '<div style="color:red">部分结算</div>';
                        else
                            return '<div style="color:green">已结算</div>';
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
                            content: '/admin/finance/purchase-audit-detail?id='+ obj.data.id,
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
                            content: '/admin/finance/purchase-settlement?id=' + obj.data.id,
                            area: ['800px', '830px'],
                            title:'结算',
                            maxmin: true,
                            btn: ['确认付款', '取消'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows_settlement);
                        break;
                    case 'audit':
                        var windows_audit = layer.open({
                            type: 2,
                            content: '/admin/finance/purchase-audit?id=' + obj.data.id,
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
                    case 'settleRecord':
                        layer.open({
                            type: 2,
                            content: '/admin/finance/purchase-settlement-record?refer_no=' + obj.data.purchase_no,
                            area: ['80%', '630px'],
                            title:'对账',
                            maxmin: true,
                            btn: ['确定', '取消'],yes: function(index, layero){
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