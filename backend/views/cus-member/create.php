<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
                <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>手机号码</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required"  placeholder="请为会员创建账号（会员账号为手机号，不能修改）" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                <div class="layui-input-block">
                    <input name="password" type="text"  placeholder="请设置登录密码" lay-verify="required" autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真写姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="nickname" placeholder="请输入真实姓名" value="" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">联系电话</label>
                <div class="layui-input-block">
                    <input type="text" name="mobile" placeholder="请输入联系电话" value="" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="checkbox" checked="" name="is_check" lay-skin="switch" lay-filter="switchTest" lay-text="已审核|未审核">
                </div>
            </div>
            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
            </div>
        </div>
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
            $.post({
                type: "post"
                , url: '/admin/cus-member/save'
                , dataType:'json'
                , data: data.field
                , success: function (e) {
                    console.log(e);
                    if(e.code === 200){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('order-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }else{
                        layer.msg(e.data);
                    }
                }
            });
        });

    });
</script>