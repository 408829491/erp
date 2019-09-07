
<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-list" data-type="batchdel">删除</button>
                <button class="layui-btn layuiadmin-btn-list" data-type="add">添加</button>
            </div>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="img">
                <image name="slideshowImgs" style="width: 40px;height: 40px;" src="{{ d.img_url }}?x-oss-process=image/resize,h_50">
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
            parseData: function (res) {
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
                        , url: '/admin/cus-store-slideshow/delete'
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
                    , content: '/admin/cus-store-slideshow/create?storeId=<?= $storeId ?>'
                    , maxmin: true
                    , area: ['800px', '300px']
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
            , url: '/admin/cus-store-slideshow/index-data?storeId=<?= $storeId ?>'
            , cols: [[
                {type: 'checkbox', fixed: 'left', minWidth: 100}
                , {field: 'id', width: 100, title: 'ID', sort: true}
                , {field: 'img_url', title: '轮播图片', width: 100, templet:"#img"}
                , {field: 'info', title: '简介'}
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
                        , url: '/admin/cus-store-slideshow/delete'
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
                    , content: '/admin/cus-store-slideshow/update?id=' + data.id
                    , maxmin: true
                    , area: ['800px', '300px']
                    , btn: ['确定', '取消']
                    , yes: function (index, layero) {
                        // 提交
                        var iframeWindow = window['layui-layer-iframe' + index];

                        var submit = layero.find('iframe').contents().find("#layuiadmin-app-form-edit");

                        //监听提交
                        iframeWindow.layui.form.on('submit(layuiadmin-app-form-edit)', function (data1) {

                            var field = data1.field; //获取提交的字段
                            field["id"] = data.id;

                            //提交 Ajax 成功后，静态更新表格中的数据
                            admin.req({
                                type: "post"
                                , url: layui.setter.reqUrlBase + '/admin/cus-store-slideshow/edit'
                                , dataType: "json"
                                , cache: false
                                , data: field
                                , done: function () {
                                    obj.update({
                                        img_url: field.img_url
                                        , info: field.info
                                    }); //数据更新

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

        $("body").on('click', '[name = slideshowImgs]', function () {
            console.log(this.src);

            layui.layer.photos({
                photos: {
                    "title": "查看轮播图"
                    , "data": [{
                        "src": this.src.substr(0, this.src.indexOf('?')) + '?x-oss-process=image/resize,h_800'
                    }]
                }
                , shade: 0.01
                , closeBtn: 1
                , anim: 5
            });
        })
    });

</script>
