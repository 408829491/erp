<form class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list"
      style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>类型</label>
        <div class="layui-input-inline">
            <div style="display: flex;">
                <select lay-verify="required" name="type">
                    <option value="0">普通优惠券</option>
                    <option value="1" <?php if ($model->type == 1) : ?>selected<?php endif; ?>>积分换购券</option>
                    <option value="2" <?php if ($model->type == 2) : ?>selected<?php endif; ?>>新人券</option>
                </select>
            </div>
        </div>

        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" placeholder="长度<30,必填" lay-verify="required" autocomplete="off" class="layui-input" value="<?= $model->name ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>活动日期</label>
        <div class="layui-input-inline">
            <input type="text" name="activityDate" readonly lay-verify="required" id="activityDate" class="layui-input" value="<?= $model->start_date ?> 到 <?= $model->end_date ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>满</label>
        <div class="layui-input-inline">
            <input type="text" name="condition" autocomplete="off" class="layui-input" value="<?= $model->condition ?>">
        </div>
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>减</label>
        <div class="layui-input-inline">
            <input type="text" name="distance" autocomplete="off" class="layui-input" value="<?= $model->distance ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">换购积分</label>
        <div class="layui-input-inline">
            <input type="text" name="integral" autocomplete="off" class="layui-input" value="<?= $model->integral ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">描述</label>
        <div class="layui-input-inline">
            <textarea type="text" name="info" style="width: 500px;" placeholder="填写描述" autocomplete="off" class="layui-textarea"><?= $model->info ?></textarea>
        </div>
    </div>

    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
    </div>
</form>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form', 'upload', 'laydate'], function () {
        var $ = layui.$
            , layer = layui.layer
            , form = layui.form
            , admin = layui.admin
            , laydate = layui.laydate;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段
            field['id'] = <?= $model->id ?>;

            var tempTimes= field.activityDate.split(" 到 ");
            field.start_date = tempTimes[0];
            field.end_date = tempTimes[1];

            var index = parent.layer.getFrameIndex(window.name); // 先得到当前iframe层的索引

            // 提交 Ajax 成功后，关闭当前弹层并重载表格
            admin.req({
                type: "post"
                , url: '/admin/cus-discount-coupon-by-store-manager/edit'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
                }
            });
        });

        laydate.render({
            elem: '#activityDate'
            ,type: 'date'
            ,range: '到'
        });
    })
</script>