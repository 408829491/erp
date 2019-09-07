<style>
    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
<!--            <ul class="layui-tab-title">-->
<!--                <li lay-data="1" class="layui-this">智能定价</li>-->
<!--                <li>历史定价</li>-->
<!--            </ul>-->
            <div class="layui-tab-content" style="padding: 0">
                <div class="layui-tab-item layui-show">
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
                                    <input type="text" name="searchText" placeholder="请输入商品编码\名称\助记码\别名"
                                           autocomplete="off"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                </button>
                            </div>

                            <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">

                                <button class="layui-btn  layui-btn-normal" data-type="allSort"
                                        style="background-color: #77CF20" lay-submit lay-filter="sync" id="sync">同步商品价格
                                </button>
                                <button class="layui-btn  layui-btn-normal" data-type="allSort"
                                        style="background-color: #77CF20;display:none;" lay-submit lay-filter="multiple_sync"
                                        id="multiple_sync">批量设置定价格式
                                </button>
                            </div>

                        </div>
                    </div>

                    <div class="layui-card-body">
                        <table id="list" lay-filter="list"></table>
                    </div>
                </div>
                <div class="layui-tab-item">
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
                                <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="layui-card-body">
                        <table id="list2" lay-filter="list2"></table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<script type="text/html" id="order-list-barDemo">
    <div style="display: flex;align-items: center"><a class="layui-btn layui-btn-xs" lay-event="formula"
                                                      style="background-color: #77CF20">设置公式</a>
        {{# if(d.is_setting_formula == 1){ }}
        <div style="text-align:center;margin:auto;color:#77CF20"><i class="layui-icon layui-icon-ok-circle"></i></div>
        {{# }}}
    </div>
</script>
<script type="text/html" id="is_order">
    <div style="text-align:center;margin:auto;color:#77CF20"><i class="layui-icon layui-icon-ok-circle"></i></div>
</script>
<script type="text/html" id="in_price">
    <div style="display: flex;align-items: center;">
        <span class="layui-inline">{{d.in_price}}</span>
        <i class="layui-icon layui-icon-edit" name="edit"
           style="font-size: 20px;color:#77CF20;margin-left:20px;display: none;cursor:pointer"></i>
    </div>
    <div style="display: none;align-items: center;">
        <span class="layui-inline"><input class="layui-input" style="height: 28px;" name="num"
                                          value="{{d.in_price}}"></span>
        <i class="layui-icon layui-icon-ok" name="edit" data-id="{{d.pid}}"
           style="font-size: 20px;color:#77CF20;margin-left:20px;cursor:pointer"></i>
    </div>
</script>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table'], function () {
        var table = layui.table
            , form = layui.form
            , admin = layui.admin
            , $ = layui.$;

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
            field = data.field;
            //执行重载
            table.reload('list', {
                where: field
            });
        });



        //同步商品价格
        form.on('submit(sync)', function () {
            layer.confirm('是否提交定价?<br>本次定价将同步商品价格<br>' +
                '1.没有进价的商品，销售价格将不会更改<br>' +
                '2.没有设置公式的商品，销售价格将不会更改 <br>', {icon: 3, title: '同步商品价格'}, function (index) {
                admin.req({
                    type: "get"
                    , url: '/admin/set-price/sync-setting-price'
                    , cache: false
                    , done: function (res) {
                        if (res.code === 200) {
                            layer.msg('同步成功');
                            table.reload('list');
                        } else {
                            layer.msg('同步失败');
                        }
                    }
                });
            });
        });


        //同步商品价格
        form.on('submit(multiple_sync)', function () {
            layer.open({
                type: 2,
                content: '/admin/commodity-list/batch-set-price',
                area: ['500px', '310px'],
                title: '批量设置定价公式',
                maxmin: false,
                btn: ['保存设置','取消']
            });
        });

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
                {type: 'checkbox', minWidth: 100}
                , {field: 'name', title: '商品名称', minWidth: 100}
                , {field: 'id', width: 100, title: '商品编码'}
                , {field: 'unit', title: '单位', minWidth: 100}
                , {field: 'is_order', title: '是否已下单', templet: '#is_order'}
                , {field: 'in_price', title: '最近一次进货价', minWidth: 100, templet: '#in_price'}
                , {
                    field: 'price', title: '客户类型价', minWidth: 100, templet: function (d) {
                        price = '';
                        $.each(d.priceInfo, function (i, f) {
                            typeName = (f.type_id === '1') ? 'B端客户：' : '门店：';
                            price += '<div>' + typeName + f.price + '</div>';
                        });
                        return price;
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#order-list-barDemo', width: 150}
            ]]
            , title: "列表"
            , done: function () {
                $('table tr').on('mouseover', function () {
                    $(this).find('.layui-icon-edit').css('display', 'block');
                }).on('mouseout', function () {
                    $(this).find('.layui-icon-edit').css('display', 'none');
                });

            }
        });


        table.on('checkbox(list)', function () {
            if (layui.table.checkStatus('list').data.length > 0) {
                $('#multiple_sync').show();
                $('#sync').hide();
            } else {
                $('#sync').show();
                $('#multiple_sync').hide();
            }
        });

        //订单历史
        table.render({
            elem: '#list2'
            , url: '/admin/commodity-list/setting-price-history'
            , cols: [[
                {field: 'id', title: 'ID', minWidth: 100}
                , {field: 'name', title: '单号', minWidth: 100}
                , {field: 'id', width: 100, title: '定价时间', sort: true}
                , {field: 'unit', title: '发货日期', minWidth: 100}
                , {field: 'is_order', title: '制单人'}
                , {field: 'in_price', title: '同步类型', minWidth: 100}
                , {field: 'in_price', title: '商品总数', minWidth: 100}
            ]]
            , title: "列表"
        });

        //修改商品进货价
        $('body').on('click', '[name=edit]', function (e) {
            var obj = $(this).parent();
            obj.css('display', 'none');
            obj.siblings().css('display', 'flex');
            if ($(this).hasClass('layui-icon-ok')) {
                if (obj.find('[name = num]').val() !== obj.siblings().find('span').text()) {
                    var field = {'id': $(this).attr('data-id'), 'price': obj.find('[name = num]').val()};
                    obj.siblings().find('span').html(obj.find('[name = num]').val());
                    admin.req({
                        type: "get"
                        , url: '/admin/commodity-list/change-in-price'
                        , cache: false
                        , data: field
                        , done: function (res) {
                            if (res.code === 200) {
                                layer.msg('保存成功');
                            } else {
                                layer.msg('保存失败');
                            }
                        }
                    });
                }
            }
        });

        //回车保存商品进货价
        $('body').on('keypress', '[name=num]', function (event) {
            if (event.keyCode === 13) {
                $(this).parent().siblings('[name=edit]').trigger("click");
            }
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'formula') {
                admin.popupRight({
                    id: 'LAY-popup-right' //定义唯一ID，防止重复弹出
                    ,
                    type: 2
                    ,
                    title: data.name
                    ,
                    area: ['500px']
                    ,
                    content: '/admin/commodity-list/set-price?id=' + data.id + '&in_price=' + data.in_price + '&unit=' + data.unit
                });
                console.log(obj);
            }
        });

    });


</script>
