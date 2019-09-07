<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <!--<button class="layui-btn layuiadmin-btn-list" data-type="batchdel">删除</button>-->
                <button class="layui-btn layuiadmin-btn-list" data-type="add">添加</button>
            </div>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="isShow">
                {{#  if(d.is_show == 0){ }}
                否
                {{#  } else { }}
                <span style="color: red">是</span>
                {{#  } }}
            </script>
            <script type="text/html" id="operation">
                {{#  if(d.pid == 0){ }}
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="subAdd" style="background-color: #FFC03B"><i
                            class="layui-icon layui-icon-add-1"></i>新增子类</a>
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
        index: 'lib/index', //主入口模块
        treetable: 'treetable-lay/treetable'
    }).use(['index', 'table', 'treetable'], function () {
        var table = layui.table
            , form = layui.form
            , admin = layui.admin
            ,treetable = layui.treetable;

        //监听搜索
        /*form.on('submit(search)', function (data) {
            var field = {};
            field.filterProperty=JSON.stringify(data.field);

            //执行重载
            table.reload('list', {
                where: field
            });
        });*/

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
                        , url: '/admin/cus-commodity-category/delete'
                        , dataType: "json"
                        , cache: false
                        , data: field
                        , done: function () {
                            renderTable();
                            layer.msg('已删除');
                        }
                    });
                });
            },
            add: function () {
                var index=layer.open({
                    type: 2
                    , title: '添加'
                    , content: '/admin/cus-commodity-category/create?pid=0&storeId=<?= $storeId ?>'
                    , maxmin: true
                    , area: ['650px', '400px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        //点击确认触发 iframe 内容中的按钮提交
                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                        submit.click();
                    }
                });
            }
        };

        $('.layui-btn.layuiadmin-btn-list').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        // 渲染表格
        var renderTable = function () {
            layer.load(2);
            treetable.render({
                treeColIndex: 2,          // treetable新增参数
                treeSpid: 0,             // treetable新增参数
                treeIdName: 'id',       // treetable新增参数
                treePidName: 'pid',     // treetable新增参数
                treeDefaultClose: true,   // treetable新增参数
                treeLinkage: true,        // treetable新增参数
                elem: '#list',
                url: '/admin/cus-commodity-category/index-data?storeId=<?= $storeId ?>',
                cols: [[
                    {type: 'checkbox', fixed: 'left', minWidth: 100}
                    , {field: 'id', width: 100, title: 'ID'}
                    , {field: 'name', title: '分类名称', minWidth: 100}
                    , {field: 'is_show', title: '小程序中是否显示', minWidth: 100, templet: '#isShow'}
                    , {field: 'sequence', title: '排序值', minWidth: 100}
                    , {title: '操作', minWidth: 250, align: 'center', fixed: 'right', toolbar: '#operation'}
                ]],
                done: function () {
                    layer.closeAll('loading');
                }
            });
        };

        renderTable();

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'subAdd') {
                layer.open({
                    type: 2
                    , title: '添加'
                    , content: '/admin/cus-commodity-category/create?pid=' + data.id + '&storeId=<?= $storeId ?>'
                    , maxmin: true
                    , area: ['500px', '280px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        //点击确认触发 iframe 内容中的按钮提交
                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-submit");
                        submit.click();
                    }
                });
            } else if (obj.event === 'del') {
                layer.confirm('确定删除此条记录？', function (index) {
                    admin.req({
                        type: "post"
                        , url: '/admin/cus-commodity-category/delete'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            ids: data.id
                        }
                        , done: function () {
                            renderTable();
                            layer.close(index);
                        }
                    });
                });
            } else if (obj.event === 'edit') {
                var area;
                if (data.pid == 0) {
                    area = ['650px', '400px']
                } else {
                    area = ['500px', '280px'];
                }

                layer.open({
                    type: 2
                    , title: '编辑'
                    , content: '/admin/cus-commodity-category/update?id=' + data.id
                    , maxmin: true
                    , area: area
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        var iframeWindow = window['layui-layer-iframe' + index]
                            , submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                        //监听提交
                        iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function (data1) {

                            var field = data1.field; //获取提交的字段
                            field["id"] = data.id;

                            //提交 Ajax 成功后，静态更新表格中的数据
                            admin.req({
                                type: "post"
                                , url: '/admin/cus-commodity-category/edit'
                                , dataType: "json"
                                , cache: false
                                , data: field
                                , done: function () {
                                    renderTable();
                                    layer.close(index); //关闭弹层
                                }
                            });
                        });
                        submit.trigger('click');
                    }
                });
            }
        });
    });
</script>
