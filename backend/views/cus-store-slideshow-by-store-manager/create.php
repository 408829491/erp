
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>图片地址</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="img_url" style="width: 300px;" readonly lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-input-inline layui-btn-container" style="width: auto;">
            <button type="button" class="layui-btn layui-btn-primary" id="LAY_avatarUpload">
                <i class="layui-icon">&#xe67c;</i>上传图片
            </button>
            <button class="layui-btn layui-btn-primary" id="lookImg">查看图片
            </button>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">简介</label>
        <div class="layui-input-block">
            <textarea name="info" placeholder="请输入内容" class="layui-textarea"></textarea>
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
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form', 'upload'], function () {
        var $ = layui.$
            , layer = layui.layerX
            , form = layui.form
            , admin = layui.admin
            , upload = layui.upload;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段

            var index = parent.layer.getFrameIndex(window.name); // 先得到当前iframe层的索引

            // 提交 Ajax 成功后，关闭当前弹层并重载表格
            admin.req({
                type: "post"
                , url: '/admin/cus-store-slideshow-by-store-manager/save'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
                }
            });
        });

        upload.render({
            elem: '#LAY_avatarUpload'
            , url: '/admin/upload-file/index'
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            , done: function (res) {
                //上传完毕 添加保存字段
                $("[name=img_url]").val(res.data.file);
            }
        });

        //查看图片
        $("#lookImg").on("click", function () {
            var src = $("[name=img_url]").val();
            layui.layer.photos({
                photos: {
                    "title": "查看头像" //相册标题
                    , "data": [{
                        "src": src //原图地址
                    }]
                }
                , shade: 0.01
                , closeBtn: 1
                , anim: 5
            });
        });

    })
</script>