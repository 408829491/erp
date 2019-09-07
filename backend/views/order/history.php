<div class="layui-fluid">
    <div class="layui-row">
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
                                   id="test-laydate-range-date" placeholder="" style="width:200px;">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="width:300px;">
                            <input type="text" name="nickname" id="nickname" placeholder="输入客户名称进行模糊搜索" autocomplete="off" class="layui-input" style="width:310px" lay-verify="required" >
                            <input type="hidden" name="user_id" id="user_id">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="请输入商品编码\名称\助记码\别名" autocomplete="off" class="layui-input" style="width: 300px;">
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
                    <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">
                        <button class="layui-btn  layui-btn-normal" data-type="export" style="background-color: #77CF20" lay-submit lay-filter="export" id="export">导出</button>
                    </div>

                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-xs5">
                            商品汇总
                            <hr>
                            <div class="grid-demo grid-demo-bg1">
                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                            </div>
                        </div>
                        <div class="layui-col-xs7">
                            商品明细
                            <hr>
                            <div class="grid-demo">
                                <table class="layui-hide" id="order-list2" lay-filter="order-list2"></table>
                            </div>
                        </div>
                    </div>
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
        }).use(['index', 'table', 'laydate','element','form','yutons_sug'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,yutons_sug  = layui.yutons_sug
                ,form = layui.form;

            table.set({
                page: true
                , parseData: function (res) {
                    return {
                        "code": 0,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.list
                    }
                }
                , request: {
                    pageName: 'pageNum',
                    limitName: 'pageSize'
                }
                , response: {
                    statusCode: 0
                }
                , toolbar: true
                , limit: 50
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            table.render({
                elem: '#order-list'
                ,url: '/admin/order/order-commodity-list-data'
                ,title: '商品汇总'
                ,cols: [[
                    {field:'name', title:'商品'}
                    ,{field:'unit', title:'单位'}
                    ,{field:'num', title:'发货数量'}
                    ,{field:'total_price', title:'金额'}
                ]]
                ,toolbar:false
                ,page:false
            });

            table.render({
                elem: '#order-list2'
                ,title: '用户数据表'
                ,url: '/admin/order/order-commodity-detail-data'
                ,cols: [[
                    {field:'delivery_date', title:'发货日期'}
                    ,{field:'name', title:'商品'}
                    ,{field:'unit', title:'单位', sort: true}
                    ,{field:'num', title:'发货数量'}
                    ,{field:'refund_num', title:'退货数量', sort: true}
                    ,{field:'total_price', title:'实际金额'}
                ]]
                ,toolbar:false
                ,page:false
            });

            //头工具栏事件
            table.on('toolbar(order-list)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id);
                switch(obj.event) {
                    case 'getCheckData':
                        var windows = layer.open({
                            type: 2,
                            content: '/admin/order/create',
                            area: ['800px', '830px'],
                            title: '新增手工订单',
                            maxmin: true,
                            btn: ['保存并继续新增', '保存返回列表页', '取消'], yes: function (index, layero) {
                                //点击确认触发 iframe 内容中的按钮提交
                                var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                                submit.click();
                            }
                        });
                        layer.full(windows);
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


            //监听行点击
            table.on('row(order-list)', function(obj){
                var data = obj.data;
                table.reload('order-list2', {
                    where: data
                });
                //标注选中样式
                obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
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

            //导出
            form.on('submit(export)', function(data){
                var field = data.field;
                console.log(field);
                document.location.href = '/admin/order/export-user-order-detail?field='+ JSON.stringify(field);
            });


            //监听搜索
            form.on('submit(search)', function(data){
                var field = data.field;
                //执行重载
                table.reload('order-list', {
                    where: field
                });
                table.reload('order-list2', {
                    where: []
                });
            });

            yutons_sug.render({
                id: "nickname", //设置容器唯一id
                height: "200",
                width:"400",
                cols: [
                    [{
                        field: 'nickname',
                        title: '客户名称'
                    },
                        {
                            field: 'username',
                            title: '联系电话'
                        },
                        {
                            field: 'address',
                            title: '地址'
                        }]
                ], //设置表头
                params: [
                    {
                        name: 'nickname',
                        field: 'nickname'
                    },
                    {
                        name: 'username',
                        field: 'username'
                    }, {
                        name: 'address',
                        field: 'address'
                    }],//设置字段映射，适用于输入一个字段，回显多个字段
                type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
                url: '/admin/user/get-user-list?keyword=' //设置异步数据接口,url为必填项,params为字段名
            });

            //获取客户筛选数据
            table.on('row(yutons_sug_nickname)', function(obj) {
                var data = obj.data;
                //console.log(data);
                $("#nickname").val(data.nickname);
                $("#user_id").val(data.id);
                $("#yutons_sug_nickname").next().hide().html("");
                table.reload('order-list2', {
                    where: []
                });
                table.reload('order-list', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    ,where: {
                        id:data.id
                    }
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