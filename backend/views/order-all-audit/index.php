<style>
    #steps {
    }

    .step-item {
        display: inline-block;
        line-height: 26px;
        position: relative;
        background: #ffffff;
    }

    .step-item-tail {
        width: 100%;
        padding: 0 10px;
        position: absolute;
        left: 0;
        top: 13px;
    }

    .step-item-tail i {
        display: inline-block;
        width: 100%;
        height: 1px;
        vertical-align: top;
        background: #c2c2c2;
        position: relative;
    }

    .step-item-tail-done {
        background: #009688 !important;
    }

    .step-item-head {
        position: relative;
        display: inline-block;
        height: 26px;
        width: 26px;
        text-align: center;
        vertical-align: top;
        color: #009688;
        border: 1px solid #009688;
        border-radius: 50%;
        background: #ffffff;
    }

    .step-item-head.step-item-head-active {
        background: #009688;
        color: #ffffff;
    }

    .step-item-main {
        background: #ffffff;
        display: block;
        position: relative;
    }

    .step-item-main-title {
        font-weight: bolder;
        color: #555555;
    }

    .step-item-main-desc {
        color: #aaaaaa;
    }

    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }

    .sell_stock {
        width: 100px;
    }

    .layui-form-checked[lay-skin=primary] i {
        border-color: #77CF20 !important;
        background-color: #77CF20 !important;
        font-weight: bold;
    }

</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div id="steps"></div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="delivery_date" class="layui-input" id="test-laydate-range-date"
                               placeholder="请选择发货日期">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select lay-filter="type" name="type_id">
                            <option value="">一级分类</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 250px">
                        <input type="text" name="keyword" placeholder="请输入商品名称" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
                <div class="layui-inline" style="padding-left:40px;line-height:22px;height: 40px;">
                    <input type="checkbox" name="sync" id="sync" lay-skin="primary" title="本次刷价同步更新商品资料" checked>
                </div>
                <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">
                    <button class="layui-btn  layui-btn-normal" data-type="allSort"
                            style="background-color: #77CF20" lay-submit lay-filter="edit" id="edit">编辑
                    </button>

                </div>

            </div>
        </div>

        <div class="layui-card-body">
            <table id="list" lay-filter="list"></table>
        </div>
    </div>
</div>

<script type="text/html" id="sell_stock">
    <div class="layui-row">
        <div class="layui-inline layui-col-md4">
            门店价格:<input type="text" name="sell_stock" placeholder="" autocomplete="off"
                        class="sell_stock layui-input layui-disabled"
                        res="{{d.commodity_id}}" lay-data="{{d.order_id}}" lay-filter="sell_stock" style="height: 32px"
                        value="{{d.price}}">
        </div>
        <div class="layui-inline layui-col-md4">
            B端价格:<input type="text" name="sell_stock" placeholder="" autocomplete="off"
                        class="sell_stock layui-input layui-disabled"
                        res="{{d.commodity_id}}" lay-data="{{d.order_id}}" lay-filter="sell_stock" style="height: 32px"
                        value="{{d.price}}">
        </div>
    </div>

</script>

<script type="text/html" id="tool_in_price">
    <div>{{d.in_price}}<a<i style="padding:3px;color:#77CF20;cursor:pointer;" class="layui-icon layui-icon-form"
                            lay-event="history"></i></div>
</script>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'steps', 'laydate'], function () {
        var table = layui.table
            , laydate = layui.laydate
            , form = layui.form
            , steps = layui.steps
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
            , limit: 30
            , limits: [30, 60, 90]
            , text: {
                none: '暂无相关数据'
            }
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            //执行重载
            table.reload('list', {
                where: data.field
            });
        });

        form.on('submit(edit)', function (data) {
            $(".sell_stock").attr("disabled", false);
            $(".sell_stock").removeClass("layui-disabled");
            var i = $(this).attr("res");
            if (i === "0") {
                $(this).attr("res", "1");
                $(this).text("编辑");
                $(".sell_stock").attr("disabled", true);
                $(".sell_stock").addClass("layui-disabled");
                var field = {};
                var tempData = [];
                $('.sell_stock').each(function (index, element) {
                    //console.log($(element).val());
                    tempData.push({"id": $(element).attr('res'), 'price': $(element).val()});
                });
            } else {
                $(this).attr("res", "0");
                $(this).html("取消编辑");
            }
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
                    $("[name=type_id]").append('<option value="' + typeDataList[i].id + '">' + typeDataList[i].name + '</option>');
                }
                form.render();
            }
        });

        // 渲染list
        table.render({
            elem: '#list'
            , url: '/admin/order/get-audit-all-data'
            , cols: [[
                {field: 'id', width: 100, title: 'ID', sort: true}
                , {field: 'commodity_name', title: '商品名称', width: 240, height: 150}
                , {field: 'num', title: '下单数量', width: 150, height: 150}
                , {field: 'unit', title: '单位', width: 150}
                , {field: 'in_price', title: '最近一次进价', width: 150, toolbar: '#tool_in_price'}
                , {
                    field: 'price', title: '价格', templet: function (d) {
                        price = '';
                        $.each(d.priceInfo, function (i, f) {
                            typeName = (f.type_id === '1') ? 'B端客户：' : '门店：';
                            price += '<div class="layui-inline" style="margin-right:8px;">' + typeName + '(' + f.name + ')<input type="number" name="sell_stock" placeholder="" autocomplete="off" disabled="true" class="sell_stock layui-input layui-disabled" commodity_id="' + d.commodity_id + '" type_id="' + f.type_id + '" type_name="' + f.name + '" data_id = "' + f.id + '" price="' + f.price + '" lay-filter="sell_stock" style="height: 38px" value="' + f.price + '"></div>';
                        });
                        return price;
                    }
                }
            ]]
            , title: "列表"
            , done: function () {
                var edit = $('#edit');
                edit.attr("res", "1");
                edit.html("编辑");
            }
        });


        table.on('tool(list)', function (obj) {
            console.log(obj)
            switch (obj.event) {
                case 'history':
                    layer.open({
                        type: 2,
                        content: '/admin/purchase/purchase-price-history?id='+ obj.data.id,
                        area: ['500px', '430px'],
                        title: '成本历史',
                        maxmin: false,
                        btn: ['取消']
                    });
                    break;
            }
        });


        //日期范围
        laydate.render({
            elem: '#test-laydate-range-date'
            , value: '<?= date("Y-m-d", strtotime("+1 day")) ?>'
            , theme: 'molv'
            , done: function (value) {
                table.reload('list', {
                    where: {'delivery_date': value}
                });
            }

        });

        $('body').on('keypress blur', '.sell_stock', function (event) {
            var _this = $(this);
            var date = $('#test-laydate-range-date').val();
            var field = {
                'type_id': _this.attr('type_id')
                , 'type_name': _this.attr('type_name')
                , 'commodity_id': _this.attr('commodity_id')
                , 'price': _this.val()
                , 'data_id': _this.attr('data_id')
                , 'date': date
                , 'sync': $("#sync").prop("checked") ? 1 : 0
            };

            if ((event.keyCode === 13 || event.type === 'focusout') && _this.val() !== _this.attr('price')) {
                if (_this.val() === '0') {
                    layer.alert('价格不能设置为0', {'icon': 5});
                    return false;
                }
                admin.req({
                    type: "post"
                    , url: '/admin/order-all-audit/save-price'
                    , data: field
                    , done: function () {
                        _this.attr('price', _this.val());
                        layer.msg('保存成功');
                    }
                });
            }
        });

        $('.layui-btn.layuiadmin-btn-list').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });


        var data = [
            {'title': "确认时间", "desc": "选择时间段要核准的订单"},
            {'title': "核价方式", "desc": "选择核准价方式"},
            {'title': "智能参考", "desc": "核准价参考方式"},
            {'title': "修改价格", "desc": "编辑商品价,敲回车"}
        ];

        steps.make(data, '#steps', 3);

    });

    //显示表格大图
    function show_img(pic) {
        //页面层
        layer.open({
            title: '商品图片',
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['800px', '645px'], //宽高
            shadeClose: true, //开启遮罩关闭
            content: '<div style="text-align:center"><img src="' + pic + '?x-oss-process=image/resize,h_800" style="width: 800px;height:600px;"/></div>'
        });
    }


</script>
