<style type="text/css">
    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header" style="display: flex;align-items: center;"><span style="border-radius: 50%;width: 12px;height: 12px;background-color: rgb(60, 195, 71);margin-right: 5px;"></span>结算单列表</div>
        <div class="layui-card-body">
            <table class="layui-hide" id="subList" lay-filter="subList"></table>
            <script type="text/html" id="billType">
                {{#  if(d.bill_type == 1){ }}
                销售订单
                {{#  } else { }}
                其他
                {{#  } }}
             </script>
            <script type="text/html" id="payWay">
                {{#  if(d.pay_way == 0){ }}
                微信
                {{#  } else if (d.pay_way == 1) { }}
                支付宝
                {{#  } else if (d.pay_way == 2) { }}
                转账
                {{#  } else if (d.pay_way == 3) { }}
                现金
                {{#  } else { }}
                文本
                {{#  } }}
             </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
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
            var subList = <?=json_encode($list)?>;
            table.render({
                elem: '#subList'
                ,cols: [[ //标题栏
                    {field:'id', title:'ID', width:80, unresize: true, sort: true, totalRowText: '合计:'}
                    ,{field: 'refer_no', title: '原始单号'}
                    ,{field: 'bill_type', title: '业务类型', templet: "#billType"}
                    ,{field: 'settle_no', title: '结算单号'}
                    ,{field: 'create_user', title: '制单人'}
                    ,{field: 'pay_user', title: '交款人'}
                    ,{field: 'pay_way', title: '交款方式',templet: "#payWay"}
                    ,{field: 'actual_price', title: '实收金额', totalRow: true}
                    ,{field: 'reduction_price', title: '抹零金额', totalRow: true}
                    ,{field: 'create_time', title: '结算日期'}
                    ,{field: 'remark', title: '备注'}
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