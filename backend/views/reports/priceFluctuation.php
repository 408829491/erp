<style>
    .layuiadmin-card-list span {
        color: #c9c8c7;
        padding: 5px;
    }
</style>
<div style="padding: 15px; background-color: #F2F2F2;">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto"
             lay-filter="layui-form">
            <div class="layui-form-item layui-inline">
                <div class="layui-inline">
                    <div class="layui-btn-group" lay-filter="date-tabs">
                        <button type="button" class="layui-btn layui-btn-md" lay-data="1">
                            本周
                        </button>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-md" lay-data="2">
                            本月
                        </button>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-md" lay-data="3">
                            上月
                        </button>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-md" lay-data="4">
                            本季
                        </button>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width:200px;">
                        <input type="text" name="create_time" class="layui-input"
                               id="test-laydate-range-date" placeholder="" style="width:200px;">
                    </div>
                </div>
                <div class="layui-inline">
                    <input type="text" name='name' class="layui-input" id="name" placeholder="请输入商品名称/编码/关键字"
                           style="width: 230px;" lay-verify="required" autocomplete="off">
                    <input type="hidden" name="commodity_id" id="commodity_id">
                    <input type="hidden" name="unit" id="unit">
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    价格波动表
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
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['element', 'echarts', 'carousel', 'table', 'laydate', 'form', 'yutons_sug'], function () {
        var element = layui.element,
            $ = layui.jquery,
            table = layui.table,
            laydate = layui.laydate,
            form = layui.form,
            yutons_sug = layui.yutons_sug,
            echarts = layui.echarts;

        table.set({
            page: true
            , parseData: function (res) {
                return {
                    "code": res.code,
                    "msg": res.msg,
                    "total_price": res.data.total_price,
                    "total_order_count": res.data.total_order_count,
                    "total_user_count": res.data.total_user_count,
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
            , url: '/admin/reports/get-price-fluctuation'
            , toolbar: true
            , title: '数据表'
            , cols: [[
                {field: 'date', title: '日期'}
                , {field: 'purchase_price', title: '采购价', sort: true}
                , {field: 'user_price', title: '销售价', sort: true}
            ]]
            , totalRow: true
            , page: false
            , done: function (obj) {
                var data = obj.data;
                xAxis = [];
                series_price = [];
                series_num = [];
                series_profit = [];
                for (var i = 0; i < data.length; i++) {
                    xAxis[i] = data[i]['date'];
                    series_price[i] = data[i]['purchase_price'];
                    series_num[i] = data[i]['user_price'];
                }
                // 基于准备好的dom，初始化echarts实例
                var Shopping = echarts.init(document.getElementById('Shopping'));
                // 指定图表的配置项和数据
                var optionShopping = {
                    title: {
                        text: $("#name").val() + '价格波动表'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['采购价', '销售价']
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
                            name: '采购价',
                            type: 'line',
                            data: series_price,
                        },
                        {
                            name: '销售价',
                            type: 'line',
                            data: series_num,
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


        //初始化姓名输入提示框
        yutons_sug.render({
            id: "name", //设置容器唯一id
            height: "200",
            width: "400",
            cols: [
                [{
                    field: 'name',
                    title: '商品名称'
                },
                    {
                        field: 'price',
                        title: '价格'
                    },
                    {
                        field: 'unit',
                        title: '单位'
                    }]
            ], //设置表头
            params: [
                {
                    name: 'name',
                    field: 'name'
                },
                {
                    name: 'price',
                    field: 'price'
                }, {
                    name: 'unit',
                    field: 'unit'
                }],//设置字段映射，适用于输入一个字段，回显多个字段
            type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
            url: '/admin/commodity-list/get-list-data?searchText=' //设置异步数据接口,url为必填项,params为字段名
        });

        //获取商品筛选数据
        table.on('row(yutons_sug_name)', function (obj) {
            var data = obj.data;
            table_name = data;
            $("#name").val(data.name);
            $("#commodity_id").val(data.id);
            $("#unit").val(data.unit);
            $("#yutons_sug_name").next().hide().html("");
        });


        //清除查询条件
        form.on('submit(search-clear)', function (obj) {
            form.val("layui-form", {
                'create_time': '',
                'keyword': ''
            })
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            var field = data.field;

            //执行重载
            table.reload('list', {
                where: field
            });
        });

        //按日期筛选
        $('.layui-btn-group > button').on('click', function () {
            $(this).removeClass('layui-btn-primary');
            $(this).siblings().addClass('layui-btn-primary');
            $type = $(this).attr('lay-data');
            switch ($type) {
                case '1':
                    laydate.render({
                        elem: '#test-laydate-range-date',
                        type: 'date',
                        value: getWeekStartDate() + ' - ' + getWeekEndDate(),
                        range: true
                    });
                    break;
                case '2':
                    laydate.render({
                        elem: '#test-laydate-range-date',
                        type: 'date',
                        value: getMonthStartDate() + ' - ' + getMonthEndDate(),
                        range: true
                    });
                    break;
                case '3':
                    laydate.render({
                        elem: '#test-laydate-range-date',
                        type: 'date',
                        value: getLastMonthStartDate() + ' - ' + getLastMonthEndDate(),
                        range: true
                    });
                    break;
                case '4':
                    laydate.render({
                        elem: '#test-laydate-range-date',
                        type: 'date',
                        value: getQuarterStartDate() + ' - ' + getQuarterEndDate(),
                        range: true
                    });
                    break;
            }
        });

        /**
         * 获取本周、本季度、本月、上月的开始日期、结束日期
         */
        var now = new Date(); //当前日期
        var nowDayOfWeek = now.getDay(); //今天本周的第几天
        var nowDay = now.getDate(); //当前日
        var nowMonth = now.getMonth(); //当前月
        var nowYear = now.getYear(); //当前年
        nowYear += (nowYear < 2000) ? 1900 : 0; //

        var lastMonthDate = new Date(); //上月日期
        lastMonthDate.setDate(1);
        lastMonthDate.setMonth(lastMonthDate.getMonth() - 1);
        var lastYear = lastMonthDate.getYear();
        var lastMonth = lastMonthDate.getMonth();

        //格式化日期：yyyy-MM-dd
        function formatDate(date) {
            var myyear = date.getFullYear();
            var mymonth = date.getMonth() + 1;
            var myweekday = date.getDate();

            if (mymonth < 10) {
                mymonth = "0" + mymonth;
            }
            if (myweekday < 10) {
                myweekday = "0" + myweekday;
            }
            return (myyear + "-" + mymonth + "-" + myweekday);
        }

        //获得某月的天数
        function getMonthDays(myMonth) {
            var monthStartDate = new Date(nowYear, myMonth, 1);
            var monthEndDate = new Date(nowYear, myMonth + 1, 1);
            var days = (monthEndDate - monthStartDate) / (1000 * 60 * 60 * 24);
            return days;
        }

        //获得本季度的开始月份
        function getQuarterStartMonth() {
            var quarterStartMonth = 0;
            if (nowMonth < 3) {
                quarterStartMonth = 0;
            }
            if (2 < nowMonth && nowMonth < 6) {
                quarterStartMonth = 3;
            }
            if (5 < nowMonth && nowMonth < 9) {
                quarterStartMonth = 6;
            }
            if (nowMonth > 8) {
                quarterStartMonth = 9;
            }
            return quarterStartMonth;
        }

        //获得本周的开始日期
        function getWeekStartDate() {
            var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
            return formatDate(weekStartDate);
        }

        //获得本周的结束日期
        function getWeekEndDate() {
            var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
            return formatDate(weekEndDate);
        }

        //获得本月的开始日期
        function getMonthStartDate() {
            var monthStartDate = new Date(nowYear, nowMonth, 1);
            return formatDate(monthStartDate);
        }

        //获得本月的结束日期
        function getMonthEndDate() {
            var monthEndDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth));
            return formatDate(monthEndDate);
        }

        //获得上月开始时间
        function getLastMonthStartDate() {
            var lastMonthStartDate = new Date(nowYear, lastMonth, 1);
            return formatDate(lastMonthStartDate);
        }

        //获得上月结束时间
        function getLastMonthEndDate() {
            var lastMonthEndDate = new Date(nowYear, lastMonth, getMonthDays(lastMonth));
            return formatDate(lastMonthEndDate);
        }

        //获得本季度的开始日期
        function getQuarterStartDate() {

            var quarterStartDate = new Date(nowYear, getQuarterStartMonth(), 1);
            return formatDate(quarterStartDate);
        }

        //或的本季度的结束日期
        function getQuarterEndDate() {
            var quarterEndMonth = getQuarterStartMonth() + 2;
            var quarterStartDate = new Date(nowYear, quarterEndMonth, getMonthDays(quarterEndMonth));
            return formatDate(quarterStartDate);
        }

        //日期范围
        laydate.render({
            elem: '#test-laydate-range-date',
            type: 'date',
            value: getWeekStartDate() + ' - ' + getWeekEndDate(),
            range: true
        });

    });
</script>
