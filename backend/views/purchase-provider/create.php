<style>
    .image_div {
        width: 100px;
        height: 100px;
        display: flex;
        justify-items: center;
        align-items: center;
        margin-left: 20px;
    }

    .image_div_img {
        width: 100px;
        height: 100px;
    }

    .image_div_close_icon {
        position: absolute;
        margin-top: -40px;
        margin-left: 80px;
    }
</style>
<div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
    <ul class="layui-tab-title">
        <li class="layui-this" lay-data="0">基本信息</li>
        <li lay-data="1">供应产品</li>
    </ul>
    <div class="layui-tab-content" style="padding: 0">
        <div class="layui-tab-item layui-show">
            <form class="layui-form" style="padding: 15px;">
                <div class="layui-card">
                    <div class="layui-card-header">基本信息</div>
                    <div class="layui-card-body">
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span
                                        style="color: #ff6d6d;margin-right: 5px;">*</span>名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="name" lay-verify="required" placeholder="请填写供应商名称" value=""
                                       autocomplete="off" class="layui-input">
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
                                <input type="text" name="contact_name" lay-verify="required" placeholder="请填写联系人姓名"
                                       value=""
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系手机</label>
                            <div class="layui-input-block">
                                <input name="mobile" type="text" placeholder="请填写联系手机" lay-verify="required|phone"
                                       autocomplete="off" class="layui-input" autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">电话</label>
                            <div class="layui-input-block">
                                <input type="text" name="tel" placeholder="请填写联系人电话号码" value="" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">详细地址</label>
                            <div class="layui-input-block">
                                <input type="text" name="address_detail" placeholder="请填写详细地址" value=""
                                       autocomplete="off"
                                       class="layui-input">
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
                                <input type="text" name="bank_name" placeholder="请填写开户名称" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">开户银行</label>
                            <div class="layui-input-block">
                                <input type="text" name="bank" placeholder="请填写开户银行" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">银行账号</label>
                            <div class="layui-input-block">
                                <input type="text" name="bank_account" placeholder="请填写银行账号" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">发票抬头</label>
                            <div class="layui-input-block">
                                <input type="text" name="invoice_title" placeholder="请填写发票抬头" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">税号</label>
                            <div class="layui-input-block">
                                <input type="text" name="invoice_number" placeholder="请填写税号" autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-card">
                    <div class="layui-card-header">账号信息</div>
                    <div class="layui-card-body">
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>登录帐号</label>
                            <div class="layui-input-block">
                                <input type="text" name="account" lay-verify="required" placeholder="请填写登录帐号" value=""
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><span
                                        style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                            <div class="layui-input-block">
                                <input type="text" name="password" lay-verify="required" placeholder="请填写密码" value=""
                                       autocomplete="off" class="layui-input">
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

                                    <div style="width:100px;height: 100px;border:1px dashed #8b98ab;display: flex;align-items: center;justify-content: center;"
                                         id="addMainPicture">
                                        <i class="layui-icon layui-icon-camera-fill"
                                           style="font-size: 30px; color: #000000;"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-hide">
                    <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit"
                           id="layuiadmin-app-form-submit"
                           value="确认">
                </div>
            </form>
        </div>
        <div class="layui-tab-item" style="padding:15px">
            <div class="layui-card">
                <div class="layui-card-header">供应商品清单</div>
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <label class="layui-form-label">商品</label>
                        <div class="layui-input-inline">
                            <input type="text" name='name' class="layui-input" id="name" placeholder="请输入商品名称/编码/关键字"
                                   style="width: 230px;">
                        </div>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn layui-input-inline" id="product-add-list" data-type="addProductList"><i
                                    class="layui-icon">&#xe654;</i></button>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">数量</label>
                        <div class="layui-input-inline">
                            <input type="text" name='count' class="layui-input" id="count" placeholder=""
                                   style="width: 60px;" value="1">
                        </div>

                        <div class="layui-input-inline">
                            <button class="layui-btn layui-input-inline" id="product-add-list"
                                    data-type="addProductSingle">添加
                            </button>
                        </div>
                    </div>
                </div>
                <div class="layui-card">
                    <table class="layui-hide" id="subList" lay-filter="subList"></table>
                    <script type="text/html" id="operation">
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                    class="layui-icon layui-icon-delete"></i>删除</a>
                    </script>
                </div>
            </div>

        </div>
    </div>


    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'laydate', 'form', 'element', 'upload', 'transfer','table'], function () {
            var $ = layui.$
                , admin = layui.admin
                , form = layui.form
                , table = layui.table
                , transfer = layui.transfer
                , upload = layui.upload;

            table.set({
                page: true
                , parseData: function (res) {
                    return {
                        "code": 0,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.list
                    }
                }
                , request: {
                    pageName: 'pageNum',
                    limitName: 'pageSize'
                }
                , response: {
                    statusCode: 0
                }
                , toolbar: true
                , limit: 50
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });
            // 监听提交
            form.on('submit(layuiadmin-app-form-submit)', function (data) {
                var index = parent.layer.getFrameIndex(window.name);
                var field = data.field;
               // data.field.sort_id = transfer.get(tb1, 'r', 'id');
                // 获取图片列表数据
                var imgListData = "";
                $(".image_div_img").each(function () {
                    imgListData += this.src;
                    imgListData += ":;";
                });
                if (imgListData !== undefined) {
                    imgListData = imgListData.substring(0, imgListData.length - 2);
                    field['pic'] = imgListData;
                }
                $.post({
                    type: "post"
                    , url: '/admin/purchase-provider/save'
                    , dataType: 'json'
                    , data: field
                    , success: function (e) {
                        console.log(e);
                        if (e.code === 200) {
                            layer.msg('提交成功', {time: 2000});
                            parent.layui.table.reload('list'); // 重载表格
                            parent.layer.close(index); // 再执行关闭
                        } else {
                            layer.msg(e.data);
                        }
                    }
                });
            });

            // 主图上传
            upload.render({
                elem: '#addMainPicture'
                , url: '/admin/upload-file/index' // 必填项
                , method: 'post'  // 可选项。HTTP类型，默认post
                , acceptMime: 'image/*'
                , accept: 'images'
                , done: function (res, index, upload) {
                    $("#mainPicture").append(
                        '<div class="image_div">\n' +
                        '                <image class="image_div_img" src="' + res.data.file + '"></image>\n' +
                        '                <i class="layui-icon layui-icon-close image_div_close_icon"></i>\n' +
                        '            </div>'
                    );
                }
            });

            // 删除图片
            $("body").on('click', '.image_div_close_icon', function () {
                $(this).parent().remove();
            });


            //展示已知数据
            var subList = [];
            table.render({
                elem: '#subList'
                , cols: [[ //标题栏
                    {
                        field: 'id',
                        title: 'ID',
                        width: 80,
                        fixed: 'left',
                        unresize: true,
                        sort: true,
                        totalRowText: '合计:'
                    }
                    , {field: 'pic', title: '商品图片', width: 100, templet: '#imgTpl', unresize: true}
                    , {field: 'name', title: '商品名称', minWidth: 100}
                    , {field: 'type_name', title: '分类', minWidth: 120}
                    , {field: 'unit', title: '单位', width: 80}
                    , {field: 'in_price', title: '进货价', width: 150}
                    , {
                        field: 'remark',
                        title: '备注',
                        width: 180,
                        templet: '#test-table-commentTpl',
                        unresize: true,
                        edit: 'text'
                    }
                    , {title: '操作', minWidth: 100, align: 'center', fixed: 'right', toolbar: '#operation'}
                ]]
                , data: subList
                , totalRow: true
                , page: false
                , toolbar: false
                , limit: Number.MAX_VALUE
                , title: "列表"
                , done: function (res, curr, count) {
                    tableDataTemp = res;
                }
            });


        });
    </script>