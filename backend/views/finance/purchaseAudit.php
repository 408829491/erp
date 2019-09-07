<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <style>
        .layui-table-cell {
            height: 39px;
            line-height: 36px;
        }
    </style>
    <div class="layui-card">
        <div class="layui-card-header">基础信息</div>
        <div class="layui-card-body">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">业务单号:</label>
                        <div class="layui-input-inline">
                            <?= $purchase_no ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">供应商:</label>
                        <div class="layui-input-inline">
                            <?= ($provider_name) ? $provider_name : '市场自采' ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">对账单号:</label>
                        <div class="layui-input-inline">
                            <?= $audit_no ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">对账人员:</label>
                        <div class="layui-input-inline">
                            <?= $author ?>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">创建日期:</label>
                        <div class="layui-input-inline">
                            <?= $create_time ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">结算状态:</label>
                        <div class="layui-input-inline">
                            <?= ($is_settlement) ? '已结算' : '未结算' ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">对账状态:</label>
                        <div class="layui-input-inline">
                            <?= ($is_audit) ? '已对账' : '未对账' ?>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">单据金额:</label>
                        <div class="layui-input-inline">
                            <?= $price ?>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">对账金额:</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input layui-inline layui-disabled" value="0"
                                   id="total_price">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">差异金额:</label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input layui-inline layui-disabled" value="0"
                                   id="diff_total_price">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">收货商品清单</div>
            <table class="layui-hide" id="subList" lay-filter="subList"></table>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                        class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
        <div class="layui-card">
            <div class="layui-card-header">备注</div>
            <div class="layui-card-body">
                <div class="layui-inline">
                    <input type="text" name="remark" id="remark" placeholder="请输入备注" autocomplete="off"
                           class="layui-input" style="width:850px" value="">

                </div>
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <input type="hidden" id="id" name="id" value="<?= $id ?>">
            <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit"
                   value="确认添加">
        </div>
        <script type="text/html" id="numTpl">
            <div style="display: flex;align-items: center" class="edit">
                <div style="line-height:20px;">
                    {{# if(d.diff_num == d.num){ }}
                    <p>{{d.num}}</p>
                    {{# } else { }}
                    <p style="text-decoration: line-through;">{{d.num}}</p>
                    <p style="color: red;">{{d.diff_num}}</p>
                    {{# } }}
                </div>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs layui-inline"
                        style="border:0;height:25px;display: none;margin-left: auto;" data-type="edit" rel="diff_num" id="{{d.id}}"><i
                        class="layui-icon layui-icon-survey" style="color:#5FB878"></i></button>
            </div>
        </script>

        <script type="text/html" id="priceTpl">
            <div style="display: flex;align-items: center" class="edit">
                <div style="line-height:20px;">
                    {{# if(d.diff_price == d.purchase_price){ }}
                    <p>{{d.purchase_price}}</p>
                    {{# } else { }}
                    <p style="text-decoration: line-through;">{{d.purchase_price}}</p>
                    <p style="color: red;">{{d.diff_price}}</p>
                    {{# } }}
                </div>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs layui-inline"
                        style="border:0;height:25px;margin-left: auto;display: none;" data-type="edit" rel="diff_price" id="{{d.id}}"><i
                        class="layui-icon layui-icon-survey" style="color:#5FB878"></i></button>
            </div>
        </script>
        <script type="text/html" id="totalPriceTpl">
            <div style="display: flex;align-items: center" class="edit">
                <div style="line-height:20px;">
                    {{# if(d.diff_total_price == d.total_price){ }}
                    <p>{{d.total_price}}</p>
                    {{# } else { }}
                    <p style="text-decoration: line-through;">{{d.total_price}}</p>
                    <p style="color: red;">{{d.diff_total_price}}</p>
                    {{# } }}
                </div>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-xs layui-inline"
                        style="border:0;height:25px;display: none;margin-left: auto;" data-type="edit" rel="diff_total_price" id="{{d.id}}"><i
                        class="layui-icon layui-icon-survey" style="color:#5FB878"></i></button>
            </div>
        </script>
        <script type="text/html" id="imgTpl">
            <img style="display: inline-block; width: 50%; height: 100%;"
                 src='{{d.pic}}?x-oss-process=image/resize,h_50'>
        </script>
    </div>

    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate', 'form', 'element'], function () {
            var $ = layui.$
                , admin = layui.admin
                , table = layui.table
                , laydate = layui.laydate
                , form = layui.form
                , element = layui.element;

            element.render();

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
                    , {field: 'commodity_name', title: '商品名称', minWidth: 100}
                    , {field: 'type_name', title: '分类', minWidth: 120}
                    , {field: 'notice', title: '描述', minWidth: 120}
                    , {field: 'unit', title: '单位', width: 80}
                    , {field: 'diff_num', title: '收货数量', width: 150, totalRow: true, templet: '#numTpl'}
                    , {field: 'diff_price', title: '收货单价', width: 150, templet: '#priceTpl'}
                    , {field: 'diff_total_price', title: '收货金额', width: 150, totalRow: true, templet: '#totalPriceTpl'}
                    , {
                        field: 'reduce_num', title: '差异数量', totalRow: true, templet: function (d) {
                            return (d.num - d.diff_num).toFixed(2);
                        }
                    }
                    , {
                        field: 'reduce_price', title: '差异单价', templet: function (d) {
                            return (d.price - d.diff_price).toFixed(2);
                        }
                    }
                    , {
                        field: 'reduce_total_price', title: '差异金额', totalRow: true, templet: function (d) {
                            return (d.total_price - d.diff_total_price).toFixed(2);
                        }
                    }
                ]]
                , data: subList
                , page: false
                , toolbar: false
                , totalRow: true
                , limit: Number.MAX_VALUE
                , title: "列表"
                , done: function (res, curr, count) {
                    tableDataTemp = res;
                    //表格刷新后重新绑定按钮事件
                    var reduce_total_price = reduce_num = diff_total_price = 0;//统计结算后余额
                    layui.each(res.data, function (index, d) {
                        reduce_num += Number(d.num) - Number(d.diff_num);
                        reduce_total_price += Number(d.total_price) - Number(d.diff_total_price);
                        diff_total_price += Number(d.diff_total_price);
                    });
                    //修改 差异数量 差异总额统计单元格文本
                    this.elem.next().find('.layui-table-total td[data-field="reduce_num"] .layui-table-cell').text(reduce_num);
                    this.elem.next().find('.layui-table-total td[data-field="reduce_total_price"] .layui-table-cell').text(reduce_total_price);
                    this.elem.next().find('.layui-table-total td[data-field="diff_num"] .layui-table-cell').css("color", "red");
                    this.elem.next().find('.layui-table-total td[data-field="diff_total_price"] .layui-table-cell').css("color", "red");
                    $('#diff_total_price').val(reduce_total_price);
                    $('#total_price').val(diff_total_price);
                }
            });

            //监听工具条
            table.on('tool(subList)', function (obj) {
                var data = obj.data;
                if (obj.event === 'del') {
                    obj.del();
                }
            });


            //修改对账数额
            var action = {
                edit: function () {
                    var _this = $(this);
                    var obj = _this.siblings('div');
                    var el = _this.find('i')
                        , value = obj.find('p').text()
                        , audit_value = obj.find('p').next().text()
                        , input_value = obj.find('input').val()
                        , index = _this.parents('tr').index()
                        , field = _this.attr('rel');
                    if (el.hasClass('layui-icon-survey')) {
                        el.addClass('layui-icon-ok');
                        el.removeClass('layui-icon-survey');
                        value = audit_value ? audit_value : value;
                        obj.html('<input type="text" class="layui-input layui-inline" style="height: 30px;" value="' + value + '">');
                    } else {
                        el.addClass('layui-icon-survey');
                        el.removeClass('layui-icon-ok');
                        changeData(field, input_value, index);
                    }

                }
            };

            /**
             * 改变表格指定行列数据
             * @param index
             * @param field
             * @param value
             */
            function changeData(field, value, index) {
                tempData = layui.table.cache['subList'];
                var row = tempData[index];
                row[field] = value;
                if (field === 'diff_total_price') {
                    row.diff_price = (row.diff_total_price / row.diff_num).toFixed(2);
                } else {
                    row.diff_total_price = (row.diff_num * row.diff_price).toFixed(2);
                }
                table.reload('subList', {
                    data: tempData
                });
            }

            $('body').on('mouseover mouseout', '.edit', function (event) {
                if (event.type == 'mouseover') {
                    // 鼠标悬停
                    $(this).find('.layui-btn').css('display', 'block');
                } else {
                    // 鼠标离开
                    $(this).find('.layui-btn').css('display', 'none');
                }
            });


            // 监听数据提交
            form.on('submit(layuiadmin-app-form-submit)', function (data) {
                var field = data.field; // 获取提交的字段
                field.commodity_list = layui.table.cache['subList'];
                var subListIndex = 0;
                field.commodity_list.forEach(function (e) {
                    if (!$.isEmptyObject(e)) {
                        e.commodity_id = e.id;
                        subListIndex += 1;
                    }
                });
                var index = parent.layer.getFrameIndex(window.name);
                admin.req({
                    type: "post"
                    , url: '/admin/finance/purchase-audit-save'
                    , dataType: "json"
                    , cache: false
                    , data: field
                    , done: function () {
                        parent.layui.table.reload('order-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }
                });
            });


            $('body').on('click', '.layui-btn', function () {
                var type = $(this).data('type');
                action[type] && action[type].call(this);
            });

        });

    </script>