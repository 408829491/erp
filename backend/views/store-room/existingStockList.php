<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select lay-filter="type" name="typeId">
                            <option value="">一级分类</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 250px">
                        <input type="text" name="searchText" placeholder="请输入商品编码\名称\助记码\别名" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="order-list-barDemo">
                <a class="layui-btn layui-btn-xs" lay-event="set-top" style="background-color: #77CF20">设置上下限</a>
               <!-- <a class="layui-btn layui-btn-xs" lay-event="recorder" style="background-color: #77CF20">成本变更记录</a>-->
            </script>
            <script type="text/html" id="stockTotalPrice">
                {{  (parseFloat(d.price) * parseFloat(d.sell_stock)).toFixed(2) }}
             </script>
        </div>
    </div>
</div>

    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table'], function () {
            var table = layui.table
                , form = layui.form
                , admin = layui.admin;

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
                , limit: 15
                , limits: [10, 15, 20, 25, 30]
                , text: {
                    none: '暂无相关数据'
                }
            });

            //监听搜索
            form.on('submit(search)', function (data) {
                   //执行重载
                table.reload('list', {
                    where: data.field
                    , page: {curr: 1}
                });
            });

            var $ = layui.$, active = {
                batchdel: function () {
                    var checkStatus = table.checkStatus('list')
                        , checkData = checkStatus.data; //得到选中的数据

                    if (checkData.length === 0) {
                        return layer.msg('请选择数据');
                    }

                    var field = {};
                    var fieldData = "";
                    for (var i = 0; i < checkData.length; i++) {
                        fieldData = fieldData + checkData[i].id + ",";
                    }
                    fieldData = fieldData.substring(0, fieldData.length - 1);
                    field['ids'] = fieldData;
                    layer.confirm('确定删除吗？', function (index) {

                        //执行 Ajax 后重载
                        admin.req({
                            type: "post"
                            , url: '/admin/commodity-list/delete'
                            , dataType: "json"
                            , cache: false
                            , data: field
                            , done: function () {
                                table.reload('list');
                                layer.msg('已删除');
                            }
                        });
                    });
                },
                add: function () {
                    var index = layer.open({
                        type: 2
                        , title: '添加'
                        , content: '/admin/commodity-list/create'
                        , maxmin: true
                        , area: ['600px', '300px']
                        , btn: ['确定', '取消']
                        , yes: function (index, layero) {
                            //点击确认触发 iframe 内容中的按钮提交
                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            submit.click();
                        }
                    });
                    layer.full(index);
                }
            };

            // 获取分类
            var typeDataList;

            admin.req({
                type: "get"
                , url: '/admin/commodity-category/first-tier-data'
                , dataType: "json"
                , cache: false
                , data: {}
                , done: function (res) {
                    typeDataList = res.data;
                    for (var i = 0; i < typeDataList.length; i++) {
                        $("[name=typeId]").append('<option value="' + typeDataList[i].id + '">' + typeDataList[i].name + '</option>');
                    }
                    form.render();
                }
            });

            // 渲染list
            table.render({
                elem: '#list'
                , url: '/admin/commodity-list/get-list-data'
                , cols: [[
                    {field: 'id', width: 100, title: 'ID', sort: true}
                    , {field: 'name', title: '商品名称', minWidth: 150}
                    , {field: 'unit', title: '单位'}
                    , {field: 'parent_type_name', title: '一级分类'}
                    , {field: 'type_name', title: '二级分类'}
                    , {field: 'sell_stock', title: '现有库存'}
                    , {field: 'price', title: '库存均价'}
                    , {field: 'channel_type', title: '库存总金额', templet: "#stockTotalPrice"}
                    , {field: 'stock_limit_up_num', title: '库存上限'}
                    , {field: 'stock_limit_down_num', title: '库存下限'}
                    , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo', width: 200}
                ]]
                , title: "列表"
            });

            //监听工具条
            table.on('tool(list)', function (obj) {
                var data = obj.data;
                if (obj.event === 'set-top') {
                    layer.open({
                        type: 2
                        , title: '设置上下限'
                        , content: '/admin/store-room/set-top?id=' + data.pid
                        , maxmin: true
                        , area: ['600px', '350px']
                        , btn: ['确定', '取消']
                        , yes: function (index, layero) {
                            // 提交
                            var iframeWindow = window['layui-layer-iframe' + index];

                            var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                            //监听提交
                            iframeWindow.layui.form.on('submit(layuiadmin-app-form-submit)', function (data1) {

                                var field = data1.field; //获取提交的字段
                                field["id"] = data.id;

                                //提交 Ajax 成功后，静态更新表格中的数据
                                admin.req({
                                    type: "post"
                                    , url: '/admin/store-room/set-top-save'
                                    , dataType: "json"
                                    , cache: false
                                    , data: field
                                    , done: function () {
                                        obj.update({
                                            stock_limit_up_num: field.stock_limit_up_num
                                            , stock_limit_down_num: field.stock_limit_down_num
                                        }); //数据更新

                                        form.render();
                                        layer.close(index); //关闭弹层
                                    }
                                });
                            });
                            submit.trigger('click');
                        }
                    });
                }
            });

            $('.layui-btn.layuiadmin-btn-list').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

        });

    </script>
