<form class="layui-form" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">销售姓名</label>
        <div class="layui-input-block">
            <input type="text" name="name"  value="<?=$model->name?>" placeholder="请输入销售姓名" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">销售账号</label>
        <div class="layui-input-block">
            <input type="text" name="account_tel"  value="<?=$model->account_tel?>" placeholder="销售帐号为手机号，不能修改" autocomplete="off" class="layui-input layui-disabled">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-block">
            <input type="text" name="password"  value="" placeholder="请输入密码" autocomplete="off" class="layui-input">
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
                , url: '/admin/salesman/save?id=<?=$model->id?>'
                , dataType: "json"
                , cache: false
                , data: data.field
                , done: function (e) {
                    if(e.code === 200){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }
                    layer.msg('保存失败');
                }
            });
        });

    });
</script>