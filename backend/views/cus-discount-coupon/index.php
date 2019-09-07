
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="type">
                            <option value="">类型</option>
                            <option value="0">普通优惠券</option>
                            <option value="1">积分换购券</option>
                            <option value="2">新人券</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 250px">
                        <input type="text" name="searchText" placeholder="请输入名称" autocomplete="off" class="layui-input">
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
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-list" data-type="batchdel">删除</button>
                <button class="layui-btn layuiadmin-btn-list" data-type="add">添加</button>
            </div>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="img">
                <image style="width: 40px;height: 40px;" src="{{ d.pic.split(':;')[0] }}?x-oss-process=image/resize,h_50">
            </script>
            <script type="text/html" id="type">
                {{#  if(d.type == 0){ }}
                普通优惠券
                {{#  } else if(d.type == 1) { }}
                积分换购券
                {{#  } else { }}
                新人券
                {{#  } }}
            </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="view" style="background-color: #77CF20"><i
                            class="layui-icon layui-icon-edit"></i>详情</a>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                        class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                        class="layui-icon layui-icon-delete"></i>删除</a>
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
            var field = {};
            field.filterProperty=JSON.stringify(data.field);

            //执行重载
            table.reload('list', {
                where: field
            });
        });

        var $ = layui.$, active = {
            batchdel: function () {
                var checkStatus = table.checkStatus('list')
                    , checkData = checkStatus.data; //得到选中的数据

                if (checkData.length === 0) {
                    return layer.msg('请选择数据');
                }

                var field={};
                var fieldData="";
                for(var i=0;i<checkData.length;i++){
                    fieldData=fieldData+checkData[i].id+",";
                }
                fieldData=fieldData.substring(0,fieldData.length-1);
                field['ids'] = fieldData;
                layer.confirm('确定删除吗？', function (index) {

                    //执行 Ajax 后重载
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-discount-coupon/delete'
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
                var index=layer.open({
                    type: 2
                    , title: '添加'
                    , content: '/admin/cus-discount-coupon/create'
                    , maxmin: true
                    , area: ['670px', '600px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        //点击确认触发 iframe 内容中的按钮提交
                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                        submit.click();
                    }
                });
            }
        };

        // 渲染list
        table.render({
            elem: '#list'
            , url: '/admin/cus-discount-coupon/index-data'
            , cols: [[
                {type: 'checkbox', fixed: 'left', minWidth: 100}
                , {field: 'id', width: 100, title: 'ID', sort: true}
                , {field: 'type', title: '类型', width: 100, templet: '#type'}
                , {field: 'store_name', title: '所属门店', width: 100}
                , {field: 'name', title: '名称', minWidth: 100}
                , {field: 'start_date', title: '开始时间', minWidth: 100}
                , {field: 'end_date', title: '结束时间', minWidth: 100}
                , {field: 'condition', title: '满', minWidth: 100}
                , {field: 'distance', title: '减', minWidth: 100, templet: '#channelType'}
                , {field: 'integral', title: '换购积分', minWidth: 100, templet: '#status'}
                , {title: '操作', minWidth: 300, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , title: "列表"
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'view') {

            } else if (obj.event === 'del') {
                layer.confirm('确定删除此条记录？', function (index) {
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-discount-coupon/delete'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            ids: data.id
                        }
                        , done: function () {
                            table.reload('list');
                            layer.close(index);
                        }
                    });
                });
            } else if (obj.event === 'edit') {
                var index = layer.open({
                    type: 2
                    , title: '编辑'
                    , content: '/admin/cus-discount-coupon/update?id=' + data.id
                    , maxmin: true
                    , area: ['670px', '600px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        var iframeWindow = window['layui-layer-iframe' + index];

                        // 触发提交
                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                        //监听提交
                        iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function (data1) {

                            var field = data1.field; // 获取提交的字段
                            field['id'] = data.id;

                            console.log(field.activityDate);

                            var tempTimes= field.activityDate.split(" 到 ");
                            field.start_date = tempTimes[0];
                            field.end_date = tempTimes[1];

                            // 店铺分隔存储
                            var tempDate = field.store_id.split(':;');
                            field.store_id = tempDate[0];
                            field.store_name = tempDate[1];

                            // 提交 Ajax 成功后，关闭当前弹层并重载表格
                            admin.req({
                                type: "post"
                                , url: '/admin/cus-discount-coupon/edit'
                                , dataType: "json"
                                , cache: false
                                , data: field
                                , done: function () {

                                    obj.update({
                                        store_name: field.store_name
                                        , type: field.type
                                        , name: field.name
                                        , start_date: field.start_date
                                        , end_date: field.end_date
                                        , condition: field.condition
                                        , distance: field.distance
                                        , integral: field.integral
                                    }); //数据更新
                                    layer.close(index); // 再执行关闭
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

    //显示表格大图
    function show_img(pic) {
        //页面层
        layer.open({
            title:'商品图片',
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['800px', '645px'], //宽高
            shadeClose: true, //开启遮罩关闭
            content: '<div style="text-align:center"><img src="'+pic+'?x-oss-process=image/resize,h_800" style="width: 800px;height:600px;"/></div>'
        });
    }

</script>
