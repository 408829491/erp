<div class="layui-fluid">
    <div class="layui-row">
            <div class="layui-card">
                <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <select name="purchase_type">
                                    <option value="">采购类型</option>
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
                                <input type="text" name="keyword" placeholder="请输入单号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
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
                    <script type="text/html" id="order-list-toolbarDemo">
                        <div class="layui-btn-container">
                            <button class="layui-btn layui-btn-list" lay-event="getCheckData">手动新增</button>
                        </div>
                    </script>
                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
                        {{#  if(d.status == 1){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="audit" style="background-color: #77CF20">审核</a>
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        {{#  } }}
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
            , limit: 10
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        table.render({
            elem: '#order-list'
            ,url: '/admin/purchase/purchase-refund-list'
            ,toolbar: '#order-list-toolbarDemo'
            ,title: '用户数据表'
            ,cols: [[
                 {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                ,{field:'refund_no', title:'采购退货单号'}
                ,{field:'price', title:'退货金额',width:90}
                ,{field:'agent_name', title:'采购员/供应商', sort: true}
                ,{field:'purchase_no', title:'源采购单', sort: true}
                ,{field:'author', title:'制单人', width:80}
                ,{field:'status_text', title:'状态', width:100}
                ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo',width:255}
            ]]
            ,page: true
            ,done: function(){
                element.render();
            }
        });

        //头工具栏事件
        table.on('toolbar(order-list)', function(obj){
            switch(obj.event) {
                case 'getCheckData':
                    var windows = layer.open({
                        type: 2,
                        content: '/admin/purchase-refund/create',
                        area: ['800px', '830px'],
                        title: '新增退货单',
                        maxmin: true,
                        btn: ['保存返回列表页', '取消'], yes: function (index, layero) {
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(windows);
                    break;
            }
        });

        //监听行工具事件
        table.on('tool(order-list)', function(obj){
            switch(obj.event){
                case 'edit':
                    var windows_update = layer.open({
                        type: 2,
                        content: '/admin/purchase-refund/update?id=' + obj.data.id,
                        area: ['800px', '830px'],
                        title:'编辑退货单',
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
                        content: '/admin/purchase-refund/view?id='+ obj.data.id,
                        area: ['800px', '830px'],
                        title:'退货单详情',
                        maxmin: true,
                        btn: ['返回']
                    });
                    layer.full(window_detail);
                    break;
                case 'audit':
                    layer.confirm('确认要审核吗?', function(index){
                        $.get({
                            type:"POST",
                            url:"/admin/purchase-refund/finish?purchase_id="+obj.data.id,
                            dataType:"json",
                            success:function(data){
                                if(data.code=='200'){
                                    table.reload('order-list');
                                    layer.msg('审核已完成！', {icon: 1});
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