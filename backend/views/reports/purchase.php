<style>
    .layuiadmin-card-list span {
        color: #c9c8c7;
        padding: 5px;
    }
</style>
<div style="padding: 15px; background-color: #F2F2F2;">
    <div class="layui-card">
        <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
            <ul class="layui-tab-title">
                <li lay-data="1" class="layui-this">按明细</li>
                <li lay-data="2">按商品</li>
                <li lay-data="3">按供应商</li>
                <li lay-data="4">按采购员</li>
            </ul>
            <div class="layui-tab-content" style="padding: 0">
            </div>
        </div>
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
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
                        <input type="text" name="create_time" class="layui-input" id="test-laydate-range-date"
                               placeholder="" style="width:210px;">
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
                    采购单
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">
                    <p class="layuiadmin-big-font"><?= $total_count ?></p><span> 个</span>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    商品种类
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">
                    <p class="layuiadmin-big-font"><?= $total_type_count ?></p><span> 种</span>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    采购总金额
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">
                    <p class="layuiadmin-big-font"><?= $total_purchase_price ?></p><span> 元</span>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    采收退货
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">

                    <p class="layuiadmin-big-font"><?= $total_refund_count ?></p><span> 个</span>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    退货总金额
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">
                    <p class="layuiadmin-big-font"><?= $total_refund_price ?></p></p><span> 元</span>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md2">
            <div class="layui-card">
                <div class="layui-card-header">
                    金额合计
                </div>
                <div class="layui-card-body layuiadmin-card-list" style="display: flex;align-items: baseline">

                    <p class="layuiadmin-big-font"><?= $total_price ?></p></p><span> 元</span>
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
    }).use(['element', 'echarts', 'carousel', 'table', 'laydate','form'], function () {
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

        var config = {
            elem: '#list'
            , url: '/admin/reports/get-purchase-statistics'
            , toolbar: true
            , title: '按明细'
            , cols: [[
                {field: 'purchase_no', title: '采购单号', width: 230}
                , {field: 'create_time', title: '采购时间'}
                , {field: 'purchase_type', title: '业务类型'}
                , {field: 'in_create_time', title: '收货时间'}
                , {field: 'in_no', title: '出/入库单号'}
                , {field: 'commodity_name', title: '商品', sort: true, width: 200}
                , {field: 'unit', title: '单位', width: 80}
                , {field: 'purchase_price', title: '单价', sort: true, totalRow: true}
                , {field: 'purchase_num', title: '收货数量', sort: true, totalRow: true}
                , {field: 'total_price', title: '小计', sort: true, totalRow: true}
            ]]
            , totalRow: true
            , page: true
            , done: function (obj) {
            }
        };

        table.render(config);
        //监听tab页
        element.on('tab(component-tabs)', function () {
            var id = this.getAttribute('lay-data');
            switch (id) {
                case '1':
                    table.render(config);
                    table.reload();
                    break;
                case '2':
                    table.render(
                        {
                            elem: '#list'
                            , url: '/admin/reports/get-purchase-statistics-by-commodity'
                            , toolbar: true
                            , title: '按商品'
                            , cols: [[
                            {field: 'commodity_id', title: '商品编码'}
                            , {field: 'type_name', title: '分类'}
                            , {field: 'commodity_name', title: '商品'}
                            , {field: 'unit', title: '单位'}
                            , {field: 'average_price', title: '入库均价'}
                            , {field: 'total_count', title: '入库数量', sort: true}
                            , {field: 'total_price', title: '小计', sort: true}
                        ]]
                            , totalRow: false
                            , page: false
                            , done: function (obj) {
                        }
                        }
                    );
                    table.reload();
                    break;
                case '3':
                    table.render(
                        {
                            elem: '#list'
                            , url: '/admin/reports/get-purchase-statistics-by-provider'
                            , toolbar: true
                            , title: '按供应商'
                            , cols: [[
                            {
                                field: 'id',
                                title: '序号',
                                width: 80,
                                fixed: 'left',
                                unresize: true,
                                sort: true,
                                totalRowText: '合计:'
                            }
                            , {field: 'agent_name', title: '供应商'}
                            , {field: 'commodity_name', title: '商品'}
                            , {field: 'unit', title: '单位'}
                            , {field: 'average_price', title: '入库均价'}
                            , {field: 'total_count', title: '入库数量'}
                            , {field: 'total_price', title: '小计', sort: true, totalRow: true}
                        ]]
                            , totalRow: true
                            , page: false
                            , done: function (obj) {
                        }
                        }
                    );
                    table.reload();
                    break;
                case '4':
                    table.render(
                        {
                            elem: '#list'
                            , url: '/admin/reports/get-purchase-statistics-by-buyer'
                            , toolbar: true
                            , title: '按采购员'
                            , cols: [[
                            {
                                field: 'id',
                                title: '序号',
                                width: 80,
                                fixed: 'left',
                                unresize: true,
                                sort: true,
                                totalRowText: '合计:'
                            }
                            , {field: 'agent_name', title: '采购员'}
                            , {field: 'commodity_name', title: '商品'}
                            , {field: 'unit', title: '单位'}
                            , {field: 'average_price', title: '入库均价'}
                            , {field: 'total_count', title: '入库数量', sort: true}
                            , {field: 'total_price', title: '小计', sort: true, totalRow: true}
                        ]]
                            , totalRow: true
                            , page: false
                            , done: function (obj) {
                        }
                        }
                    );
                    table.reload();
                    break;
            }

        });
        //监听搜索
        form.on('submit(search)', function(data){
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
