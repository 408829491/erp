<style type="text/css">
    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }

    .billsImg {
        width: 100px;
        height: 100px;
        border: 1px dashed #8b98ab;
        display: flex;
        align-items: center;
        justify-content: center;
    }

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
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">

    <input type="hidden" name="user_audit_id" value="<?= $model['id'] ?>">
    <input type="hidden" name="user_id" value="<?= $model['user_id'] ?>">
    <input type="hidden" name="user_name" value="<?= $model['user_name'] ?>">
    <input type="hidden" name="refer_no" value="<?= $model['source_no'] ?>">
    <input type="hidden" name="create_user_id" value="<?= Yii::$app->user->identity['id'] ?>">

    <div class="layui-card">
        <div class="layui-card-header" style="display: flex;align-items: center;"><span style="border-radius: 50%;width: 12px;height: 12px;background-color: rgb(60, 195, 71);margin-right: 5px;"></span>基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item" style="margin-bottom: 0">
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">支付方式:</label>
                        <div class="layui-input-inline">
                            <select name="pay_way" lay-verify="required">
                                <option value="">支付方式</option>
                                <option value="0">微信</option>
                                <option value="1">支付宝</option>
                                <option value="2">转账</option>
                                <option value="3">现金</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">制单人:</label>
                        <div class="layui-input-inline">
                            <input type="text" style="background-color: #eaeaea;" name="create_user" class="layui-input" disabled value="<?= Yii::$app->user->identity['nickname'] ?>" />
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">交款人:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="pay_user" lay-verify="required" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">单据金额:</label>
                        <div class="layui-input-inline">
                            <?= $model['audit_price'] - $model['received_price'] ?>
                        </div>
                    </div>
                </div class="layui-form-item">
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header" style="display: flex;align-items: center;"><span style="border-radius: 50%;width: 12px;height: 12px;background-color: rgb(60, 195, 71);margin-right: 5px;"></span>结算单列表</div>
            <div class="layui-card-body">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
                <script type="text/html" id="type">
                    {{#  if(d.type == 1){ }}
                    销售订单
                    {{#  } else { }}
                    其他
                    {{#  } }}
                 </script>
                <script type="text/html" id="unPay">
                    {{ (d.audit_price - d.received_price).toFixed(2) }}
                 </script>
                <script type="text/html" id="pay">
                    <input class="layui-input" type="text" name="realPay" lay-verify="required" value="{{ (d.audit_price - d.received_price).toFixed(2) }}"/>
                 </script>
                <script type="text/html" id="skipPay">
                    <input class="layui-input" type="text" name="skipPay" lay-verify="required" value="0"/>
                </script>
                <script type="text/html" id="info">
                    <input class="layui-input" type="text" name="remarkDetail"/>
                </script>
            </div>
        </div>
        <div class="layui-card">
            <div class="layui-form-item">
                <div class="layui-inline" style="display: inline-flex;align-items: center;">
                    <label class="layui-form-label">上传凭证:</label>
                    <div class="billsImg" id="billsImg">
                        <i class="layui-icon layui-icon-camera-fill" style="font-size: 30px; color: #000000;"></i>
                    </div>
                    <div id="billsImgUrl" style="display: flex;flex-flow: wrap;"></div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline" style="display: inline-flex;align-items: center;">
                    <label class="layui-form-label">备注:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="remark" class="layui-input" style="width: 500px"/>
                    </div>
                </div>
            </div>
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
        }).use(['index', 'table', 'form', 'upload'], function(){
            var $ = layui.$
                ,admin = layui.admin
                ,table = layui.table
                , upload = layui.upload
                ,form = layui.form;

            //table设置默认参数
            table.set({
                page: true
                , parseData: function (res) {
                    return {
                        "code": res.code,
                        "msg": res.msg,
                        "count": res.data.total,
                        "data": res.data.list,
                    }
                }
                , request: {
                    pageName: 'pageNum',
                    limitName: 'pageSize'
                }
                , response: {
                    statusCode: 200
                }
                , toolbar: true
                , limit: 50
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            //展示已知数据
            var subList = [<?=json_encode($model)?>];
            table.render({
                elem: '#subList'
                ,cols: [[ //标题栏
                    {field:'id', title:'ID', width:80, unresize: true, sort: true, totalRowText: '合计:'}
                    ,{field: 'source_no', title: '原始单号'}
                    ,{field: 'user_name', title: '客户名称'}
                    ,{field: 'type', title: '业务类型', templet: "#type"}
                    ,{field: 'audit_price', title: '应收金额', totalRow: true}
                    ,{field: 'received_price', title: '已收金额'}
                    ,{templet: "#unPay", title: '未收金额'}
                    ,{title: '实收金额', templet: "#pay"}
                    ,{field: 'subPrice', templet: "#skipPay", title: '抹零金额'}
                    ,{field: 'delivery_date', title: '应收日期'}
                    ,{field: 'subTotalPrice', title: '备注', templet:"#info"}
                ]]
                , data: subList
                , page: false
                , toolbar: false
                , totalRow: true
                , limit: Number.MAX_VALUE
                , title: "列表"
                ,done: function(res, curr, count){
                    tableDataTemp = res;
                }
            });

            // 凭证上传
            upload.render({
                elem: '#billsImg'
                , url: '/admin/upload-file/index' // 必填项
                , method: 'post'  // 可选项。HTTP类型，默认post
                , acceptMime: 'image/*'
                , accept: 'images'
                , done: function (res, index, upload) {
                    $("#billsImgUrl").append(
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
        });
    </script>