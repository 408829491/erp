<link rel="stylesheet" media="screen and (min-device-width: 1920px)" href="/admin/resources/css/fullScreenSortTypeSelectedWidth1920.css">
<link rel="stylesheet" media="screen and (max-device-width: 1366px)" href="/admin/resources/css/fullScreenSortTypeSelectedWidth1366.css">

<div class="body_div">
    <div class="body_div_div">
        <div class="body_div_div_div1">
            <img src="/admin/imgs/sort-by-commodity.png" class="body_div_div_div1_img">
            <div class="body_div_div_div1_text">按商品分拣</div>
        </div>
        <div class="body_div_div_div2">
            <img src="/admin/imgs/sort-by-user.png" class="body_div_div_div1_img">
            <div class="body_div_div_div1_text">按客户分拣</div>
        </div>
    </div>
</div>
<div class="close_button">
    <img src="/admin/imgs/type-select-on-off.png" class="close_button_img">
    <div class="close_button_text">关闭</div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index'], function(){
        var $ = layui.$;

        // 关闭全屏
        $('.close_button').on('click', function () {
            parent.layui.admin.exitScreen();
            parent.layui.layer.closeAll();
        });

        // 商品分拣
        $('.body_div_div_div1').on('click', function () {
            layer.open({
                type: 2,
                content: '/admin/sort/full-screen-sort',
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
                    })
                }
            });
        });

        // 客户分拣
        $('.body_div_div_div2').on('click', function () {
            layer.open({
                type: 2,
                content: '/admin/sort/full-screen-sort-by-user',
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
                    })
                }
            });
        });

    });

</script>