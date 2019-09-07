<style>
    .layui-form-checked[lay-skin=primary] i{
        border-color:#77CF20 !important;
        background-color:#77CF20 !important;
        font-weight: bold;
    }
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs">
                <ul class="layui-tab-title">
                    <li lay-data="1" class="layui-this">分拣打印</li>
                    <li>商品分拣进度</li>
                    <li>客户分拣进度</li>
                    <div class="layui-inline" style="position:absolute;right: 10px;padding: 6px; 6px 6px 0;">
                        <div class="layui-form layui-inline" lay-filter="layui-form">
                            <input type="checkbox" id="startPrint" lay-skin="primary" style="background-color: red;" title="分拣打印" checked>
                        </div>
                        <button class="layui-btn layui-btn-sm layui-btn-normal" data-type="fullScreenSort" style="background-color: #77CF20;margin-right: 10px;">全屏分拣</button>
                        <button class="layui-btn layui-btn-sm layui-btn-normal" data-type="allSort" style="background-color: #77CF20">一键分拣</button>
                        <button class="layui-btn layui-btn-sm layui-btn-normal tpl_print_view_summary" data-id='' data-type="printAll" style="background-color: #77CF20">打印拣货汇总</button>
                        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide" data-type="exports" style="background-color: #77CF20">导出汇总表</button>
                        <button id="batch-print" class="layui-btn layui-btn-sm layui-btn-normal tpl_print_pick layui-hide" data-id="0" style="background-color: #77CF20">批量打印</button>
                    </div>
                </ul>

                <div class="layui-tab-content" style="padding: 0">
                    <div class="layui-tab-item layui-show">

                        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" readonly name="delivery_date" class="layui-input" id="laydate-create-date" placeholder="发货日期">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="storeId">
                                            <option value="">默认仓库</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="line_id">
                                            <option value="">全部线路</option>
                                            <?php foreach ($lineData as $item) : ?>
                                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="agent_id">
                                            <option value="">全部供应商</option>
                                            <?php foreach ($providerData as $item) : ?>
                                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="type_first_tier_id">
                                            <option value="">商品分类</option>
                                            <?php foreach ($commodityCategory as $item) : ?>
                                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!--<div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="is_pay">
                                            <option value="">是否标品</option>
                                            <option value="1">是</option>
                                            <option value="0">否</option>
                                        </select>
                                    </div>
                                </div>-->
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="is_sorted">
                                            <option value="">分拣状态</option>
                                            <option value="1">已分拣</option>
                                            <option value="0">未分拣</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" name="searchText" placeholder="输入客户名称" autocomplete="off" class="layui-input" style="width: 200px;">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                                    </button>
                                    <div class="layui-form-item layui-hide">
                                        <input type="button" lay-submit lay-filter="confirmOneTouch" id="confirmOneTouch" value="确认一键分拣">
                                    </div>
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
                            </div>
                        </div>
                    </div>

                    <div class="layui-tab-item">
                        <iframe src="/admin/sort/sort-rate" name="sortRateIframe" width="100%" height="800px" style="border: none"></iframe>
                    </div>
                    <div class="layui-tab-item">
                        <iframe src="/admin/sort/sort-rate-by-user" name="sortRateByUserIframe" width="100%" height="800px" style="border: none"></iframe>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>
    <style>
        .num-input{
            height:28px;
            text-align: center;
        }
    </style>
    <script type="text/html" id="order-list-barDemo">
        <a class="layui-btn layui-btn-normal layui-btn-xs tpl_print_pick" lay-event="print" data-id="{{d.id}}" style="background-color: #77CF20"><i class="layui-icon layui-icon-print"></i>打印</a>
        {{#  if(d.is_sorted == 1 && d.status == 1){ }}
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="reSort" style="background-color: #00b0e8"><i class="layui-icon layui-icon-rest"></i>重置</a>
        {{#  } }}
    </script>
    <script type="text/html" id="isSorted">
        <span name="isSorted">
        {{#  if(d.is_sorted == 1){ }}
        <span style="color:green">已分拣</span>
        {{#  } else { }}
        <span style="color:red">未分拣</span>
        {{#  } }}
        </span>
    </script>
    <script type="text/html" id="unit">
        {{#  if(d.is_basics_unit == 1){ }}
            {{ d.unit }}
        {{#  } else { }}
            {{ d.unit }}（{{ d.base_self_ratio }}{{ d.base_unit }}）
        {{#  } }}
     </script>
    <script type="text/html" id="test-table-countTpl">
        {{#  if(d.is_sorted == 1){ }}
        <input type="text" disabled name="order_count" placeholder="" data-id="{{d.id}}" autocomplete="off" class="layui-input num-input" id="order_count" lay-filter="order_count" style="text-align: center;font-weight: normal;background-color: #EEEEEE" value="{{ d.actual_num }}">
        {{#  } else { }}
        <input type="text" name="order_count" placeholder="" data-id="{{d.id}}" autocomplete="off" class="layui-input num-input" id="order_count" lay-filter="order_count" style="text-align: center;font-weight: normal;" value="">
        {{#  } }}
    </script>

    <script src="/admin/plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/pFuncs.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/printCommon.js"></script>
    <script src="/admin/plugins/cdsPrint/common/js/print/publicPrinter.js"></script>
    <script src="/admin/plugins/cdsPrint/js/print/summary.js"></script>
    <script src="/admin/plugins/cdsPrint/js/print/pick.js"></script>

    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','element','form'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,form = layui.form
                ,admin = layui.admin
                ,$ = layui.$;

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
                , limit: 100
                , limits: [100, 200, 300]
                , text: {
                    none: '暂无相关数据'
                }
            });

            var tomorrow = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);

            // 格式化日期
            function formatDate(date) {
                var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
                var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                return date.getFullYear() + '-' + month + '-' + day;
            }

            table.render({
                elem: '#order-list'
                ,url: '/admin/sort/get-index-data'
                ,toolbar: '#order-list-toolbarDemo'
                ,title: '用户数据表'
                ,where: {filterProperty: JSON.stringify({delivery_date: formatDate(tomorrow)})}
                ,cols: [[
                    {field:'commodity_name', title:'商品名称',sort: true,width:200}
                    ,{field:'nick_name', title:'客户名称', width:200}
                    ,{field:'num', title:'订购数量',width:120}
                    ,{field:'actual_num', title:'实际数量', templet: '#test-table-countTpl',width:240}
                    ,{field:'unit', title:'单位', templet: "#unit"}
                    ,{field:'remark', title:'备注'}
                    ,{field:'is_sorted', title:'分拣状态',templet:"#isSorted"}
                    ,{field:'sorter', title:'分拣员'}
                    ,{fixed: 'right', title:'操作', toolbar: '#order-list-barDemo',width:130}
                ]]
                ,page: true
            });

            //日期范围
            laydate.render({
                elem: '#laydate-create-date'
                , value:new Date(tomorrow)
                , done: function () {
                    $('[lay-filter = search]').trigger('click');
                }
            });

            //清除查询条件
            form.on('submit(search-clear)', function(obj){
                form.val("layui-form", {
                    'delivery_date': tomorrow,
                    'line_id': '',
                    'provider_id': '',
                    'agent_id': '',
                    'is_sorted':'',
                    'type_first_tier_id':'',
                    'nickName': ''
                })
            });

            //监听搜索
            form.on('submit(confirmOneTouch)', function(data){
                admin.req({
                    type: "post"
                    , url: '/admin/sort/check-one-touch-print'
                    , dataType: "json"
                    , data:data.field
                    , cache: false
                    , done: function (e) {
                        if(e.data === 0){
                            layer.alert(e.msg, {
                                icon: 5,
                                title: "提示信息"
                            });
                            return false;
                        }else{
                            layer.confirm(e.msg, function (index) {
                                admin.req({
                                    type: "post"
                                    , url: '/admin/sort/sort-all'
                                    , dataType: "json"
                                    , cache: false
                                    , data: {
                                        delivery_date: $('[name = delivery_date]').val()
                                    }
                                    , done: function () {
                                        $('#batch-print').attr('data-id',e.data.ids);
                                        pick_print.startPrint({clickObj:$('#batch-print')});
                                        table.reload('order-list');
                                        layer.msg('一键分拣成功');
                                    }
                                });
                            });
                        }
                    }
                });
            });


            //监听搜索
            form.on('submit(search)', function(data){
                var field = {};
                field.filterProperty=JSON.stringify(data.field);

                //执行重载
                table.reload('order-list', {
                    where: field
                });
            });

            // 监听实际数量获取焦点
            $('body').on('focus', '[name=order_count]', function () {
                var tr = $(this).parent().parent().parent();
                tr.css({'color': 'red', 'font-weight': 'bold'});
            });

            // 监听实际数量失去焦点
            $('body').on('blur', '[name=order_count]', function () {
                var tr = $(this).parent().parent().parent();
                tr.css('color', 'red');
                tr.css({'color': '#666', 'font-weight': 'normal'});
            });

            // 监听实际数量输入回车键
            $('body').on('keyup', '[name=order_count]', function (e) {
                var inputObj = $(this);
                var id = inputObj.attr('data-id');
                var amount = inputObj.val();
                var tr = inputObj.parent().parent().parent();

                if (e.keyCode === 13) {
                    // 修改成功下一个输入框获取焦点
                    admin.req({
                        type: "get"
                        , url: '/admin/sort/change-status'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            id: id,
                            amount: amount
                        }
                        , done: function () {
                            layer.msg('成功分拣');
                            // 更新
                            tr.find('[name = isSorted]').html('<span style="color:green">已分拣</span>');

                            // 修改输入框
                            inputObj.css('background-color', '#eee');
                            inputObj.attr('disabled', 'disabled');

                            // 添加重置按钮
                            tr.find('[lay-event = print]').eq(0).after('<a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="reSort" style="background-color: #00b0e8"><i class="layui-icon layui-icon-rest"></i>重置</a>');
                            // 打印分拣标签
                            if($("#startPrint").prop("checked")){
                                pick_print.startPrint({clickObj:tr.find('[lay-event = print]').eq(0)});
                            }

                            // 下一个获取焦点
                            tr.next().find('[name=order_count]').focus();
                        }
                    });
                }
            });

            //监听工具条
            table.on('tool(order-list)', function (obj) {
                var data = obj.data;
                if (obj.event === 'reSort') {
                    // 分拣重置

                    layer.confirm('是否重置分拣', {icon: 3, title:'提示'}, function (index) {
                        admin.req({
                            type: "get"
                            , url: '/admin/sort/re-status'
                            , dataType: "json"
                            , cache: false
                            , data: {
                                id: data.id,
                                amount: 0
                            }
                            , done: function () {
                                layer.msg('重置成功');
                                var inputObj = obj.tr.find('[name = order_count]');
                                obj.tr.find('[name = isSorted]').html('<span style="color:red">未分拣</span>');
                                inputObj.removeAttr('disabled');
                                inputObj.css('background-color','#ffffff');
                                inputObj.val('');
                                obj.tr.find('[lay-event = reSort]').remove();
                                inputObj.focus();
                            }
                        });
                    });
                }
            });

            var active = {
                printAll: function () {
                    var data = layui.table.cache['order-list'];
                    var ids = [];
                    layui.each(data,function(k,v){
                        if(ids.indexOf(v.commodity_id)===-1){
                            ids.push(v.commodity_id);
                        }
                    });
                    ids = ids.toString(ids);
                    if(ids === ''){
                        layer.alert('暂无数据', {
                            icon: 5,
                            title: "提示"
                        });
                    }
                    $('.tpl_print_view_summary').attr("data-id",ids);
                },
                allSort:function(){
                    $('[lay-filter = confirmOneTouch]').trigger('click');

                },
                fullScreenSort:function() {
                    parent.layui.layer.open({
                        type: 2,
                        content: '/admin/sort/full-screen-sort-type-select',
                        title: false,
                        closeBtn: 0,
                        shadeClose: true,
                        skin: 'yourClass',
                        area: ["100%", "100%"],
                    });
                    parent.layui.admin.fullScreen();
                },
                exports:function(){
                    layer.msg('export');
                }
            };

            element.on('tab', function (data) {
                if (data.index == 0) {
                    table.reload('order-list');
                } else if (data.index == 1) {
                    var iframeWindow = window['sortRateIframe'];
                    iframeWindow.layui.table.reload('list');
                } else {
                    var iframeWindow = window['sortRateByUserIframe'];
                    iframeWindow.layui.table.reload('list');
                }
            });

            $('.layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] && active[type].call(this);
            });

        });

    </script>