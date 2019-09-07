<script>
    /*'yyyy-MM-dd HH:mm:ss'格式的字符串转日期*/
    function stringToDate(str){
        var tempStrs = str.split(" ");
        var dateStrs = tempStrs[0].split("-");
        var year = parseInt(dateStrs[0], 10);
        var month = parseInt(dateStrs[1], 10) - 1;
        var day = parseInt(dateStrs[2], 10);
        var timeStrs = tempStrs[1].split(":");
        var hour = parseInt(timeStrs [0], 10);
        var minute = parseInt(timeStrs[1], 10);
        var second = parseInt(timeStrs[2], 10);
        var date = new Date(year, month, day, hour, minute, second);
        return date;
    }
</script>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select lay-filter="type" name="typeId">
                            <option value="">活动状态</option>
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
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-list" data-type="batchdel">删除</button>
                <button class="layui-btn layuiadmin-btn-list" data-type="add">添加</button>
            </div>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="activityTime">
                {{d.start_time}} 到 {{d.end_time}}
            </script>
            <script type="text/html" id="status">
                {{#
                    var startTime = Date.parse(d.start_time);
                    var endTime = Date.parse(d.end_time);
                    if(d.is_close == 1 || endTime < new Date()){
                }}
                已结束
                {{#
                    } else if(startTime <= new Date()) {
                }}
                已开始
                {{#  } else { }}
                未开始
                {{#  } }}
            </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="view" style="background-color: #77CF20"><i
                            class="layui-icon layui-icon-edit"></i>详情</a>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="close" style="background-color: #FFC03B"><i
                            class="layui-icon layui-icon-edit"></i>关闭</a>
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
            , limit: 10
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

                var field = {};
                var tempData = [];
                checkData.forEach(function (e) {
                    tempData.push(e.id);
                });
                field['ids'] = tempData;

                layer.confirm('确定删除吗？', function (index) {

                    //执行 Ajax 后重载
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-group-commodity-by-store-manager/delete'
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
                    , content: '/admin/cus-group-commodity-by-store-manager/create'
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

        // 渲染list
        table.render({
            elem: '#list'
            , url: '/admin/cus-group-commodity-by-store-manager/index-data'
            , cols: [[
                {type: 'checkbox', fixed: 'left', minWidth: 100}
                , {field: 'id', width: 100, title: 'ID', sort: true}
                , {field: 'name', title: '活动名称', minWidth: 100}
                , {title: '团购活动时间', minWidth: 100, templet: '#activityTime'}
                , {field: 'group_commoditys_name', title: '团购商品', minWidth: 100}
                , {field: 'buy_num', title: '下单数', minWidth: 100}
                , {title: '状态', minWidth: 100, templet: '#status'}
                , {field: 'close_name', title: '关闭人', minWidth: 100}
                , {field: 'close_time', title: '关闭时间', minWidth: 100, templet: '#channelType'}
                , {title: '操作', minWidth: 300, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , title: "列表"
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'del') {
                layer.confirm('确定删除此条记录？', function (index) {
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-group-commodity-by-store-manager/delete'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            ids: data.id
                        }
                        , done: function () {
                            obj.del();
                            layer.close(index);
                        }
                    });
                });
            } else if (obj.event === 'edit') {
                var index = layer.open({
                    type: 2
                    , title: '编辑'
                    , content: '/admin/cus-group-commodity-by-store-manager/update?id=' + data.id
                    , maxmin: true
                    , area: ['500px', '250px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        //点击确认触发 iframe 内容中的按钮提交
                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                        submit.click();
                    }
                });
                layer.full(index);
            } else if (obj.event === 'close') {
                admin.req({
                    type: "post"
                    , url: '/admin/cus-group-commodity-by-store-manager/close'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        id: data.id
                    }
                    , done: function (ref) {
                        if (ref.data.code == 0) {
                            layer.msg("此活动已结束")
                        } else {
                            table.reload('list');
                            layer.msg("结束活动成功")
                        }
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
