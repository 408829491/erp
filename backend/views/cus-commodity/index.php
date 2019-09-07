
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
                    <div class="layui-input-inline">
                        <select name="isOnline">
                            <option value="">全部状态</option>
                            <option value="0">下架</option>
                            <option value="1">上架</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="channelType">
                            <option value="">采购类型</option>
                            <option value="0">市场自采</option>
                            <option value="1">供应商直供</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 250px">
                        <input type="text" name="searchText" placeholder="请输入商品编码\名称\助记码\别名" autocomplete="off" class="layui-input">
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
            <script type="text/html" id="channelType">
                {{#  if(d.channel_type == 0){ }}
                市场自采
                {{#  } else { }}
                供应商供货
                {{#  } }}
            </script>
            <script type="text/html" id="status">
                {{#  if(d.is_online == 0){ }}
                未上架
                {{#  } else { }}
                已上架
                {{#  } }}
            </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="view" style="background-color: #77CF20"><i
                            class="layui-icon layui-icon-edit"></i>详情</a>
                {{#  if(d.is_online == 0){ }}
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="editIsOnlineToUp" style="background-color: #FFC03B"><i
                            class="layui-icon layui-icon-edit"></i>上架</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="editIsOnlineToDown" style="background-color: #FFC03B"><i
                            class="layui-icon layui-icon-edit"></i>下架</a>
                {{#  } }}
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
                        , url: '/admin/cus-commodity/delete'
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
                    , content: '/admin/cus-commodity/create?storeId=<?= $storeId ?>'
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
            , url: '/admin/cus-commodity-category/first-tier-data?storeId=<?= $storeId ?>'
            , dataType: "json"
            , cache: false
            , data: {}
            , done: function (res) {
                typeDataList = res.data;
                for (var i = 0; i< typeDataList.length; i++) {
                    $("[name=typeId]").append('<option value="' + typeDataList[i].id + '">' + typeDataList[i].name + '</option>');
                }
                form.render();
            }
        });

        // 渲染list
        table.render({
            elem: '#list'
            , url: '/admin/cus-commodity/index-data?storeId=<?= $storeId ?>'
            , cols: [[
                {type: 'checkbox', fixed: 'left', minWidth: 100}
                , {field: 'id', width: 100, title: 'ID', sort: true}
                , {field: 'pic', title: '商品图片', width: 100,templet: function(d){
                    return '<div onclick=show_img("'+d.pic+'") ><img src="'+d.pic+'?x-oss-process=image/resize,h_50" alt="" width="40px" height="30px"></a></div>';
                }}
                , {field: 'type_name', title: '商品分类', minWidth: 100}
                , {field: 'name', title: '商品名称', minWidth: 100}
                , {field: 'unit', title: '单位', minWidth: 100}
                , {field: 'price', title: '市场价', minWidth: 100}
                , {field: 'alias', title: '别名', minWidth: 100}
                , {field: 'channel_type', title: '采购类型', minWidth: 100, templet: '#channelType'}
                /*, {field: 'id', title: '采购员/供应商', minWidth: 100}*/
                , {field: 'is_online', title: '状态', minWidth: 100, templet: '#status'}
                , {title: '操作', minWidth: 300, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , title: "列表"
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'view') {

            } else if (obj.event === 'editIsOnlineToUp') {
                admin.req({
                    type: "post"
                    , url: '/admin/cus-commodity/update-is-online'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        id: data.id,
                        is_online: true
                    }
                    , done: function () {
                        obj.update({
                            is_online: true
                        }); //数据更新
                        table.reload('list');
                    }
                });
            } else if (obj.event === 'editIsOnlineToDown') {
                admin.req({
                    type: "post"
                    , url: '/admin/cus-commodity/update-is-online'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        id: data.id,
                        is_online: false
                    }
                    , done: function () {
                        obj.update({
                            is_online: false
                        }); //数据更新
                        table.reload('list');
                    }
                });
            } else if (obj.event === 'del') {
                layer.confirm('确定删除此条记录？', function (index) {
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-commodity/delete'
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
                    , content: '/admin/cus-commodity/update?id=' + data.id + '&storeId=<?= $storeId ?>'
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
