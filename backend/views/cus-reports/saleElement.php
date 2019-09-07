<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li lay-data="1" class="layui-this">营业占比分析</li>
                    <li>图表展示</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-card">
                        <div class="layui-form layui-card-header layuiadmin-card-header-auto"
                             lay-filter="layui-form">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width:300px;">
                                        <input type="text" name="create_time" class="layui-input"
                                               id="test-laydate-range-date" placeholder="" style="width:300px;">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item layui-show">
                        <div class="layui-card">
                            <div class="layui-card-body">

                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                                <script type="text/html" id="triangle">
                                    <i class="layui-icon layui-icon-triangle-r"></i>
                                </script>

                            </div>
                        </div>
                    </div>

                    <div class="layui-tab-item">
                        <div class="layui-card">
                            <div class="layui-card-body">
                                <div style="width: 1524px; height: 800px;" id="Shopping"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="order-list-barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="detail">趋势分析</a>
    </script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate', 'element', 'form', 'admin', 'echarts'], function () {
            var table = layui.table
                , laydate = layui.laydate
                , element = layui.element
                , echarts = layui.echarts
                , admin = layui.admin
                , form = layui.form;

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
                , url: '/admin/cus-reports/get-sale-element-data'
                , title: '订单汇总表'
                , cols: [[
                    {field:'id', title:'序号', sort: true,width:100}
                    , {field: 'type_name', title: '商品分类', width: 230, sort: true}
                    , {field: 'order_counts', title: '销售单数', sort: true}
                    , {field: 'total_num', title: '商品数量'}
                    , {field: 'price', title: '商品原价'}
                    , {field: 'total_price', title: '销售额',sort: true}
                    , {field: 'price_radio', title: '营业占比',sort: true}
                    , {field: 'total_profit', title: '利润'}
                    , {field: 'unit_price', title: '笔单价'}
                ]]
                , page: false
                , done:function (obj) {
                    // 基于准备好的dom，初始化echarts实例
                    var data = obj.data;
                    legend =[];
                    series =[];
                    for (var i = 0; i < data.length; i++) {
                        legend[i] = data[i]['type_name'];
                        series[i] = {"name":data[i]['type_name'],"value":data[i]['total_price']}
                    }
                    var Shopping = echarts.init(document.getElementById('Shopping'));
                    // 指定图表的配置项和数据
                    var optionShopping = {
                        title: {
                            text: '占比统计（营业额）-商品分类',
                            subtext: '',
                            x: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient: 'vertical',
                            x: 'left',
                            data: legend
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                mark: {show: true},
                                dataView: {show: true, readOnly: false},
                                magicType: {
                                    show: true,
                                    type: ['pie', 'funnel'],
                                    option: {
                                        funnel: {
                                            x: '25%',
                                            width: '50%',
                                            funnelAlign: 'left',
                                            max: 1548
                                        }
                                    }
                                },
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        calculable: true,
                        series: [
                            {
                                name: '占比统计',
                                type: 'pie',
                                radius: '55%',
                                center: ['50%', '60%'],
                                data: series
                            }
                        ]
                    };
                    // 使用刚指定的配置项和数据显示图表。
                    Shopping.setOption(optionShopping);
                    window.onresize = function () {
                        Shopping.resize();
                        Notice.resize();
                        NoticeAll.resize();
                    };
                }
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date',
                value: '<?=date("Y-m-d", time())?>' + ' 00:00:00 - ' + '<?=date("Y-m-d", time() + 1)?>' + ' 23:59:59',
                type: 'datetime',
                range: true
            });

            //清除查询条件
            form.on('submit(search-clear)', function (obj) {
                form.val("layui-form", {
                    'delivery_date': '',
                    'is_pay': '',
                    'create_time': '',
                    'keyword': '',
                    'source': ''
                })
            });

            //监听搜索
            form.on('submit(search)', function (data) {
                var field = data.field;

                //执行重载
                table.reload('order-list', {
                    where: field
                });
            });


        });
    </script>