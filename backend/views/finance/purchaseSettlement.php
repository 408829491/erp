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
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header">基础信息</div>
        <div class="layui-card-body">
            <div class="layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <label class="layui-form-label">供应商：</label>
                    <div class="layui-input-inline">
                        <?=$model['agent_name']?>
                    </div>
                    <label class="layui-form-label">付款方式：</label>
                    <div class="layui-input-inline">
                        <select name="pay_way" lay-verify="required" >
                            <option value="">支付方式</option>
                            <option value="0">微信</option>
                            <option value="1">支付宝</option>
                            <option value="2">转账</option>
                            <option value="3">现金</option>
                        </select>
                    </div>
                    <label class="layui-form-label">制单人：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="create_user" placeholder="" autocomplete="off" class="layui-input layui-disabled" disabled value="<?=Yii::$app->user->identity['username']?>">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">领款人：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="pay_user" placeholder="请输入交款人" autocomplete="off" class="layui-input" lay-verify="required">
                    </div>
                    <label class="layui-form-label">付款金额：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="actual_price" id="actual_price" autocomplete="off" class="layui-input layui-disabled" value="0.00">
                    </div>
                    <label class="layui-form-label">单据金额：</label>
                    <div class="layui-input-inline">
                        <?=$model['price']?>
                    </div>
                </div>
                <div class="layui-form-item layui-hide">
                    <input type="hidden" name="agent_id" value="<?=$model['agent_id']?>">
                    <input type="hidden" name="agent_name" value="<?=$model['agent_name']?>">
                    <input type="hidden" name="purchase_type" value="<?=$model['purchase_type']?>">
                    <input type="hidden" name="purchase_no" value="<?=$model['purchase_no']?>">
                    <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">结算单列表</div>
            <div class="layui-card-body">


            <div class="layui-card">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
                <script type="text/html" id="operation">
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
                </script>

            </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <label class="layui-form-label">上传凭证</label>
                        <div style="display: flex;flex-flow: wrap;" id="mainPicture">

                            <div style="width:100px;height: 100px;border:1px dashed #8b98ab;display: flex;align-items: center;justify-content: center;" id="addMainPicture">
                                <i class="layui-icon layui-icon-camera-fill" style="font-size: 30px; color: #000000;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <label class="layui-form-label">备注</label>
                        <div class="layui-input-inline">
                            <input type="text" name="remark" id="remark" placeholder="请输入备注" autocomplete="off" class="layui-input" style="width:850px" value="">
                            <input type="hidden" id="id" name="id" value="<?=Yii::$app->request->get('id')?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/html" id="actual_priceTpl">
            <input type="text" name="actual_price" placeholder="" autocomplete="off" class="layui-input table_input" lay-filter="actual_price" style="height: 28px" value="{{d.actual_price}}" data-type="edit">
        </script>
        <script type="text/html" id="reduction_priceTpl">
            <input type="text" name="reduction_price" placeholder="" autocomplete="off" class="layui-input table_input" lay-filter="reduction_price" style="height: 28px" value="{{d.reduction_price}}" data-type="edit">
        </script>
        <script type="text/html" id="remarkTpl">
            <input type="text" name="remark" placeholder="" autocomplete="off" class="layui-input table_input" style="height: 28px" value="{{d.remark}}" data-type="edit">
        </script>

        <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
        <script>
            layui.config({
                base: '/admin/plugins/layuiadmin/' //静态资源所在路径
            }).extend({
                index: 'lib/index' //主入口模块
            }).use(['index', 'table', 'laydate','form','element','yutons_sug','upload'], function(){
                var $ = layui.$
                    ,admin = layui.admin
                    ,table = layui.table
                    ,laydate = layui.laydate
                    ,form = layui.form
                    ,element = layui.element
                    ,upload  = layui.upload

                element.render();

                //table设置默认参数
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
                    , limit: 15
                    , limits: [10, 15, 20, 25, 30]
                    , text: {
                        none: '暂无相关数据'
                    }
                });

                //展示已知数据
                var subList = <?=$list?>;
                table.render({
                    elem: '#subList'
                    ,cols: [[ //标题栏
                        {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                        ,{field: 'purchase_no', title: '原始单号', width: 225, unresize: true}
                        ,{field: 'audit_type', title: '业务类型',templet:function (e) {
                            return (e.audit_type === '0')?'采购收货':'采购退货';
                        }}
                        ,{field: 'audit_price', title: '应付金额',totalRow: true}
                        ,{field: 'settle_price', title: '已付金额',totalRow: true}
                        ,{field: 'need_pay', title: '未收金额', unresize: true,totalRow: true}
                        ,{field: 'actual_price', title: '实付金额',templet: '#actual_priceTpl', unresize: true,totalRow: true}
                        ,{field: 'reduction_price', title: '抹零金额',templet: '#reduction_priceTpl', unresize: true,totalRow: true}
                        ,{field: 'create_time', title: '应付日期', unresize: true}
                        ,{field: 'remark', title: '备注',templet: '#remarkTpl', unresize: true}
                    ]]
                    , data: subList
                    , totalRow: true
                    , page: false
                    , toolbar: false
                    , limit: Number.MAX_VALUE
                    , title: "列表"
                    ,done: function(res){
                        tableDataTemp = res;
                        var actual_price = 0;//统计结算后余额
                        layui.each(res.data, function (index, d) {
                            actual_price += Number(d.actual_price)
                        });
                        //修改 差异数量 差异总额统计单元格文本
                        $('#actual_price').val(actual_price);
                    }
                });

                // 监听提交
                form.on('submit(layuiadmin-app-form-submit)', function (data) {
                    var field = data.field; // 获取提交的字段
                    field.list = layui.table.cache['subList'];

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

                    var index = parent.layer.getFrameIndex(window.name);
                    admin.req({
                        type: "post"
                        , url: '/admin/finance/save-purchase'
                        , dataType: "json"
                        , cache: false
                        , data: field
                        , done: function () {
                            parent.layui.table.reload('order-list'); // 重载表格
                            parent.layer.close(index); // 再执行关闭
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

                //监听工具条
                table.on('tool(subList)', function (obj) {
                    var data = obj.data;
                    if (obj.event === 'del') {
                        obj.del();
                    }
                });


                //发货日期
                laydate.render({
                    elem: '#test-laydate-range-date',
                    min:minDate()
                });


                var action = {
                    edit: function(){
                        _this = $(this);
                        changeData(_this.index(),_this.attr('name'),_this.val());
                    }
                };

                /**
                 * 更新表格数据
                 * @param id
                 * @param field
                 * @param value
                 */
                function changeData(id, field, value) {
                    var tempData = [];
                    layui.table.cache['subList'].forEach(function (e,index) {
                        if (!$.isEmptyObject(e)) {
                            if (index === id) {
                                e[field] = value;
                            }
                            tempData.push(e);
                        }
                    });
                    table.reload('subList', {
                        data: tempData
                    });
                }


                $('body').on('change','.table_input',function () {
                    var type = $(this).data('type');
                    action[type] && action[type].call(this);
                });

            });

            //格式化价格
            function toDecimal2(x) {
                var f = parseFloat(x);
                if (isNaN(f)) {
                    return false;
                }
                var s = f.toString();
                var rs = s.indexOf('.');
                if (rs < 0) {
                    rs = s.length;
                    s += '.';
                }
                while (s.length <= rs + 2) {
                    s += '0';
                }
                return s;
            }

            // 设置最小可选的日期
            function minDate(){
                var now = new Date();
                now.setDate(now.getDate()+1);
                return now.getFullYear()+"-" + (now.getMonth()+1) + "-" + (now.getDate());
            }

        </script>