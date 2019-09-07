<div class="layui-fluid">
    <div class="layui-row">
            <div class="layui-card">
                <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <select name="purchase_type">
                                    <option value="-1">采购类型</option>
                                    <option value="0">市场自采</option>
                                    <option value="1">供应商供货</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date" placeholder="请选择单据日期">
                            </div>
                        </div>
                        <div class="layui-inline">
                                <input type="text" name="keyword" placeholder="输入采购员/单号" autocomplete="off" class="layui-input" id="commodity" style="width: 200px;">
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                            </button>
                        </div>
                    </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">

                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>

                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="refund">选择退货</a>
                    </script>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="progressTpl">
        <div class="layui-progress" style="margin-top:12px;">
            <div class="layui-progress-bar" lay-percent="{{#  if(d.status == 3){ }}100%{{#  } }}{{#  if(d.status == 2){ }}50%{{#  } }}{{#  if(d.status == 1){ }}20%{{#  } }}"></div>
        </div>
    </script>
    <script src="/admin/plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/pFuncs.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/printCommon.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/publicPrinter.js"></script>
    <script src="/admin/plugins/cdsPrint/js/print/pur.js"></script>
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
            , limit: 8
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        table.render({
            elem: '#order-list'
            ,url: '/admin/purchase/purchase-list?status=3'
            ,toolbar: false
            ,title: '用户数据表'
            ,cols: [[
                 {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                ,{field:'purchase_no', title:'采购单号'}
                ,{field:'author', title:'制单人', width:80}
                ,{field:'status_text', title:'状态', width:100}
                ,{field:'plan_date', title:'计划交货日期', width:120}
                ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo',width:108}
            ]]
            ,page: true
            ,done: function(){
                element.render();
            }
        });

        //监听行工具事件
        table.on('tool(order-list)', function(obj){
            switch(obj.event){
                case 'refund':
                    parent.layui.table.reload('subList',{
                        url: '/admin/purchase/get-purchase-detail',
                        where: {id:obj.data.id}
                    });
                    parent.layer.close(parent.layer.index);
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