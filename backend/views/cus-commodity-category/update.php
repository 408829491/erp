
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">分类名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="必填"
                   autocomplete="off" class="layui-input" value="<?= $model->name ?>">
        </div>
    </div>
    <?php if ($model->pid == 0) : ?>
        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>分类图片</label>
            <div style="display: flex;">
                <input type="text" name="pic_category" style="width: 300px;" lay-verify="required" placeholder="请选择图片"
                       autocomplete="off" readonly class="layui-input" value="<?= $model->pic_category ?>">
                <button type="button" class="layui-btn layui-btn-primary" id="LAY_avatarUpload">
                    <i class="layui-icon">&#xe67c;</i>上传图片
                </button>
                <button class="layui-btn layui-btn-primary" id="lookImg">查看图片</button>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>分类大图</label>
            <div style="display: flex;">
                <input type="text" name="pic_path_big" style="width: 300px;" lay-verify="required" placeholder="请选择图片"
                       autocomplete="off" readonly class="layui-input" value="<?= $model->pic_path_big ?>">
                <button type="button" class="layui-btn layui-btn-primary" id="LAY_avatarUpload2">
                    <i class="layui-icon">&#xe67c;</i>上传图片
                </button>
                <button class="layui-btn layui-btn-primary" id="lookImg2">查看图片</button>
            </div>
        </div>
    <?php endif; ?>
    <div class="layui-form-item">
        <label class="layui-form-label">排序号</label>
        <div class="layui-input-inline">
            <input type="text" name="sequence" style="width: 300px;" placeholder="请输入设备名称"
                   autocomplete="off"
                   class="layui-input" value="<?= $model->sequence ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">是否显示</label>
        <div class="layui-input-inline">
            <input type="checkbox" name="is_show" lay-skin="switch" lay-text="是|否" <?php if ($model->is_show == 1) : ?>checked<?php endif; ?>>
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

        upload.render({
            elem: '#LAY_avatarUpload'
            , url: '/admin/upload-file/index'
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            , done: function (res) {
                //上传完毕 添加保存字段
                $("[name=pic_category]").val(res.data.file);
            }
        });

        //查看图片
        $("#lookImg").on("click", function () {
            var src = $("[name=pic_category]").val();
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

        upload.render({
            elem: '#LAY_avatarUpload2'
            , url: '/admin/upload-file/index'
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            , done: function (res) {
                //上传完毕 添加保存字段
                $("[name=pic_path_big]").val(res.data.file);
            }
        });

        //查看图片
        $("#lookImg2").on("click", function () {
            var src = $("[name=pic_path_big]").val();
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