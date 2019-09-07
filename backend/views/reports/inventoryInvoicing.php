<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
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
                            <input type="text" name="create_time" class="layui-input"
                                   id="test-laydate-range-date" placeholder="" style="width:210px;">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select lay-filter="type" name="typeId">
                                <option value="">商品分类</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="输入商品名称/条码/关键字" autocomplete="off" class="layui-input" id="commodity" style="width:300px;">
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
                    <script type="text/html" id="triangle">
                        <i class="layui-icon layui-icon-triangle-r"></i>
                    </script>

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
        }).use(['index', 'table', 'laydate','element','form','admin'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,admin = layui.admin
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
                , limit: 30
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            table.render({
                elem: '#order-list'
                ,url: '/admin/reports/get-inventory-invoicing-statistics'
                ,title: '进销存报表'
                ,cols: [[
                    {field:'id', title:'ID', sort: true,width:60}
                    ,{field:'name', title:'商品名称',width:180}
                    ,{field:'type_name', title:'分类名称'}
                    ,{field:'total_count', title:'销售数量'}
                    ,{field:'unit', title:'库存单位'}
                    ,{field:'early_stock', title:'期初库存'}
                    ,{field:'early_price', title:'期初金额'}
                    ,{field:'in_count', title:'入库数量'}
                    ,{field:'in_price', title:'入库金额'}
                    ,{field:'out_count', title:'出库数量'}
                    ,{field:'out_price', title:'出库成本'}
                    ,{field:'loss_count', title:'报损数量'}
                    ,{field:'loss_price', title:'报损金额'}
                    ,{field:'overflow_count', title:'报溢数量'}
                    ,{field:'overflow_price', title:'报溢金额'}
                ]]
                ,page: true
            });

            // 获取分类
            var typeDataList;

            admin.req({
                type: "get"
                , url: '/admin/commodity-category/first-tier-data'
                , dataType: "json"
                , cache: false
                , data: {}
                , done: function (res) {
                    typeDataList = res.data;
                    for (var i = 0; i < typeDataList.length; i++) {
                        $("[name=typeId]").append('<option value="' + typeDataList[i].id + '">' + typeDataList[i].name + '</option>');
                    }
                    form.render();
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
                table.reload('order-list', {
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