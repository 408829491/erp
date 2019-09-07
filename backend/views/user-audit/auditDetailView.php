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
                            <?= $audit_price ?>
                        </div>
                    </div>
                    <div class="layui-inline" style="display: inline-flex;align-items: center;">
                        <label class="layui-form-label">差异金额:</label>
                        <div class="layui-input-inline">
                            <?= $total_price - $audit_price ?>
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
                    <?= $info ?>
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
        }).use(['index', 'table','form','element'], function(){
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

            $('.layui-btn').on('click', function(e){
                var type = $(this).data('type');
                action[type] && action[type].call(this);
            });

        });
    </script>