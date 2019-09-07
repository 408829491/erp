<form class="layui-form" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">商品</label>
        <div class="layui-input-block">
            <input type="text" name="name" value="<?= $commodity_name ?>" autocomplete="off" class="layui-input layui-disabled">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单位</label>
        <div class="layui-input-block">
            <input type="text" name="unit" value="<?= $unit ?>" autocomplete="off" class="layui-input layui-disabled">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">库存上限</label>
        <div class="layui-input-block">
            <input type="text" name="stock_limit_up_num" value="<?= $stock_limit_up_num ?>" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">库存下限</label>
        <div class="layui-input-block">
            <input type="text" name="stock_limit_down_num" value="<?= $stock_limit_down_num ?>" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
    </div>
</form>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form'], function () {
        var $ = layui.$
            , layer = layui.layerX
            , form = layui.form;
    })
</script>
