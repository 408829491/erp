<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li class="layui-this"  lay-data="ORDER">订单发货单</li>
                    <li lay-data="ORDER_RETURN">订单退货单</li>
                    <li lay-data="PICK">分拣单</li>
                    <li lay-data="PUR">采购单</li>
                    <li lay-data="PUR_TAKE">采购收货单</li>
                    <li lay-data="PUR_RETURN">采购退货单</li>
                    <li lay-data="IN_STORAGE">入库单</li>
                    <li lay-data="CHECK">盘点单</li>
                    <li lay-data="SUMMARY">拣货单</li>
                    <li lay-data="SOA">对账单</li>
                </ul>
            </div>

            <div class="layui-card">
                <div class="layui-card-body">

                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>

                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                        <a class="layui-btn layui-btn-xs" lay-event="detail">导出</a>
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
                , toolbar: false
                , limit: 15
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            table.render({
                elem: '#order-list'
                ,url: 'template-list'
                ,title: '数据表'
                ,cols: [[
                    {field:'id', title:'模板ID', width:120, fixed: 'left', unresize: true, sort: true}
                    ,{field:'name', title:'模板名称', width:235}
                    ,{field:'page_width', title:'宽', width:230,sort: true}
                    ,{field:'page_height', title:'高', width:130, sort: true}
                    ,{field:'page_top', title:'上边距',width:130}
                    ,{field:'page_left', title:'左边距', width:100, sort: true}
                    ,{field:'is_show_header', title:'是否每页显示表头', width:110}
                    ,{field:'is_show_sign', title:'底部显示收货人姓名', width:150, sort: true}
                    ,{field:'update_time', title:'修改时间', width:110}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo'}
                ]]
                ,page: true
            });


            //监听行工具事件
            table.on('tool(order-list)', function(obj){
                var typeConf = obj.data.config;
                var url = 'update?TYPE_CONF='+JSON.stringify(typeConf).replace(/\"/g,"'")+'&id='+obj.data.id;
                console.log(url);

                switch(obj.event){
                    case 'edit':
                        layer.open({
                            type: 2,
                            content: url,
                            area: ['1400px', '800px'],
                            title:'编辑',
                            maxmin: true,
                            btn: ['保存', '关闭'],yes: function(index, layero){
                                var submit = layero.find('iframe').contents().find("#form-submit");
                                submit.click();
                            }
                        });
                        break;
                    case 'export':
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
                       type:_this.getAttribute('lay-data')?_this.getAttribute('lay-data'):'ORDER'
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