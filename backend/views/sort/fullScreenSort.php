<link rel="stylesheet" href="/admin/resources/css/fullScreenSortWidth1920.css">

<style media="screen">
    @media screen and (max-device-width:1366px){
        .body_div_div2_div2_div_list {
            overflow-x: hidden;
            overflow-y: auto;
            -ms-overflow-style: none;
            overflow: -moz-scrollbars-none;
            height: 600px;
        }

        .body_div_div2_div1_other {
            overflow-x: hidden;
            overflow-y: auto;
            -ms-overflow-style: none;
            overflow: -moz-scrollbars-none;
            height: 607px;
        }
    }
</style>

<div class="body_div">
    <div class="body_div_div">
        <div class="body_div_div_div1">
            <image src="/admin/imgs/return.png" class="body_div_div_div1_image"></image>
            <span class="body_div_div_div1_text">返回</span>
        </div>
        <div class="body_div_div_div2">
            <input class="layui-input" style="width: 97%;">
        </div>
        <div class="body_div_div_div3">
            筛选
        </div>
        <div class="body_div_div_div4">
            一键分拣
        </div>
    </div>
    <div class="body_div_div2">
        <div class="body_div_div2_div1">
            <div class="body_div_div2_div1_div">
                <div class="body_div_div2_div1_div_icon">
                    <img src="/admin/imgs/selected.png" class="body_div_div2_div1_div_icon_img">
                </div>
                <div class="body_div_div2_div1_div_text">全部</div>
            </div>
            <div class="body_div_div2_div1_other">

            </div>
        </div>
        <div class="body_div_div2_div2">
            <div class="body_div_div2_div2_div">
                <div class="body_div_div2_div2_div_div">
                    发货日期：
                    <div class="body_div_div2_div2_div_div_text"></div>
                </div>
                <div class="body_div_div2_div2_div_list">

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 筛选 -->
<div class="limit_div">
    <div class="limit_div_div"></div>
    <div class="limit_div_div2 layui-form">
        <div class="limit_div_div2_div">
            <div class="limit_div_div2_div_div">发货日期</div>
            <div class="limit_div_div2_div_div2">
                <div class="limit_div_div2_div_div2_div1" name="dateSelect" data-type="0">昨日</div>
                <div class="limit_div_div2_div_div2_div2" name="dateSelect" data-type="1">今日</div>
                <div class="limit_div_div2_div_div2_div3" name="dateSelect" data-type="2">明日</div>
                <div class="limit_div_div2_div_div2_div4">
                    <input type="text" id="sendDate" class="layui-input limit_div_div2_div_div2_div4_input"/>
                </div>
            </div>
            <div class="limit_div_div2_div_div3">分拣状态</div>
            <div class="limit_div_div2_div_div4">
                <div class="limit_div_div2_div_div4_div1 sortStatusSelected" data-is-sorted="" name="sortStatus">全部</div>
                <div class="limit_div_div2_div_div4_div2" data-is-sorted="1" name="sortStatus">已分拣</div>
                <div class="limit_div_div2_div_div4_div3" data-is-sorted="0" name="sortStatus">未分拣</div>
            </div>
            <!--<div class="limit_div_div2_div_div5">是否标品</div>
            <div class="limit_div_div2_div_div6">
                <div class="limit_div_div2_div_div6_div1 isStandardSelected" name="isRough">全部</div>
                <div class="limit_div_div2_div_div6_div2" name="isRough">标品</div>
                <div class="limit_div_div2_div_div6_div3" name="isRough">非标品</div>
            </div>-->
            <!--<div class="limit_div_div2_div_div7">更多筛选条件</div>
            <div class="limit_div_div2_div_div8">
                <div class="limit_div_div2_div_div8_div2">
                    <select lay-filter="parentType" name="type_first_tier_id">
                        <option value="">请选择路线</option>
                    </select>
                </div>
            </div>-->
            <div class="limit_div_div2_div_div9">
                <div class="limit_div_div2_div_div9_div1">取消</div>
                <div class="limit_div_div2_div_div9_div2">确定</div>
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
            ,form = layui.form
            ,admin = layui.admin
            ,$ = layui.$;

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
            , limit: 100
            , limits: [100, 200, 300]
            , text: {
                none: '暂无相关数据'
            }
        });

        var tomorrow = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);

        // 格式化日期
        function formatDate(date) {
            var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
            var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
            return date.getFullYear() + '-' + month + '-' + day;
        }

        var deliveryDate = formatDate(tomorrow);
        var isSorted = "";
        var searchText = "";

        // 初始化加载数据
        function initData() {
            admin.req({
                type: "post"
                , url: '/admin/sort/find-class-and-product'
                , dataType: "json"
                , cache: false
                , data: {
                    filterProperty: JSON.stringify({
                        delivery_date: deliveryDate,
                        is_sorted: isSorted,
                        searchText: searchText
                    })
                }
                , done: function (res) {
                    // 添加一级分类
                    $('.body_div_div2_div1_other').html('');
                    res.data.firstTierClassList.forEach(function (res) {
                        $('.body_div_div2_div1_other').append('<div class="body_div_div2_div1_div" data-type-first-tier-id="' + res.type_first_tier_id + '">\n' +
                            '                <div class="body_div_div2_div1_div_icon">\n' +
                            '                    <img src="/admin/imgs/unselected.png" class="body_div_div2_div1_div_icon_img">\n' +
                            '                </div>\n' +
                            '                <div class="body_div_div2_div1_div_text">' + res.parent_type_name + '</div>\n' +
                            '            </div>');
                    });

                    // 添加二级分类
                    $('.body_div_div2_div2_div_list').html('');
                    res.data.secondTierAndDataList.forEach(function (res) {
                        $('.body_div_div2_div2_div_list').append('<div class="body_div_div2_div2_div_div2">\n' +
                            '                        <div class="body_div_div2_div2_div_div2_line"></div>\n' +
                            '                        <div class="body_div_div2_div2_div_div2_div">\n' +
                            '                            ' + res.parent_type_name + '—' + res.type_name + '\n' +
                            '                            <div class="body_div_div2_div2_div_div2_div_div2" data-type-id="' + res.type_id + '">展开</div>\n' +
                            '                        </div>\n' +
                            '                        <div class="body_div_div2_div2_div_div2_div2" name="dataList">\n' +
                            '                        </div>\n' +
                            '                    </div>');
                    });
                }
            });
        }

        initData();

        // 切换一级分类
        $('body').on('click', '.body_div_div2_div1_div', function () {
            var that = this;

            // 根据一级分类查询数据
            admin.req({
                type: "post"
                , url: '/admin/sort/find-data-by-first-tier'
                , dataType: "json"
                , data: {
                    filterProperty: JSON.stringify({
                        delivery_date: deliveryDate,
                        is_sorted: isSorted,
                        searchText: searchText,
                        type_first_tier_id: $(this).attr('data-type-first-tier-id')
                    })
                }
                , cache: false
                , done: function (res) {
                    // 添加二级分类节点
                    $('.body_div_div2_div2_div_list').html('');
                    res.data.forEach(function (res2) {
                        $('.body_div_div2_div2_div_list').append('<div class="body_div_div2_div2_div_div2">\n' +
                            '                        <div class="body_div_div2_div2_div_div2_line"></div>\n' +
                            '                        <div class="body_div_div2_div2_div_div2_div">\n' +
                            '                            ' + res2.parent_type_name + '—' + res2.type_name + '\n' +
                            '                            <div class="body_div_div2_div2_div_div2_div_div2" data-type-id="' + res2.type_id + '">展开</div>\n' +
                            '                        </div>\n' +
                            '                        <div class="body_div_div2_div2_div_div2_div2" name="dataList">\n' +
                            '                        </div>\n' +
                            '                    </div>');
                    });
                    // 找到选中的切换未选中
                    $('.body_div_div2_div1_div').each(function () {
                        $(this).find('img').prop('src', '/admin/imgs/unselected.png');
                    });
                    $(that).find('img').prop('src', '/admin/imgs/selected.png');
                }
            });
        });

        // 弹出筛选
        $('.body_div_div_div3').on('click', function () {
            $('.limit_div').css('display', 'flex');
        });

        // 页面显示明日的收货日期
        $('.body_div_div2_div2_div_div_text').html(formatDate(tomorrow));

        // 筛选绑定日期
        laydate.render({
            elem: '#sendDate'
            , value:new Date(tomorrow)
        });

        // 取消关闭筛选面板
        $('.limit_div_div2_div_div9_div1').on('click', function () {
            $('.limit_div').css('display', 'none');
        });

        // 筛选确定
        $('.limit_div_div2_div_div9_div2').on('click', function () {
            deliveryDate = $('#sendDate').val();
            $('.body_div_div2_div2_div_div_text').html(deliveryDate);
            isSorted = $('.sortStatusSelected').attr('data-is-sorted');
            initData();
            $('.limit_div').css('display', 'none');
        });

        // 固定日期选择
        $('[name = dateSelect]').on('click', function () {
            if ($(this).eq(0).attr('data-type') == 0) {
                // 昨日
                $('#sendDate').val(formatDate(new Date(new Date().getTime() - 24 * 60 * 60 * 1000)));
            } else if ($(this).eq(0).attr('data-type') == 1) {
                // 今日
                $('#sendDate').val(formatDate(new Date()));
            } else if ($(this).eq(0).attr('data-type') == 2) {
                // 明日
                $('#sendDate').val(formatDate(tomorrow));
            }
            $('[name = dateSelect]').each(function () {
                $(this).css('background-color', '#ffffff');
                $(this).css('color', '#121212');
            });
            $(this).css('background-color', '#4ab7c5');
            $(this).css('color', '#ffffff');
        });

        // 分拣状态选择
        $('[name = sortStatus]').on('click', function () {
            $('[name = sortStatus]').each(function () {
                $(this).removeClass('sortStatusSelected')
            });
            $(this).addClass('sortStatusSelected');
        });

        // 是否标品选择
        $('[name = isRough]').on('click', function () {
            $('[name = isRough]').each(function () {
                $(this).css('background-color', '#ffffff');
                $(this).css('color', '#121212');
            });
            $(this).css('background-color', '#4ab7c5');
            $(this).css('color', '#ffffff');
        });

        // 收起商品列表
        $('body').on('click', '.body_div_div2_div2_div_div2_div_div', function () {
            $(this).html('展开');
            $(this).removeClass('body_div_div2_div2_div_div2_div_div');
            $(this).addClass('body_div_div2_div2_div_div2_div_div2');
            $(this).parent().next().css('display', 'none');
        });

        // 展开商品列表
        $('body').on('click', '.body_div_div2_div2_div_div2_div_div2', function () {
            // 根据二级分类加载商品
            var that = this;
            admin.req({
                type: "post"
                , url: '/admin/sort/find-data-by-second-tier'
                , dataType: "json"
                , data: {
                    filterProperty: JSON.stringify({
                        delivery_date: deliveryDate,
                        is_sorted: isSorted,
                        searchText: searchText,
                        type_id: $(this).attr('data-type-id')
                    })
                }
                , cache: false
                , done: function (res) {
                    $(that).html('收起');
                    $(that).removeClass('body_div_div2_div2_div_div2_div_div2');
                    $(that).addClass('body_div_div2_div2_div_div2_div_div');
                    // 添加商品
                    $(that).parent().next().html('');
                    res.data.forEach(function (res2) {
                        var dataHtml = '<div class="body_div_div2_div2_div_div2_div2_div ';
                        // 根据分拣情况加入样式
                        if (res2.totalNum == res2.sortedNum) {
                            dataHtml += 'body_div_div2_div2_div_div2_div2_div_sorted';
                        } else if (res2.sortedNum == 0) {

                        } else if (res2.totalNum != res2.sortedNum) {
                            dataHtml += 'body_div_div2_div2_div_div2_div2_div_sort_some';
                        }
                        dataHtml += '" data-commodity-id="' + res2.commodity_id + '" data-totalNum="' + res2.totalNum + '">' + res2.commodity_name + '</div>';
                        $(that).parent().next().append(dataHtml);
                    });
                    $(that).parent().next().css('display', 'flex');
                }
            });
        });

        // 监听搜索
        $('.layui-input').on('keydown', function (e) {
            if (e.keyCode == 13) {
                searchText = $(this).val();
                initData();
            }
        });

        // 跳转商品详情页
        $('body').on('click', '.body_div_div2_div2_div_div2_div2_div', function () {
            var that = this;
            var totalNum = $(that).attr('data-totalNum');
            layer.open({
                type: 2,
                content: '/admin/sort/full-screen-sort-detail?commodity_id=' + $(this).attr('data-commodity-id') + '&deliveryDate=' + deliveryDate + '&isSorted=' + isSorted,
                title: false,
                closeBtn: 0,
                shadeClose: true,
                skin: 'yourClass',
                area: ["100%", "100%"],
                success: function (layero, index) {
                    // 监听返回判断选择的商品是否已全部分拣
                    var domObj = layero.find('iframe').contents();
                    var submit = domObj.find(".body_div_div_div1");

                    submit.on('click', function () {
                        layer.close(index);
                        $(that).removeClass('body_div_div2_div2_div_div2_div2_div_sorted');
                        $(that).removeClass('body_div_div2_div2_div_div2_div2_div_sort_some');
                        // 根据分拣列表统计分拣数量
                        var sortedNum = 0;
                        domObj.find('.body_div_div4_div_div').each(function () {
                            if ($(this).find('.body_div_div4_div_div_div2').attr('data-is-sorted') == 1) {
                                sortedNum += 1;
                            }
                        });

                        if (sortedNum == 0) {

                        } else if (sortedNum == totalNum) {
                            $(that).addClass('body_div_div2_div2_div_div2_div2_div_sorted');
                        } else {
                            $(that).addClass('body_div_div2_div2_div_div2_div2_div_sort_some');
                        }
                    })
                }
            });
        });

        // 按照大类id一键分拣
        $('.body_div_div_div4').on('click', function () {

            var bigClassId = $('img[src = "/admin/imgs/selected.png"]').parent().parent().attr('data-type-first-tier-id');
            if (bigClassId == undefined || bigClassId == "undefined") {
                bigClassId = -1;
            }
            admin.req({
                type: "post"
                , url: '/admin/sort/find-sort-num-all-by-first-tier-class-and-date'
                , dataType: "json"
                , data: {
                    bigClassId: bigClassId,
                    date: deliveryDate
                }
                , cache: false
                , done: function (res) {
                    if (res.data == 0) {
                        layer.msg('没有可分拣的商品');
                    } else {
                        layer.confirm('一共有' + res.data + '个商品未分拣，是否全部分拣？', {icon: 3, title:'提示'}, function (index) {
                            admin.req({
                                type: "post"
                                , url: '/admin/sort/sort-by-date-and-big-class-id'
                                , dataType: "json"
                                , cache: false
                                , data: {
                                    bigClassId: bigClassId,
                                    date: deliveryDate
                                }
                                , done: function () {
                                    layer.msg('一键分拣成功');
                                    // 修改已经打开的商品为已分拣
                                    $('.body_div_div2_div2_div_div2_div2_div').each(function () {
                                        if (!$(this).hasClass('body_div_div2_div2_div_div2_div2_div_sorted')) {
                                            $(this).addClass('body_div_div2_div2_div_div2_div2_div_sorted');
                                        }
                                    })
                                }
                            });
                        });
                    }
                }
            });
        });

    });

</script>