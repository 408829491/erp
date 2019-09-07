<mate name="viewport" content="width=375,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no"/>

<style>

    html {
        background-color: #ffffff;
    }

    .body_div {
        width: 100%;
    }

    .body_div_div1 {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #49c167;
    }

    .body_div_div1_div1 {
        width: 87.2%;
        display: flex;
        align-items: center;
        margin-top: 11px;
    }

    .body_div_div1_div1:last-child {
        margin-bottom: 11px;
    }

    .body_div_div1_div1_left {
        width: 35.8%;
        height: 22px;
        font-size: 16px;
        color: #ffffff;
    }

    .body_div_div1_div1_right {
        width: 64.2%;
        font-size: 16px;
        color: #ffffff;
    }

    .body_div_div2 {
        width: 100%;
        display: flex;
        align-items: center;
        margin-top: 16px;
    }

    .body_div_div2_div1 {
        font-size: 16px;
        color: #333333;
        margin-left: 50%;
        transform: translateX(-50%);
        font-weight: bold;
    }

    .body_div_div2_div2 {
        font-size: 16px;
        color: red;
        margin-left: auto;
        margin-right: 4.5%;
        font-weight: bold;
    }

    .body_div_div3 {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .body_div_div3_div1 {
        width: 91.4%;
        margin-top: 18px;
        margin-bottom: 18px;
        font-size: 16px;
        color: #333333;
        display: flex;
        align-items: center;
        font-weight: bold;
    }

    .body_div_div3_div1_div1 {
        width: 63.3%;
    }

    .body_div_div3_div1_div2 {
        width: 19%;
        display: flex;
        justify-content: center;
    }

    .body_div_div3_div1_div3 {
        margin-left: auto;
    }

    .body_div_div3_div2 {
        width: 100%;
        height: 1px;
        background-color: #f2f2f2;
    }

    .body_div_div3_div3 {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .body_div_div3_div3_div1 {
        width: 91.4%;
        margin-top: 9px;
        margin-bottom: 12px;
        font-size: 15px;
        color: #333333;
        display: flex;
        align-items: center;
    }

    .body_div_div3_div3_div3 {
        width: 91.4%;
        background-color: #f2f2f2;
        border-radius: 4px;
        margin-bottom: 12px;
        display: none;
        justify-content: center;
    }

    .body_div_div3_div3_div3_div {
        width: 88.4%;
        margin-top: 8px;
        margin-bottom: 9px;
    }

    .body_div_div3_div3_div3_div_div1 {
        display: flex;
        align-items: center;
    }

    .body_div_div3_div3_div3_div_div1_div1 {
        width: 36.2%;
    }

    .body_div_div3_div3_div3_div_div1_div2 {
        width: 27.2%;
    }

    .body_div_div3_div3_div3_div_div1_div3 {
        width: 16.6%;
    }

    .body_div_div3_div3_div3_div_div1_div4 {
        margin-left: auto;
    }

    .body_div_div3_div3_div3_div_div2 {
        display: flex;
        align-items: center;
        margin-top: 3px;
    }

    .body_div_div3_div3_div1_div1 {
        width: 63.3%;
        display: flex;
        align-items: center;
    }

    .body_div_div3_div3_div1_div1_img {
        height: 13px;
        width: auto;
        margin-right: 5.5%;
    }

    .body_div_div3_div3_div1_div2 {
        width: 19%;
        display: flex;
        justify-content: center;
    }

    .body_div_div3_div3_div1_div3 {
        margin-left: auto;
    }

    .body_div_div3_div3_div2 {
        width: 100%;
        height: 1px;
        background-color: #f2f2f2;
    }

    .body_div_div4 {
        width: 100%;
        display: none;
        flex-direction: column;
        align-items: center;
    }

    .body_div_div4_div1 {
        width: 91.4%;
        margin-top: 18px;
        margin-bottom: 18px;
        font-size: 16px;
        color: #333333;
        display: flex;
        align-items: center;
        font-weight: bold;
    }

    .body_div_div4_div1_div1 {
        width: 75%;
    }

    .body_div_div4_div1_div3 {
        margin-left: auto;
    }

    /* 旋转 */
    .rotate {
        transform:rotate(90deg);
        -ms-transform:rotate(90deg);
        -moz-transform:rotate(90deg);
        -webkit-transform:rotate(90deg);
        -o-transform:rotate(90deg);
    }

    /* 旋转回复 */
    .rotateReply {
        transform:rotate(0);
        -ms-transform:rotate(0deg);
        -moz-transform:rotate(0deg);
        -webkit-transform:rotate(0);
        -o-transform:rotate(0deg);
    }
</style>

<div class="body_div">
    <div class="body_div_div1">
        <div class="body_div_div1_div1">
            <div class="body_div_div1_div1_left">采购方</div>
            <div class="body_div_div1_div1_right">鲜精灵</div>
        </div>
        <div class="body_div_div1_div1">
            <div class="body_div_div1_div1_left">供货方</div>
            <div class="body_div_div1_div1_right"><?= $model->agent_name ?></div>
        </div>
        <div class="body_div_div1_div1">
            <div class="body_div_div1_div1_left">采购单号</div>
            <div class="body_div_div1_div1_right"><?= $model->purchase_no ?></div>
        </div>
        <div class="body_div_div1_div1">
            <div class="body_div_div1_div1_left">计划采购时间</div>
            <div class="body_div_div1_div1_right"><?= $model->plan_date ?></div>
        </div>
    </div>
    <div class="body_div_div2">
        <div class="body_div_div2_div1">采购明细</div>
        <div class="body_div_div2_div2">按客户</div>
    </div>
    <div class="body_div_div3">
        <div class="body_div_div3_div1">
            <div class="body_div_div3_div1_div1">商品名称</div>
            <div class="body_div_div3_div1_div2">采购数量</div>
            <div class="body_div_div3_div1_div3">单位</div>
        </div>
        <div class="body_div_div3_div2"></div>

        <?php foreach ($commodityDataList as $item) : ?>
            <div class="body_div_div3_div3">
                <div class="body_div_div3_div3_div1">
                    <div class="body_div_div3_div3_div1_div1">
                        <img src="/admin/imgs/arrows-left.png" class="body_div_div3_div3_div1_div1_img">
                        <?= $item['commodity_name'] ?>
                    </div>
                    <div class="body_div_div3_div3_div1_div2"><?= $item['num'] ?></div>
                    <div class="body_div_div3_div3_div1_div3"><?= $item['unit'] ?></div>
                </div>
                <div class="body_div_div3_div3_div3">
                    <div class="body_div_div3_div3_div3_div">
                        <div class="body_div_div3_div3_div3_div_div1">
                            <?php if ( $type == 0 ) : ?>
                                <div class="body_div_div3_div3_div3_div_div1_div1">客户名称</div>
                            <?php else : ?>
                                <div class="body_div_div3_div3_div3_div_div1_div1">客户编码</div>
                            <?php endif; ?>
                            <div class="body_div_div3_div3_div3_div_div1_div2">数量</div>
                            <div class="body_div_div3_div3_div3_div_div1_div3">单位</div>
                            <div class="body_div_div3_div3_div3_div_div1_div4">备注</div>
                        </div>
                        <?php foreach ($item['details'] as $item2) : ?>
                            <div class="body_div_div3_div3_div3_div_div2">
                                <?php if ( $type == 0 ) : ?>
                                    <div class="body_div_div3_div3_div3_div_div1_div1"><?= $item2['nick_name'] ?></div>
                                <?php else : ?>
                                    <div class="body_div_div3_div3_div3_div_div1_div1"><?= $item2['user_id'] ?></div>
                                <?php endif; ?>
                                <div class="body_div_div3_div3_div3_div_div1_div2"><?= $item2['num'] ?></div>
                                <div class="body_div_div3_div3_div3_div_div1_div3"><?= $item2['unit'] ?></div>
                                <div class="body_div_div3_div3_div3_div_div1_div4"><?= $item2['remark'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="body_div_div3_div3_div2"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="body_div_div4">
        <div class="body_div_div4_div1">
            <?php if ( $type == 0 ) : ?>
                <div class="body_div_div4_div1_div1">客户名称</div>
            <?php else : ?>
                <div class="body_div_div4_div1_div1">客户编码</div>
            <?php endif; ?>
            <div class="body_div_div4_div1_div3">采购数量</div>
        </div>
        <div class="body_div_div3_div2"></div>
        <?php foreach ($userDataList as $item) : ?>
            <div class="body_div_div3_div3">
                <div class="body_div_div3_div3_div1">
                    <div class="body_div_div3_div3_div1_div1">
                        <img src="/admin/imgs/arrows-left.png" class="body_div_div3_div3_div1_div1_img">
                        <?php if ( $type == 0 ) : ?>
                            <?= $item['nick_name'] ?>
                        <?php else : ?>
                            <?= $item['user_id'] ?>
                        <?php endif; ?>
                    </div>
                    <div class="body_div_div3_div3_div1_div3"><?= $item['num'] ?></div>
                </div>
                <div class="body_div_div3_div3_div3">
                    <div class="body_div_div3_div3_div3_div">
                        <div class="body_div_div3_div3_div3_div_div1">
                            <div class="body_div_div3_div3_div3_div_div1_div1">商品名称</div>
                            <div class="body_div_div3_div3_div3_div_div1_div2">数量</div>
                            <div class="body_div_div3_div3_div3_div_div1_div3">单位</div>
                            <div class="body_div_div3_div3_div3_div_div1_div4">备注</div>
                        </div>
                        <?php foreach ($item['details'] as $item2) : ?>
                            <div class="body_div_div3_div3_div3_div_div2">
                                <div class="body_div_div3_div3_div3_div_div1_div1"><?= $item2['commodity_name'] ?></div>
                                <div class="body_div_div3_div3_div3_div_div1_div2"><?= $item2['num'] ?></div>
                                <div class="body_div_div3_div3_div3_div_div1_div3"><?= $item2['unit'] ?></div>
                                <div class="body_div_div3_div3_div3_div_div1_div4"><?= $item2['remark'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="body_div_div3_div3_div2"></div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table'], function(){
        var table = layui.table
            ,admin = layui.admin
            ,$ = layui.$;

        // 修改title
        document.title = '鲜精灵';

        // 根据商品加载明细数据 监听明细行
        $('.body_div_div3_div3').on('click', function () {
            var line = $(this).find('.body_div_div3_div3_div3');
            if (line.css('display') == 'none') {
                line.css('display', 'flex');
                // 添加动画
                $(this).find('img').removeClass('rotateReply').addClass('rotate');
            } else {
                line.css('display', 'none');
                // 动画复原
                $(this).find('img').removeClass('rotate').addClass('rotateReply');
            }
        });
        
        // 切换显示
        $('.body_div_div2_div2').on('click', function () {
            var commodityObj = $('.body_div_div3');
            var userObj = $('.body_div_div4');
            var textObj = $('.body_div_div2_div2');

            if (commodityObj.css('display') == "none") {
                commodityObj.css('display', 'flex');
                userObj.css('display', 'none');
                textObj.html('按客户');
            } else {
                commodityObj.css('display', 'none');
                userObj.css('display', 'flex');
                textObj.html('按商品');
            }
        })
    });

</script>
