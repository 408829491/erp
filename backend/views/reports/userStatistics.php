<div style="padding: 15px; background-color: #F2F2F2;">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto"
             lay-filter="layui-form">
            <div class="layui-form-item">
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
                               id="test-laydate-range-date" placeholder="" style="width:210px;">
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

    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    订货单
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font" id="order">0</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    退货单
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font" id="refund">0</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    订单金额
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font" id="order_price">0</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    退货金额
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font" id="order_refund">0</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">
                    金额合计
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font" id="summary">0</p>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6 layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div style="width: 100%; height: 300px;" id="Shopping">
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div style="width: 100%; height: 300px;" id="Refund">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
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
    }).use(['element', 'echarts', 'carousel', 'table','laydate','form'], function () {
        var element = layui.element,
            $ = layui.jquery,
            table = layui.table,
            laydate = layui.laydate,
            form = layui.form,
            echarts = layui.echarts;

        table.set({
            page: true
            , parseData: function (res) {
                return {
                    "code": res.code,
                    "msg": res.msg,
                    "order": res.data.order,
                    "refund": res.data.refund,
                    "order_price": res.data.order_price,
                    "refund_price": res.data.refund_price,
                    "summary": res.data.summary,
                    "data": res.data.list
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
            , url: '/admin/reports/get-user-statistics'
            , toolbar: true
            , title: '用户数据表'
            , cols: [[
                {field: 'id', title: '序号', width: 80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                , {field: 'nick_name', title: '客户名称'}
                , {field: 'total_count', title: '订单笔数', sort: true}
                , {field: 'total_refund_count', title: '退货笔数', sort: true}
                , {field: 'total_price', title: '订货金额', sort: true}
                , {field: 'total_refund_price', title: '退货金额', sort: true}
                , {field: 'summary', title: '金额小计', sort: true, totalRow: true}
                , {field: 'rank', title: '综合排名', sort: true}
            ]]
            , totalRow: true
            , page: false
            , done: function (obj) {
                // 基于准备好的dom，初始化echarts实例
                $('#order').text(obj.order);
                $('#refund').text(obj.refund);
                $('#refund_price').text(obj.refund_price);
                $('#order_price').text(obj.order_price);
                $('#summary').text(obj.summary);
                var data = obj.data;
                legend =[];
                series =[];
                series_refund = [];
                for (var i = 0; i < data.length; i++) {
                    legend[i] = data[i]['nick_name'];
                    series[i] = {"name":data[i]['nick_name'],"value":data[i]['total_price']}
                    series_refund[i] = {"name":data[i]['nick_name'],"value":data[i]['total_refund_price']}
                }
                var Shopping = echarts.init(document.getElementById('Shopping'));
                var Refund = echarts.init(document.getElementById('Refund'));
                // 指定图表的配置项和数据
                var optionShopping =  {
                    title : {
                        text: '订货金额',
                        subtext: '客户订购金额',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:legend
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {
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
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'访问来源',
                            type:'pie',
                            radius : '55%',
                            center: ['50%', '60%'],
                            data:series
                        }
                    ]
                };

                var optionRefund =  {
                    title : {
                        text: '退货金额',
                        subtext: '客户退换货金额',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:legend
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {
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
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'统计',
                            type:'pie',
                            radius : '55%',
                            center: ['50%', '60%'],
                            data:series_refund
                        }
                    ]
                };
                // 使用刚指定的配置项和数据显示图表。
                Shopping.setOption(optionShopping);
                Refund.setOption(optionRefund);
                window.onresize = function () {
                    Shopping.resize();
                    Refund.resize();
                    Notice.resize();
                    NoticeAll.resize();
                };
            }
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
            table.reload('list', {
                where: field
            });
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
