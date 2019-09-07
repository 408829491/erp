<style type="text/css">
    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header" style="display: flex;align-items: center;"><span style="border-radius: 50%;width: 12px;height: 12px;background-color: rgb(60, 195, 71);margin-right: 5px;"></span>基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item" style="margin-bottom: 0">
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">业务单号:</label>
                        <div class="layui-input-inline">
                            <?= $source_no ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">客户名称:</label>
                        <div class="layui-input-inline">
                            <?= $user_name ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">对账单号:</label>
                        <div class="layui-input-inline">
                            <?= $audit_no ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">对账人员:</label>
                        <div class="layui-input-inline">
                            <?= $audit_man_name ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">创建日期:</label>
                        <div class="layui-input-inline">
                            <?= $create_datetime ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">发货日期:</label>
                        <div class="layui-input-inline">
                            <?= $delivery_date ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">单据状态:</label>
                        <div class="layui-input-inline">
                            <?php if ( $is_settlement == 1 ) : ?>
                            已结算
                            <?php else : ?>
                            未结算
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">对账状态:</label>
                        <div class="layui-input-inline">
                            <?php if ( $is_audit == 1 ) : ?>
                            已对账
                            <?php else : ?>
                            未对账
                            <?php endif; ?>
                        </div>
                    </div>
                </div class="layui-form-item">
                <div class="layui-form-item">
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">单据金额:</label>
                        <div class="layui-input-inline">
                            <?= $total_price ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">对账金额:</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" style="background-color: #eaeaea;" name="auditPrice" disabled value="<?= $audit_price ?>"/>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">差异金额:</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" style="background-color: #eaeaea;" name="subTotalPrice" disabled value="<?= $total_price - $audit_price ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header" style="display: flex;align-items: center;"><span style="border-radius: 50%;width: 12px;height: 12px;background-color: rgb(60, 195, 71);margin-right: 5px;"></span>订单商品清单</div>
            <div class="layui-card-body">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
                <script type="text/html" id="sendNum">
                    <input type="hidden" name="id" value="{{ d.id }}">
                    <input type="hidden" name="diffNum" value="{{ d.diff_num }}">
                    <input type="hidden" name="diffPrice" value="{{ d.diff_price }}">
                    <input type="hidden" name="actualNum" value="{{ d.actual_num }}">
                    <input type="hidden" name="totalPrice" value="{{ d.total_price }}">
                    <div name="sendNum" style="display: flex;align-items: center;">
                        {{#  if( parseFloat(d.actual_num) == parseFloat(d.diff_num) ){ }}
                        <span>{{ d.actual_num }}</span>
                        <i class="layui-icon layui-icon-edit" name="sendNumUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                        {{#  } else { }}
                        <div style="display: flex;flex-direction: column;">
                        <span style="display: flex;text-decoration:line-through;">{{ d.actual_num }}</span>
                        <span style="display: flex;color: red;">{{ d.diff_num }}</span>
                    </div>
                    <i class="layui-icon layui-icon-edit" name="sendNumUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                    {{#  } }}
                </div>
            </script>
            <script type="text/html" id="sendPrice">
                <input type="hidden" name="id" value="{{ d.id }}">
                <input type="hidden" name="diffNum" value="{{ d.diff_num }}">
                <input type="hidden" name="diffPrice" value="{{ d.diff_price }}">
                <input type="hidden" name="price" value="{{ d.price }}">
                <input type="hidden" name="totalPrice" value="{{ d.total_price }}">
                <div name="sendNum" style="display: flex;align-items: center;">
                    {{#  if( parseFloat(d.price) == parseFloat(d.diff_price) ){ }}
                    <span>{{ d.price }}</span>
                    <i class="layui-icon layui-icon-edit" name="sendPriceUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                    {{#  } else { }}
                    <div style="display: flex;flex-direction: column;">
                        <span style="display: flex;text-decoration:line-through;">{{ d.price }}</span>
                        <span style="display: flex;color: red;">{{ d.diff_price }}</span>
                    </div>
                    <i class="layui-icon layui-icon-edit" name="sendPriceUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                    {{#  } }}
                </div>
            </script>
            <script type="text/html" id="sendTotalPrice">
                <input type="hidden" name="id" value="{{ d.id }}">
                <input type="hidden" name="diffNum" value="{{ d.diff_num }}">
                <input type="hidden" name="diffPrice" value="{{ d.diff_price }}">
                <input type="hidden" name="price" value="{{ d.price }}">
                <input type="hidden" name="totalPrice" value="{{ d.total_price }}">
                <input type="hidden" name="diffTotalPrice" value="{{ d.diff_total_price }}">
                <div name="sendNum" style="display: flex;align-items: center;">
                    {{#  if( parseFloat(d.total_price) == parseFloat(d.diff_total_price) ){ }}
                    <span>{{ d.total_price }}</span>
                    <i class="layui-icon layui-icon-edit" name="sendTotalPriceUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                    {{#  } else { }}
                    <div style="display: flex;flex-direction: column;">
                        <span style="display: flex;text-decoration:line-through;">{{ d.total_price }}</span>
                        <span style="display: flex;color: red;">{{ d.diff_total_price }}</span>
                    </div>
                    <i class="layui-icon layui-icon-edit" name="sendTotalPriceUpdate" style="font-size: 20px; margin-left: auto;display: none;"></i>
                    {{#  } }}
                </div>
            </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-form-item">
            <div class="layui-inline" style="display: inline-flex;align-items: center;">
                <label class="layui-form-label">备注:</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" name="info" style="width: 500px"/>
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
        }).use(['index', 'table', 'laydate','form','element'], function(){
            var $ = layui.$
                ,admin = layui.admin
                ,table = layui.table
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
            var subList = <?=json_encode($details)?>;
            table.render({
                elem: '#subList'
                ,cols: [[ //标题栏
                    {field:'id', title:'ID', width:80, unresize: true, sort: true, totalRowText: '合计:'}
                    ,{field: 'commodity_name', title: '商品名称'}
                    ,{field: 'notice', title: '描述'}
                    ,{field: 'unit', title: '发货单位'}
                    ,{field: 'diff_num', title: '发货数量', templet: "#sendNum", totalRow: true}
                    ,{field: 'price', templet: "#sendPrice", title: '发货单价（元）'}
                    ,{field: 'diff_total_price', templet: "#sendTotalPrice", title: '发货金额（元）', totalRow: true}
                    ,{field: 'subNum', title: '差异数量', totalRow: true}
                    ,{field: 'subPrice', title: '差异单价（元）' , templet:"#diffPrice"}
                    ,{field: 'subTotalPrice', title: '差异金额（元）', templet:"#diffTotalPrice", totalRow: true}
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

            $('body').on('mouseover mouseout', '[name = sendNum]', function (event) {
                if (event.type == 'mouseover') {
                    // 鼠标悬停
                    $(this).find('.layui-icon-edit').css('display', 'flex');
                } else {
                    // 鼠标离开
                    $(this).find('.layui-icon-edit').css('display', 'none');
                }
            });

            $('body').on('click', '[name = sendNumUpdate]', function () {
                var divObj = $(this).parent();
                var diffNum = divObj.parent().find('[name = diffNum]').val();
                divObj.html('<input class="layui-input" style="height: 25px;" name="diffNum" value="' + diffNum + '"><i class="layui-icon layui-icon-ok" name="sendNumUpdateComplete" style="font-size: 20px; margin-left: auto;"></i>');
            });

            // 根据数量修改子表
            $('body').on('click', '[name = sendNumUpdateComplete]', function () {
                // 计算值
                var divObj = $(this).parent();
                var diffNum = divObj.find('[name = diffNum]').val();
                var diffPrice = divObj.parent().find('[name = diffPrice]').val();
                var actualNum = divObj.parent().find('[name = actualNum]').val();
                var totalPrice = divObj.parent().find('[name = totalPrice]').val();

                // 修改子表
                var index = divObj.parent().parent().parent().index();
                var data= layui.table.cache.subList;
                data[index].diff_num = diffNum;
                data[index].diff_total_price = (diffNum * diffPrice).toFixed(2);
                data[index].subNum = (actualNum - diffNum).toFixed(2);
                data[index].subTotalPrice = (totalPrice - data[index].diff_total_price).toFixed(2);
                table.reload('subList', {data: data});
                updateAuditPrice();
            });

            $('body').on('click', '[name = sendPriceUpdate]', function () {
                var divObj = $(this).parent();
                var diffPrice = divObj.parent().find('[name = diffPrice]').val();
                divObj.html('<input class="layui-input" style="height: 25px;" name="diffPrice" value="' + diffPrice + '"><i class="layui-icon layui-icon-ok" name="sendPriceUpdateComplete" style="font-size: 20px; margin-left: auto;"></i>');
            });

            // 根据单价修改子表
            $('body').on('click', '[name = sendPriceUpdateComplete]', function () {
                // 计算值
                var divObj = $(this).parent();
                var diffPrice = divObj.find('[name = diffPrice]').val();
                var diffNum = divObj.parent().find('[name = diffNum]').val();
                var price = divObj.parent().find('[name = price]').val();
                var totalPrice = divObj.parent().find('[name = totalPrice]').val();

                // 修改子表
                var index = divObj.parent().parent().parent().index();
                var data = layui.table.cache.subList;
                data[index].diff_total_price = (diffNum * diffPrice).toFixed(2);
                data[index].diff_price = diffPrice;
                data[index].subPrice = (price - diffPrice).toFixed(2);
                data[index].subTotalPrice = (totalPrice - data[index].diff_total_price).toFixed(2);
                table.reload('subList', {data: data});
                updateAuditPrice();
            });

            $('body').on('click', '[name = sendTotalPriceUpdate]', function () {
                var divObj = $(this).parent();
                var diffTotalPrice = divObj.parent().find('[name = diffTotalPrice]').val();
                divObj.html('<input class="layui-input" style="height: 25px;" name="diffTotalPrice" value="' + diffTotalPrice + '"><i class="layui-icon layui-icon-ok" name="sendTotalPriceUpdateComplete" style="font-size: 20px; margin-left: auto;"></i>');
            });

            // 根据总价修改子表
            $('body').on('click', '[name = sendTotalPriceUpdateComplete]', function () {
                // 计算值
                var divObj = $(this).parent();
                var diffTotalPrice = divObj.find('[name = diffTotalPrice]').val();
                var diffNum = divObj.parent().find('[name = diffNum]').val();
                var price = divObj.parent().find('[name = price]').val();
                var totalPrice = divObj.parent().find('[name = totalPrice]').val();

                // 修改子表
                var index = divObj.parent().parent().parent().index();
                var data = layui.table.cache.subList;
                data[index].diff_total_price = diffTotalPrice;
                data[index].diff_price = (diffTotalPrice / diffNum).toFixed(2);
                data[index].subPrice = (price - data[index].diff_price).toFixed(2);
                data[index].subTotalPrice = (totalPrice - data[index].diff_total_price).toFixed(2);
                table.reload('subList', {data: data});
                updateAuditPrice();
            });

            $('.layui-btn').on('click', function(e){
                var type = $(this).data('type');
                action[type] && action[type].call(this);
            });

            // 修改主表的差异金额与对账总额
            function updateAuditPrice() {
                var data = table.cache.subList;
                var auditPrice = 0;
                var subTotalPrice = 0;

                for (var i = 0; i < data.length; i++) {
                    auditPrice += parseFloat(data[i].diff_total_price);
                    subTotalPrice += parseFloat(data[i].subTotalPrice)
                }

                $('[name = auditPrice]').val(auditPrice);
                $('[name = subTotalPrice]').val(subTotalPrice);
            }

        });
    </script>