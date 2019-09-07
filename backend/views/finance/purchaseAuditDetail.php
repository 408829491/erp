<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
<div class="layui-card">
    <div class="layui-card-header">基础信息</div>
    <div class="layui-card-body">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">业务单号:</label>
                    <div class="layui-input-inline">
                        CGX19072500003
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">供应商:</label>
                    <div class="layui-input-inline">
                        杨梅花
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">对账单号:</label>
                    <div class="layui-input-inline">
                        CD19072600001
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">对账人员:</label>
                    <div class="layui-input-inline">

                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">创建日期:</label>
                    <div class="layui-input-inline">
                        2019-07-26 20:59:39
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结算状态:</label>
                    <div class="layui-input-inline">
                        未结算
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">对账状态:</label>
                    <div class="layui-input-inline">
                        未对账
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">单据金额:</label>
                    <div class="layui-input-inline">
                        2.00
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">对账金额:</label>
                    <div class="layui-input-inline">
                        200.00
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">差异金额:</label>
                    <div class="layui-input-inline">
                        10.00
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">收货商品清单</div>
    </div>
    <div class="layui-card">
        <table class="layui-hide" id="subList" lay-filter="subList"></table>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">备注：</div>
        <div class="layui-card-body">
            <div class="layui-inline">
                    <?=$remark ?>
            </div>
        </div>
    </div>
    <script type="text/html" id="imgTpl">
        <img style="display: inline-block; width: 50%; height: 100%;" src= '{{d.pic}}?x-oss-process=image/resize,h_50'>
    </script>
</div>

    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','form','element'], function(){
            var $ = layui.$
                ,admin = layui.admin
                ,table = layui.table
                ,laydate = layui.laydate
                ,form = layui.form
                ,element = layui.element;

            element.render();

            //table设置默认参数
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
                , limit: 50
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            //展示已知数据
            var subList = <?=json_encode($details)?>;
            table.render({
                elem: '#subList'
                ,cols: [[ //标题栏
                    {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                    ,{field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                    ,{field: 'commodity_name', title: '商品名称', minWidth: 100}
                    ,{field: 'type_name', title: '分类', minWidth: 120}
                    ,{field: 'notice', title: '描述', minWidth: 120}
                    ,{field: 'unit', title: '单位', width: 80}
                    ,{field: 'num', title: '收货数量',width: 220,totalRow: true,templet: '#numTpl'}
                    ,{field: 'price', title: '收货单价', width: 150,templet: '#priceTpl'}
                    ,{field: 'total_price', title: '收货金额', width: 190,totalRow: true,templet: '#totalPriceTpl'}
                    ,{field: 'diff_num', title: '差异数量',totalRow: true,value:'0'}
                    ,{field: 'diff_price', title: '差异单价',value:'0'}
                    ,{field: 'diff_total_price', title: '差异金额',totalRow: true,value:'0'}
                ]]
                , data: subList
                , page: false
                , toolbar: false
                , totalRow: true
                , limit: Number.MAX_VALUE
                , title: "列表"
                ,done: function(res, curr, count){
                    tableDataTemp = res;
                }
            });

            //自动查询组件
//        autocomplete.render({
//            elem: '#commodity',
//            url: 'order/order-list',
//            template_val: 'id',
//            method:'post',
//            template_txt: 'abc <span class=\'layui-badge layui-bg-gray\'>13866665747</span>',
//            onselect: function (resp) {
//
//            }
//        });

            //监听工具条
            table.on('tool(subList)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    obj.del();
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

            var action = {
                copyOrder: function(){
                    top.layui.admin.popupRight({
                        id: 'LAY_adminPopupLayerTest'
                        ,success: function(){
                            $('#'+ this.id).html('<div style="padding: 20px;">放入内容</div>');
                        }
                    });
                },
                addProductList: function(){
                    var create_window = layer.open({
                        id:'main',
                        type: 2,
                        content: '/admin/commodity-list/list',
                        area: ['1000px', '630px'],
                        title:'请选择商品',
                        maxmin: false,
                        btn: ['确定', '取消'],yes: function(index, layero){
                            var sonTable = layero.find('iframe')[0].contentWindow.layui.table.checkStatus('list').data;
                            table.reload('subList', {
                                data: layui.table.cache['subList'].concat(sonTable)
                            });
                            layer.close(index);
                        }
                    });
                },
                addProductSingle: function(){
                    alert('ok');
                },
                saveOrder:function(){
                    //保存订单数据
                }
            };


            $('.layui-btn').on('click', function(e){
                var type = $(this).data('type');
                action[type] && action[type].call(this);
                console.log(e);
            });

        });

        function test(obj)
        {
            layui.table.reload('test-table-data',obj);
        }

    </script>