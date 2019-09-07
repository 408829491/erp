<form class="layui-form" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">客户账号</label>
        <div class="layui-input-block">
            <input type="text" name="user_name"  value="<?=$username?>" autocomplete="off" class="layui-input layui-disabled">
            <input type="hidden" name="user_id" value="<?=$id?>">
            <input type="hidden" name="tel" value="<?=$mobile?>">
            <input type="hidden" name="current_balance" value="<?=$balance?>">
            <input type="hidden" name="type" value="1">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户名称</label>
        <div class="layui-input-block">
            <input type="text" name="nickname" value="<?=$nickname?>" lay-verify="required" autocomplete="off" class="layui-input layui-disabled">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">操作员</label>
        <div class="layui-input-block">
            <input type="text" name="op_user" lay-verify="required"  autocomplete="off" class="layui-input layui-disabled" value="<?=Yii::$app->user->identity['username']?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">扣款金额</label>
        <div class="layui-input-block">
            <input type="text" name="amount" placeholder="￥" lay-verify="required"  autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea name="remark" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
    </div>
</form>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'laydate','form','element'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,form = layui.form;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var index = parent.layer.getFrameIndex(window.name);
            admin.req({
                type: "post"
                , url: '/admin/finance/confirm-recharge'
                , dataType: "json"
                , cache: false
                , data: data.field
                , done: function (e) {
                    console.log(e);
                    if(e.code === '200'){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('order-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }
                    layer.msg('保存失败');
                }
            });
        });

    });
</script>