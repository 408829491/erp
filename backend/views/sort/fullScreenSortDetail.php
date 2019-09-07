<link rel="stylesheet" media="screen and (min-device-width: 1920px)" href="/admin/resources/css/fullScreenSortDetailWidth1920.css">
<link rel="stylesheet" media="screen and (max-device-width: 1366px)" href="/admin/resources/css/fullScreenSortDetailWidth1366.css">

<div class="body_div">
    <div class="body_div_div">
        <div class="body_div_div_div1">
            <image src="/admin/imgs/return.png" class="body_div_div_div1_image"></image>
            <span class="body_div_div_div1_text">返回</span>
        </div>
        <div class="body_div_div_div2">
            <input class="layui-input" style="width: 97%;">
        </div>
        <div class="body_div_div_div4">
            一键分拣
        </div>
    </div>
    <div class="body_div_div2">
        <div class="body_div_div2_div"><?= $commodityName ?></div>
        <div class="body_div_div2_div2">斤</div>
        <input type="hidden" id="commodityId" value="<?= $commodityId ?>" />
        <input type="hidden" id="selectedDate" value="<?= $selectedDate ?>" />
    </div>
    <div class="body_div_div3">
        <div class="body_div_div3_div1">
            分拣总数：
            <span class="body_div_div3_div1_span" id="sortTotalNum"><?= $totalNum ?></span>
        </div>
        <div class="body_div_div3_div2">
            已分拣总数：
            <span class="body_div_div3_div1_span" id="sortedTotalNum"><?= $sortedNum ?></span>
        </div>
        <div class="body_div_div3_div3">
            未分拣总数：
            <span class="body_div_div3_div1_span" id="unSortTotalNum"><?= $totalNum - $sortedNum ?></span>
        </div>
    </div>
    <div class="body_div_div4">
        <div class="body_div_div4_div">

            <?php foreach ($data as $index=>$item) : ?>
                <div class="body_div_div4_div_div">
                    <div class="body_div_div4_div_div_div1 <?php if ( $item['is_sorted'] == 1 ) : ?>body_div_div4_div_div_div1_sorted<?php endif; ?> <?php if ( $index == 0 ) : ?><?php if ( $item['is_sorted'] == 1 ) : ?>body_div_div4_div_div_div1_activity2<?php else : ?>body_div_div4_div_div_div1_activity<?php endif; ?><?php endif; ?>">
                        <div class="body_div_div4_div_div_div1_text"><?= $item['nick_name'] ?></div>
                        <div class="body_div_div4_div_div_div1_text2"><?= $item['num'] ?><?= $item['unit'] ?></div>
                        <div class="body_div_div4_div_div_div1_text3">
                            称重：
                            <span class="body_div_div4_div_div_div1_text3_text"><?= $item['actual_num'] ?></span>
                            <?= $item['unit'] ?>
                        </div>
                    </div>
                    <div class="body_div_div4_div_div_div2" data-index="<?= $index ?>" data-actual-num="<?= $item['actual_num'] ?>" data-is-sorted="<?= $item['is_sorted'] ?>" data-unit="<?= $item['unit'] ?>" data-num="<?= $item['num'] ?>" data-id="<?= $item['id'] ?>" data-nickname="<?= $item['nick_name'] ?>" data-commodity-name="<?= $item['commodity_name'] ?>">
                        <div class="body_div_div4_div_div_div2_text">操</div>
                        <div>作</div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<!-- 分拣 -->
<div class="sort_div">
    <div class="sort_div_div"></div>
    <div class="sort_div_div2">
        <div class="sort_div_div2_div">
            <div class="sort_div_div2_div_div"></div>
            <img src="/admin/imgs/x.png" class="sort_div_div2_div_img">
        </div>
        <div class="sort_div_div2_div2">
            <div class="sort_div_div2_div2_div1">分拣</div>
            <div class="sort_div_div2_div2_div2">打印</div>
            <div class="sort_div_div2_div2_div3">重置</div>
        </div>
        <div class="sort_div_div2_div3">
            <div class="sort_div_div2_div3_div1">订购数量：</div>
            <div class="sort_div_div2_div3_div2"></div>
        </div>
        <input class="layui-input sort_div_div2_div4">
        <div class="sort_div_div2_div5">
            <div class="sort_div_div2_div5_div1">
                <div class="sort_div_div2_div5_div1_div1">
                    <div class="sort_div_div2_div5_div1_div1_div1" data-num="1" name="numKeyboard">1</div>
                    <div class="sort_div_div2_div5_div1_div1_div2"></div>
                    <div class="sort_div_div2_div5_div1_div1_div1" data-num="2" name="numKeyboard">2</div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="4" name="numKeyboard">4</div>
                    <div class="sort_div_div2_div5_div1_div2_div2"></div>
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="5" name="numKeyboard">5</div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="7" name="numKeyboard">7</div>
                    <div class="sort_div_div2_div5_div1_div2_div2"></div>
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="8" name="numKeyboard">8</div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="-1" name="numKeyboard">返回</div>
                    <div class="sort_div_div2_div5_div1_div2_div2"></div>
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="0" name="numKeyboard">0</div>
                </div>
            </div>
            <div class="sort_div_div2_div5_div2"></div>
            <div class="sort_div_div2_div5_div3">
                <div class="sort_div_div2_div5_div1_div1">
                    <div class="sort_div_div2_div5_div1_div1_div1" data-num="3" name="numKeyboard">3</div>
                    <div class="sort_div_div2_div5_div1_div1_div2"></div>
                    <div class="sort_div_div2_div5_div1_div1_div1" data-num="-2" name="numKeyboard">
                        <img src="/admin/imgs/x2.png" class="sort_div_div2_div5_div1_div1_div1_img">
                    </div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="6" name="numKeyboard">6</div>
                    <div class="sort_div_div2_div5_div1_div2_div2"></div>
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="-3" name="numKeyboard">清空</div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="9" name="numKeyboard">9</div>
                    <div class="sort_div_div2_div5_div1_div2_div2"></div>
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="." name="numKeyboard">.</div>
                </div>
                <div class="sort_div_div2_div5_div1_div3"></div>
                <div class="sort_div_div2_div5_div1_div2">
                    <div class="sort_div_div2_div5_div1_div2_div1" data-num="-4" name="numKeyboard">完成</div>
                </div>
            </div>
        </div>
        <div class="sort_div_div2_div6">已分拣</div>
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script src="/admin/plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
<script src="/admin/plugins/cdsPrint/common/js/print/pFuncs.js"></script>
<script src="/admin/plugins/cdsPrint/common/js/print/printCommon.js"></script>
<script src="/admin/plugins/cdsPrint/common/js/print/publicPrinter.js"></script>
<script src="/admin/plugins/cdsPrint/js/print/summary.js"></script>
<script src="/admin/plugins/cdsPrint/js/print/pick.js"></script>

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

        var sortShow = $('.sort_div');
        var numInput = $('.sort_div_div2_div4');
        var success = $('[data-num = -4]');

        // 操作监听
        $('.body_div_div4_div_div_div2').on('click', function () {

            var print = $('.sort_div_div2_div2_div2');
            // 赋值
            $('.sort_div_div2_div_div').html($(this).attr('data-commodity-name') + '【' + $(this).attr('data-nickname') + '】');
            $('.sort_div_div2_div3_div2').html($(this).attr('data-num') + $(this).attr('data-unit'));
            success.attr('data-id', $(this).attr('data-id'));
            success.attr('data-index', $(this).attr('data-index'));
            // 判断是否已分拣
            numInput.val('');
            if (1 == $(this).attr('data-is-sorted')) {
                var reStatus = $('.sort_div_div2_div2_div3');
                reStatus.css('display', 'flex');
                $('.sort_div_div2_div6').css('display', 'flex');
                numInput.val($(this).attr('data-actual-num'));
                numInput.prop('disabled','disabled');
                // 重置按钮绑定id
                reStatus.attr('data-id', $(this).attr('data-id'));
                reStatus.attr('data-index', $(this).attr('data-index'));
                // 打印按钮绑定id
                print.attr('data-id', $(this).attr('data-id'));
            } else {
                $('.sort_div_div2_div2_div3').css('display', 'none');
                $('.sort_div_div2_div6').css('display', 'none');
                numInput.removeAttr('disabled');
                print.removeAttr('data-id');
            }

            sortShow.css('display', 'flex');
        });

        var sortTotalNum = $('#sortTotalNum').html();
        var sortedTotalNum = $('#sortedTotalNum').html();
        var unSortTotalNum = $('#unSortTotalNum').html();

        // 数字键盘监听
        $('body').on('click', '[name = numKeyboard]', function () {
            var num = numInput.val();
            var that = this;
            switch ($(this).attr('data-num')) {
                case '1':
                    num += "1";
                    break;
                case '2':
                    num += "2";
                    break;
                case '3':
                    num += "3";
                    break;
                case '4':
                    num += "4";
                    break;
                case '5':
                    num += "5";
                    break;
                case '6':
                    num += "6";
                    break;
                case '7':
                    num += "7";
                    break;
                case '8':
                    num += "8";
                    break;
                case '9':
                    num += "9";
                    break;
                case '0':
                    num += "0";
                    break;
                case '.':
                    num += ".";
                    break;
                case '-1':
                    sortShow.css('display', 'none');
                    break;
                case '-2':
                    num = num.substr(0, num.length - 1);
                    break;
                case '-3':
                    num = '';
                    break;
                case '-4':
                    // 分拣完成
                    admin.req({
                        type: "get"
                        , url: '/admin/sort/change-status'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            id: $(this).attr('data-id'),
                            amount: num
                        }
                        , done: function (res) {
                            sortShow.css('display', 'none');
                            // 改变列表的样式
                            var index = $(that).attr('data-index');
                            var dataOne = $('.body_div_div4_div_div').eq(index);
                            var dataOneLeft = dataOne.find('.body_div_div4_div_div_div1');
                            dataOneLeft.addClass('body_div_div4_div_div_div1_sorted');
                            // 如果是已选中切换选中状态
                            if (dataOneLeft.hasClass('body_div_div4_div_div_div1_activity')) {
                                dataOneLeft.removeClass('body_div_div4_div_div_div1_activity');
                                dataOneLeft.addClass('body_div_div4_div_div_div1_activity2');
                            }
                            dataOne.find('.body_div_div4_div_div_div1_text3_text').html(num);
                            // 修改是否分拣状态
                            dataOne.find('.body_div_div4_div_div_div2').attr('data-is-sorted', '1');
                            dataOne.find('.body_div_div4_div_div_div2').attr('data-actual-num', num);
                            // 修改分拣总数
                            $('#sortedTotalNum').html(++sortedTotalNum);
                            $('#unSortTotalNum').html(--unSortTotalNum);
                            // 打印标签
                            pick_print.startPrint({clickObj: dataOne.find('.body_div_div4_div_div_div2').eq(0)});
                        }
                    });
                    break;
            }

            numInput.val(num);
        });

        // 数字键盘X点击监听
        $('body').on('click', '.sort_div_div2_div_img', function () {
            sortShow.css('display', 'none');
        });

        // 数字键盘重置监听
        $('body').on('click', '.sort_div_div2_div2_div3', function () {
            var that = this;
            layer.confirm('是否确认重新分拣？', {icon:3, title:'提示信息'}, function (index) {
                admin.req({
                    type: "get"
                    , url: '/admin/sort/re-status'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        id: $(that).attr('data-id')
                    }
                    , done: function () {
                        layer.close(index);
                        sortShow.css('display', 'none');
                        // 重置列表
                        var index2 = $(that).attr('data-index');
                        var dataOne = $('.body_div_div4_div_div').eq(index2);
                        var dataOneLeft = dataOne.find('.body_div_div4_div_div_div1');
                        dataOneLeft.removeClass('body_div_div4_div_div_div1_sorted');
                        // 如果是选中，那么切换选择样式
                        if (dataOneLeft.hasClass('body_div_div4_div_div_div1_activity2')) {
                            dataOneLeft.removeClass('body_div_div4_div_div_div1_activity2');
                            dataOneLeft.addClass('body_div_div4_div_div_div1_activity');
                        }
                        dataOne.find('.body_div_div4_div_div_div1_text3_text').html(0);
                        // 修改是否分拣状态
                        dataOne.find('.body_div_div4_div_div_div2').attr('data-is-sorted', '0');
                        // 修改分拣总数
                        $('#sortedTotalNum').html(--sortedTotalNum);
                        $('#unSortTotalNum').html(++unSortTotalNum);
                    }
                });
            });
        });

        // 数字键盘打印监听
        $('body').on('click', '.sort_div_div2_div2_div2', function () {
            var that = this;
            var id = $(this).attr('data-id');
            if (id == undefined) {
                layer.msg('未分拣商品不能打印');
                return ;
            }

            layer.confirm('是否确认打印', {icon:3, title:'提示信息'}, function (index) {
                // 打印标签
                pick_print.startPrint({clickObj: $(that)});
                layer.close(index);
            });
        });

        var num = "";
        // 监听body上的回车键
        $('body').on('keyup', function (e) {
            if (e.keyCode === 190) {
                // 输入.
                num += ".";
            } else if (e.keyCode >= 48 && e.keyCode <= 57) {
                num += e.keyCode - 48;
            } else if (e.keyCode === 13) {
                // 按照选中的进行分拣
                var activityObj = $('.body_div_div4_div_div_div1_activity');
                var optObj = activityObj.next();
                var isSorted = optObj.attr('data-is-sorted');
                var tempNum = num;
                if (isSorted == 0) {
                    admin.req({
                        type: "get"
                        , url: '/admin/sort/change-status'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            id: optObj.attr('data-id'),
                            amount: tempNum
                        }
                        , done: function () {
                            activityObj.addClass('body_div_div4_div_div_div1_sorted');
                            activityObj.find('.body_div_div4_div_div_div1_text3_text').html(tempNum);
                            // 修改是否分拣状态
                            optObj.attr('data-is-sorted', '1');
                            optObj.attr('data-actual-num', tempNum);
                            // 修改分拣总数
                            $('#sortedTotalNum').html(++sortedTotalNum);
                            $('#unSortTotalNum').html(--unSortTotalNum);
                            // 打印标签
                            pick_print.startPrint({clickObj: optObj});
                            // 选中移动到下一个
                            $('.body_div_div4_div_div_div1').each(function () {
                                if ($(this).hasClass('body_div_div4_div_div_div1_activity')) {
                                    $(this).removeClass('body_div_div4_div_div_div1_activity');
                                    return false;
                                }
                                if ($(this).hasClass('body_div_div4_div_div_div1_activity2')) {
                                    $(this).removeClass('body_div_div4_div_div_div1_activity2');
                                    return false;
                                }
                            });
                            var nextDataOneLeft = activityObj.parent().next().find('.body_div_div4_div_div_div1');
                            if (nextDataOneLeft.hasClass('body_div_div4_div_div_div1_sorted')) {
                                nextDataOneLeft.addClass('body_div_div4_div_div_div1_activity2');
                            } else {
                                nextDataOneLeft.addClass('body_div_div4_div_div_div1_activity');
                            }
                        }
                    });
                } else {
                    layer.msg('订单已分拣', {icon: 6});
                }
                num = "";
            }
        });

        // 列表框选中
        $('.body_div_div4_div_div_div1').on('click', function () {
            $('.body_div_div4_div_div_div1').each(function () {
               if ($(this).hasClass('body_div_div4_div_div_div1_activity')) {
                   $(this).removeClass('body_div_div4_div_div_div1_activity');
                   return false;
               }
                if ($(this).hasClass('body_div_div4_div_div_div1_activity2')) {
                    $(this).removeClass('body_div_div4_div_div_div1_activity2');
                    return false;
                }
            });
            // 如果是已分拣改成红色
            if ($(this).next().attr('data-is-sorted') == 1) {
                $(this).addClass('body_div_div4_div_div_div1_activity2');
            } else {
                $(this).addClass('body_div_div4_div_div_div1_activity');
            }
        });

        // 一键分拣
        $('.body_div_div_div4').on('click', function () {

            var date = $('#selectedDate').val();
            var commodityId = $('#commodityId').val();

            admin.req({
                type: "post"
                , url: '/admin/sort/find-sort-num-all-by-date-and-commodity-id'
                , dataType: "json"
                , data: {
                    commodityId: commodityId,
                    date: date
                }
                , cache: false
                , done: function (res) {
                    if (res.data.totalNum == 0) {
                        layer.msg('没有可分拣的商品');
                    } else {
                        layer.confirm('一共有' + res.data.totalNum + '个商品未分拣，是否全部分拣？', {icon: 3, title:'提示'}, function (index) {
                            admin.req({
                                type: "post"
                                , url: '/admin/sort/sort-by-date-and-commodity-id'
                                , dataType: "json"
                                , cache: false
                                , data: {
                                    commodityId: commodityId,
                                    date: date
                                }
                                , done: function () {
                                    layer.msg('一键分拣成功');
                                    // 打印分拣标签
                                    var obj = $('<div></div>');
                                    obj.attr('data-id', res.data.ids);
                                    pick_print.startPrint({clickObj: obj});
                                    window.location.reload();
                                }
                            });
                        });
                    }
                }
            });

        });

        // 窗口获取焦点
        window.focus();

    });

</script>