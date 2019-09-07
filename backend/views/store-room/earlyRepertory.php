
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
                        <input type="text" name="searchText" placeholder="请输入商品编码\名称\助记码\别名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
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
    {{#  if(d.sell_stock == 0){ }}
    <input type="text" name="sell_stock" placeholder="" autocomplete="off" class="sell_stock layui-input layui-disabled" res="0" lay-filter="sell_stock" style="height: 28px" value="{{d.sell_stock}}">
    {{# }else{}}
       {{d.sell_stock}}
    {{#  } }}
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

        form.on('submit(edit)', function (data) {
            $(".sell_stock").removeClass("layui-disabled");
            var  i=$(this).attr("res");
            if(i==="0"){
                $(this).attr("res","1");
                $(this).html("编辑");
                $(".sell_stock").addClass("layui-disabled");
                layer.msg('保存成功');
            }else{
                $(this).attr("res","0");
                $(this).html("保存");
            }
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
                var index=layer.open({
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
                for (var i = 0; i< typeDataList.length; i++) {
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
                , {field: 'name', title: '商品名称', minWidth: 100}
                , {field: 'unit', title: '单位', minWidth: 100}
                , {field: 'sell_stock', title: '期初库存',templet:'#sell_stock'}
                , {field: 'price', title: '期初单价', minWidth: 100}
                , {field: 'total', title: '期初金额', minWidth: 100, templet: function (d) {
                    return d.sell_stock*d.price;
                }}
            ]]
            , title: "列表"
            ,done:function () {
                var edit = $('#edit');
                edit.attr("res","1");
                edit.html("编辑");
            }
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'view') {

            } else if (obj.event === 'editIsOnlineToUp') {
                admin.req({
                    type: "post"
                    , url: '/admin/commodity-list/update-is-online'
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
                    , url: '/admin/commodity-list/update-is-online'
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
                        , url: '/admin/commodity-list/delete'
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
                    , content: '/admin/commodity-list/update?id=' + data.id
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
