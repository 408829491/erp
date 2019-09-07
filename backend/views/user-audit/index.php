<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">

                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="delivery_date" class="layui-input" id="test-laydate-range-date" placeholder="选择发货日期">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="settlementStatus">
                                <option value="">结算状态</option>
                                <option value="0">未完成</option>
                                <option value="1">完成</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <select name="lineId">
                                <option value="">全部线路</option>
                                <?php foreach ($lineData as $item) : ?>
                                    <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <input type="text" name="searchText" placeholder="请输入客户名称/联系人手机号" autocomplete="off" class="layui-input" id="commodity" style="width: 300px;">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit  lay-filter="search-clear">
                            清除查询条件
                        </button>
                    </div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                    <script type="text/html" id="type">
                        {{#  if(d.type == 1){ }}
                        销售订单
                        {{#  } else { }}
                        其他
                        {{#  } }}
                     </script>
                    <script type="text/html" id="payWay">
                        {{#  if(d.pay_way == 1){ }}
                        货到付款
                        {{#  } else { }}
                        其他
                        {{#  } }}
                    </script>
                    <script type="text/html" id="auditStatus">
                        {{#  if(d.is_audit == 1){ }}
                        已对账
                        {{#  } else { }}
                        <span style="color:red">未对账</span>
                        {{#  } }}
                    </script>
                    <script type="text/html" id="settlementStatus">
                        <div name="settlementStatus2">
                            {{#  if(d.is_settlement == 0){ }}
                            <span style="color:red">未结算</span>
                            {{#  } else if (d.audit_price != d.received_price) { }}
                            <span style="color:red">部分结算</span>
                            {{#  } else { }}
                            已结算
                            {{#  } }}
                        </div>
                    </script>
                    <script type="text/html" id="order-list-barDemo">
                        <a class="layui-btn layui-btn-xs" lay-event="detail">详情</a>
                        {{#  if(d.is_settlement == 0){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="audit" style="background-color: #77CF20">对账</a>
                        {{#  } }}
                        {{#  if(d.is_settlement == 0 || d.audit_price != d.received_price){ }}
                        <a class="layui-btn layui-btn-xs" lay-event="settlement" style="background-color: #77CF20">结算</a>
                        {{#  } else { }}
                        <a class="layui-btn layui-btn-xs" lay-event="auditedList" style="background-color: #77CF20">结算记录</a>
                        {{#  } }}
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
        }).use(['index', 'table', 'laydate','element','form'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,admin = layui.admin
                ,form = layui.form;

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
                , limit: 15
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            var monthStartDate = getCurrentMondayStart();
            var currentDate = getCurrentDate();
            function getCurrentDate() {
                var now = new Date();

                return formatDate(now);
            }

            function getCurrentMondayStart() {
                // 获取当前月的第一天
                var lastMonthDate = new Date();
                lastMonthDate.setDate(1);

                return formatDate(lastMonthDate);
            }

            function formatDate(date) {
                var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
                var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                return date.getFullYear() + '-' + month + '-' + day;
            }

            table.render({
                elem: '#order-list'
                ,url: '/admin/user-audit/index-data'
                ,title: '客户对账单'
                ,cols: [[
                    {field:'source_no', title:'结算单号', width:235}
                    ,{field:'user_name', title:'客户名称'}
                    ,{field:'user_phone', title:'手机号'}
                    ,{field:'type', title:'单据类型', templet: "#type"}
                    ,{field:'audit_price', title:'应收金额'}
                    ,{field:'received_price', title:'已收金额'}
                    ,{field:'unPayPrice', title:'未收金额'}
                    ,{field:'delivery_date', title:'发货日期'}
                    ,{field:'line_name', title:'路线名称'}
                    ,{field:'driver_name', title:'司机'}
                    ,{field:'pay_way', title:'付款方式', templet: "#payWay"}
                    ,{field:'audit_no', title:'对账单号'}
                    ,{field:'is_audit', title:'对账状态', templet: "#auditStatus"}
                    ,{field:'audit_man_name', title:'对账人'}
                    ,{field:'is_settlement', title:'结算状态', templet: "#settlementStatus"}
                    ,{field:'info', title:'备注'}
                    ,{fixed: 'right', title:'操作', width:160,toolbar: '#order-list-barDemo'}
                ]]
                ,where: {filterProperty: JSON.stringify({startDate: monthStartDate, endDate: currentDate})}
                ,page: true
            });

            //监听行工具事件
            table.on('tool(order-list)', function(obj){
                switch(obj.event){
                    case 'detail':
                        var window_detail = layer.open({
                            type: 2,
                            content: '/admin/finance/purchase-settlement-detail?id='+ obj.data.id,
                            area: ['800px', '830px'],
                            title:'结算单详情',
                            maxmin: true,
                            btn: ['返回']
                        });
                        layer.full(window_detail);
                        break;
                }
            });

            //日期范围
            laydate.render({
                elem: '#test-laydate-range-date'
                , range: '到'
                , value: monthStartDate + ' 到 ' + currentDate
                , theme: 'molv'
            });

            //清除查询条件
            form.on('submit(search-clear)', function(obj){
                form.val("layui-form", {
                    'delivery_date': '',
                    'is_pay': '',
                    'create_time': '',
                    'keyword': '',
                    'source':''
                })
            });

            //监听搜索
            form.on('submit(search)', function(data){
                var field = data.field;
                var deliveryDate = field.delivery_date.split(' 到 ');
                field.startDate = deliveryDate[0];
                field.endDate = deliveryDate[1];

                var field1 = {};
                field1.filterProperty=JSON.stringify(field);

                //执行重载
                table.reload('order-list', {
                    where: field1
                });
            });

            //监听工具条
            table.on('tool(order-list)', function (obj) {
                var data = obj.data;
                if (obj.event === 'detail') {
                    // 详情
                    detailView(obj);
                } else if (obj.event === 'audit') {
                    // 对账 订单必须已完成才能对账
                    admin.req({
                        type: "get"
                        , url: '/admin/user-audit/find-order-is-completed'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            id: data.source_id
                        }
                        , done: function (res) {
                            if (res.data) {
                                // 弹出提示框
                                layer.confirm('销售单处于“待收货状态”，点击“确定”继续处理，销售单状态将自动变为“已完成”', {icon: 3, title:'提示'}, function (index) {
                                    admin.req({
                                        type: "get"
                                        , url: '/admin/order/finish'
                                        , dataType: "json"
                                        , cache: false
                                        , data: {
                                            order_id: data.source_id
                                        }
                                        , done: function () {
                                            layer.close(index);
                                            // 跳转对账编辑
                                            auditFunction(obj);
                                        }
                                    });
                                });
                            } else {
                                // 订单已完成直接对账
                                auditFunction(obj);
                            }
                        }
                    });
                } else if (obj.event === 'settlement') {
                    // 结算
                    settlementFunction(obj);
                } else if (obj.event === 'auditedList') {
                    // 结算记录
                    layer.open({
                        type: 2
                        , title: '结算记录(客户名称：' + data.user_name + ')'
                        , content: '/admin/user-audit/audited-list?orderId=' + obj.data.source_no
                        , maxmin: true
                        , area: ['80%', '80%']
                        , btn: ['确定', '取消']
                        , yes: function (index, layero) {
                            layer.close(index); //关闭弹层
                        }
                    });
                }
            });

            // 弹出详情
            function detailView(obj) {
                layer.open({
                    type: 2
                    , title: '详情'
                    , content: '/admin/user-audit/audit-detail-view?id=' + obj.data.id
                    , maxmin: true
                    , area: ['80%', '80%']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        layer.close(index); //关闭弹层
                    }
                });
            }

            // 弹出对账框
            function auditFunction(obj) {
                layer.open({
                    type: 2
                    , title: '对账信息'
                    , content: '/admin/user-audit/audit-update?id=' + obj.data.id
                    , maxmin: true
                    , area: ['80%', '80%']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        // 提交
                        var iframeWindow = window['layui-layer-iframe' + index];

                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                        //监听提交
                        iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function (data1) {

                            var field = data1.field; //获取提交的字段
                            field["id"] = obj.data.id;
                            field['details'] = JSON.stringify(iframeWindow.layui.table.cache.subList);

                            //提交 Ajax 成功后，静态更新表格中的数据
                            admin.req({
                                type: "post"
                                , url: '/admin/user-audit/update-save'
                                , dataType: "json"
                                , cache: false
                                , data: field
                                , done: function (res) {
                                    obj.update({
                                        is_audit: 1
                                        , audit_man_id: res.data.audit_man_id
                                        , audit_man_name: res.data.audit_man_name
                                        , audit_price: res.data.audit_price
                                    }); //数据更新

                                    layer.close(index); //关闭弹层
                                }
                            });
                        });
                        submit.trigger('click');
                    }
                });
            }

            // 弹出结算框
            function settlementFunction(obj) {
                layer.open({
                    type: 2
                    , title: '结算信息'
                    , content: '/admin/user-audit/settlement-update?id=' + obj.data.id
                    , maxmin: true
                    , area: ['80%', '80%']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        // 提交
                        var iframeWindow = window['layui-layer-iframe' + index];

                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                        //监听提交
                        iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function (data1) {

                            var field = data1.field; //获取提交的字段
                            field["id"] = obj.data.id;
                            // 实收金额+抹零金额不能大于未收金额
                            var realObj = layero.find('iframe').contents().find('[name = realPay]');
                            var skipPayObj = layero.find('iframe').contents().find('[name = skipPay]')
                            var realPay = realObj.val();
                            var skipPay = skipPayObj.val();
                            var subData = iframeWindow.layui.table.cache.subList[0];
                            var unPay = subData.audit_price - subData.received_price;
                            var totalSettlementPrice = parseFloat(realPay) + parseFloat(skipPay);
                            if (unPay < totalSettlementPrice) {
                                iframeWindow.layui.layer.msg('实收金额加抹零金额不能大于未付金额');
                                realObj.css('border-color','rgb(255, 87, 34)');
                                skipPayObj.css('border-color','rgb(255, 87, 34)');
                                return ;
                            }
                            field['actual_price'] = realPay;
                            field['reduction_price'] = skipPay;
                            field['audit_price'] = subData.audit_price;
                            field['received_price'] = subData.received_price;
                            field['type'] = subData.type;

                            // 凭证图片添加
                            var imgListData = "";
                            layero.find('iframe').contents().find('.image_div_img').each(function () {
                                imgListData += this.src;
                                imgListData += ":;";
                            });
                            if (imgListData != undefined) {
                                imgListData = imgListData.substring(0, imgListData.length - 2);
                                field['pic'] = imgListData;
                            }
                            console.log(subData.audit_price);

                            //提交 Ajax 成功后，静态更新表格中的数据
                            admin.req({
                                type: "post"
                                , url: '/admin/user-audit/settlement-save'
                                , dataType: "json"
                                , cache: false
                                , data: field
                                , done: function (res) {
                                    var auditReceivedPrice = (parseFloat(obj.data.received_price) + parseFloat(realPay) + parseFloat(skipPay)).toFixed(2);
                                    obj.update({
                                        received_price: auditReceivedPrice
                                        , unPayPrice: (subData.audit_price - auditReceivedPrice).toFixed(2)
                                    }); //数据更新

                                    // 删除对账按钮
                                    obj.tr.find('[lay-event = audit]').remove();
                                    // 根据情况删除结算按钮跟显示结算状态
                                    if (subData.audit_price == auditReceivedPrice) {
                                        // 结算完成
                                        obj.tr.find('[lay-event = settlement]').remove();
                                        obj.tr.find('[name = settlementStatus2]').html('已结算');
                                    } else {
                                        obj.tr.find('[name = settlementStatus2]').html('<span style="color:red">部分结算</span>');
                                    }

                                    layer.close(index); //关闭弹层
                                }
                            });
                        });
                        submit.trigger('click');
                    }
                });
            }

        });
    </script>