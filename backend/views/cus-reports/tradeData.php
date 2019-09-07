<div style="padding: 15px; background-color: #F2F2F2;">

    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    营业趋势分析
                    <div class="layui-btn-group layuiadmin-btn-group">
                        <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">查看详情</a>
                    </div>
                </div>
                <div class="layui-card-body">
                    <div style="width: 100%; height: 300px;" id="Shopping">
                    </div>
                    <table class="layui-hide" id="list" lay-filter="list"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    //注意：折叠面板 依赖 element 模块，否则无法进行功能性操作
    layui.config({
        version: 1,
        base: '/admin/plugins/layuiadmin/lib/extend/' //静态资源所在路径
    }).use(['element', 'echarts', 'carousel', 'table'], function () {
        var element = layui.element,
            $ = layui.jquery,
            table = layui.table,
            echarts = layui.echarts;

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
            elem: '#list'
            , url: '/admin/cus-reports/get-trade-data-list'
            , toolbar: true
            , title: '用户数据表'
            , cols: [[
                {field: 'id', title: '序号', width: 80, fixed: 'left', unresize: true, sort: true,totalRowText: '合计:'}
                , {field: 'date', title: '时间'}
                , {field: 'order_count', title: '销售单数', sort: true,totalRow: true}
                , {field: 'commodity_count', title: '商品数量', sort: true,totalRow: true}
                , {field: 'total_price', title: '销售总额', sort: true,totalRow: true}
                , {field: 'total_profit', title: '利润', sort: true,totalRow: true}
                , {field: 'total_profit_radio', title: '毛利率', sort: true,totalRow: true}
            ]]
            , totalRow:true
            , page: false
            ,done:function (obj) {
                var data = obj.data;
                xAxis =[];
                series_price =[];
                series_num=[];
                series_profit = [];
                for (var i = 0; i < data.length; i++) {
                    xAxis[i] = data[i]['date'];
                    series_price[i] = data[i]['total_price'];
                    series_num[i] = data[i]['order_count'];
                    series_profit[i] = data[i]['total_profit'];
                }
                // 基于准备好的dom，初始化echarts实例
                var Shopping = echarts.init(document.getElementById('Shopping'));
                // 指定图表的配置项和数据
                var optionShopping = {
                    title: {
                        text: '销售趋势分析'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['销售总额', '销售单量', '利润']
                    },
                    toolbox: {
                        show: true,
                        feature: {
                            mark: {show: true},
                            dataView: {show: true, readOnly: false},
                            magicType: {show: true, type: ['line', 'bar']},
                            restore: {show: true},
                            saveAsImage: {show: true}
                        }
                    },
                    calculable: true,
                    xAxis: [
                        {
                            type: 'category',
                            data: xAxis
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value'
                        }
                    ],
                    series: [
                        {
                            name: '销售总额',
                            type: 'bar',
                            data: series_price,
                            markPoint: {
                                data: [
                                    {type: 'max', name: '最大值'},
                                    {type: 'min', name: '最小值'}
                                ]
                            },
                        },
                        {
                            name: '销售单量',
                            type: 'bar',
                            data: series_num,
                            markPoint: {
                                data: [
                                    {name: '月最高', value: 16182.2, xAxis: 7, yAxis: 183, symbolSize: 18},
                                    {name: '月最低', value: 233.3, xAxis: 11, yAxis: 3}
                                ]
                            },
                            markLine: {
                                data: [
                                    {type: 'average', name: '平均值'}
                                ]
                            }
                        },
                        {
                            name: '利润',
                            type: 'bar',
                            data: series_profit,
                            markPoint: {
                                data: [
                                    {type: 'max', name: '最大值'},
                                    {type: 'min', name: '最小值'}
                                ]
                            },
                        }
                    ]
                };
                // 使用刚指定的配置项和数据显示图表。
                Shopping.setOption(optionShopping);
                window.onresize = function () {
                    Shopping.resize();
                };
            }
        });



    });
</script>
