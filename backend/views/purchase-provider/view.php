<style>
    .image_div {
        width:100px;
        height: 100px;
        display: flex;
        justify-items: center;
        align-items: center;
        margin-left: 20px;
    }
    .image_div_img {
        width:100px;
        height: 100px;
    }
    .image_div_close_icon {
        position: absolute;
        margin-top: -40px;
        margin-left: 80px;
    }
</style>
<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" lay-verify="required"  placeholder="请填写供应商名称" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">通讯信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系人</label>
                <div class="layui-input-block">
                    <input type="text" name="contact_name" lay-verify="required"  placeholder="请填写联系人姓名" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系手机</label>
                <div class="layui-input-block">
                    <input name="mobile" type="text"  placeholder="请填写联系手机" lay-verify="required" autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">电话</label>
                <div class="layui-input-block">
                    <input type="text" name="tel" placeholder="请填写联系人电话号码" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">详细地址</label>
                <div class="layui-input-block">
                    <input type="text" name="address_detail" placeholder="请填写详细地址" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">财务信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label">开户名称</label>
                <div class="layui-input-block">
                    <input type="text" name="bank_name"  placeholder="请填写开户名称"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开户银行</label>
                <div class="layui-input-block">
                    <input type="text" name="bank" placeholder="请填写开户银行"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">银行账号</label>
                <div class="layui-input-block">
                    <input type="text" name="bank_account" placeholder="请填写银行账号" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">发票抬头</label>
                <div class="layui-input-block">
                    <input type="text" name="invoice_title" placeholder="请填写发票抬头" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">税号</label>
                <div class="layui-input-block">
                    <input type="text" name="invoice_number" placeholder="请填写税号" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">账号信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>登录帐号</label>
                <div class="layui-input-block">
                    <input type="text" name="account" lay-verify="required"  placeholder="请填写供应商名称" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                <div class="layui-input-block">
                    <input type="text" name="password" lay-verify="required"  placeholder="请填写供应商名称" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">资质信息（图片上传请限制在5张以下）</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <div style="display: flex;flex-flow: wrap;" id="mainPicture">

                        <div style="width:100px;height: 100px;border:1px dashed #8b98ab;display: flex;align-items: center;justify-content: center;" id="addMainPicture">
                            <i class="layui-icon layui-icon-camera-fill" style="font-size: 30px; color: #000000;"></i>
                        </div>
                    </div>
                </div>

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
    }).use(['index', 'laydate','form','element','upload'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,form = layui.form
            ,upload  = layui.upload;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var index = parent.layer.getFrameIndex(window.name);
            // 获取图片列表数据
            var imgListData = "";
            $(".image_div_img").each(function () {
                imgListData += this.src;
                imgListData += ":;";
            });
            if (imgListData !== undefined) {
                imgListData = imgListData.substring(0,imgListData.length-2);
                field['pic'] = imgListData;
            }
            $.post({
                type: "post"
                , url: '/admin/customer/save'
                , dataType:'json'
                , data: data.field
                , success: function (e) {
                    console.log(e);
                    if(e.code === 200){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('customer-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }else{
                        layer.msg(e.data);
                    }
                }
            });
        });

        // 主图上传
        upload.render({
            elem: '#addMainPicture'
            ,url: '/admin/upload-file/index' // 必填项
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            ,done: function (res, index, upload) {
                $("#mainPicture").append(
                    '<div class="image_div">\n' +
                    '                <image class="image_div_img" src="' + res.data.file + '"></image>\n' +
                    '                <i class="layui-icon layui-icon-close image_div_close_icon"></i>\n' +
                    '            </div>'
                );
            }
        });

        // 删除图片
        $("body").on('click','.image_div_close_icon',function () {
            $(this).parent().remove();
        });


    });
</script>