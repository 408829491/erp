
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">分类名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="必填"
                   autocomplete="off" class="layui-input" value="<?= $model->name ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序号</label>
        <div class="layui-input-inline">
            <input type="text" name="sequence" style="width: 300px;" placeholder="请输入设备名称"
                   autocomplete="off"
                   class="layui-input" value="<?= $model->sequence ?>">
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'upload'], function () {
        var $ = layui.$
            , layer = layui.layer
            , form = layui.form
            , admin = layui.admin
            , upload = layui.upload;
    })
</script>