<style>
    .unit_list {
        display: flex;
        flex-direction: column;
        padding: 10px;
    }

    .unit_list_div {
        display: flex;
        align-items: center;
        height: 24px;
    }

    .unit_list_div_div {
        border-radius: 50%;
        width: 6px;
        height: 6px;
        background: #1fb922;
    }

    .unit_list_div_div2 {
        font-weight: 400;
        font-size: 20px;
        margin-left: 20px;
    }

    .unit_list_div3 {
        height: 1px;
        background-color: #F2F2F2;
        margin-top: 10px;
    }

    .unit_list_div2 {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .unit_list_div2_out {
        height: 32px;
        margin-bottom: 10px;
        margin-right: 15px;
        border: 1px solid #ccc;
    }

    .unit_list_div2_out_div {
        margin: 0 10px;
        min-width: 60px;
        height: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #bbb;
    }

    .unit_list_div2_out_div2 {
        min-width: 60px;
        height: 32px;
        display: none;
        justify-content: center;
        align-items: center;
        color: #bbb;
        position: relative;
        margin-top: -32px;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .unit_list_div2_div2 {
        min-width: 80px;
        height: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
        line-height: 32px;
        background-color: rgb(25, 190, 107);
        color: rgb(255, 255, 255);
        margin-bottom: 10px;
        margin-right: 15px;
    }

    .unit_list_input_div {
        padding: 20px;
        width: 300px;
        display: flex;
        align-items: center;
    }

    .unit_list_input_div_input {
        width: 200px;
        margin-left: 20px;
    }
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="unit_list">
            <div class="unit_list_div">
                <div class="unit_list_div_div"></div>
                <div class="unit_list_div_div2">计量单位</div>
            </div>
            <div class="unit_list_div3"></div>
            <div class="unit_list_div2">
                <?php foreach ($unitList as $item) : ?>
                    <div class="unit_list_div2_out">
                        <div class="unit_list_div2_out_div">
                            <?= $item ?>
                        </div>
                        <div class="unit_list_div2_out_div2">
                            <i class="layui-icon layui-icon-edit" name="unitUpdate" style="font-size: 30px; color: rgb(25, 190, 107);"></i>
                            <i class="layui-icon layui-icon-delete" name="unitDelete" style="font-size: 30px; color: rgb(25, 190, 107);margin-left: 5px;"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button class="layui-btn layui-btn-primary unit_list_div2_div2" id="unitAdd">
                    新增
                </button>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="unit_list">
            <div class="unit_list_div">
                <div class="unit_list_div_div"></div>
                <div class="unit_list_div_div2">商品标签</div>
            </div>
            <div class="unit_list_div3"></div>
            <div class="unit_list_div2">
                <?php foreach ($tagList as $item) : ?>
                    <div class="unit_list_div2_out">
                        <div class="unit_list_div2_out_div">
                            <?= $item ?>
                        </div>
                        <div class="unit_list_div2_out_div2">
                            <i class="layui-icon layui-icon-edit" name="tagUpdate" style="font-size: 30px; color: rgb(25, 190, 107);"></i>
                            <i class="layui-icon layui-icon-delete" name="tagDelete" style="font-size: 30px; color: rgb(25, 190, 107);margin-left: 5px;"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button class="layui-btn layui-btn-primary unit_list_div2_div2" id="tagAdd">
                    新增
                </button>
            </div>
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
            , limit: 10
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        $('#unitAdd').on('click', function (res) {
            layer.open({
                type: 1
                , title: '添加'
                , content: '<div class="unit_list_input_div"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称<input id="unitName" type="text" class="layui-input unit_list_input_div_input"></div>'
                , maxmin: true
                , area: ['400px', '200px']
                , btn: ['确定', '取消']
                , yes: function (index, layero) {
                    var unitName = $("#unitName").val();
                    if (unitName == '') {
                        layer.msg('请输入名称');
                        return ;
                    }
                    admin.req({
                        type: "post"
                        , url: '/admin/config/add-unit'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            unitName: unitName
                        }
                        , done: function () {
                            $('#unitAdd').before('<div class="unit_list_div2_out"><div class="unit_list_div2_out_div">' + unitName + '</div><div class="unit_list_div2_out_div2"><i class="layui-icon layui-icon-edit" name="unitUpdate" style="font-size: 30px; color: rgb(25, 190, 107);"></i><i class="layui-icon layui-icon-delete" name="unitDelete" style="font-size: 30px; color: rgb(25, 190, 107);margin-left: 5px;"></i></div></div>');
                            layer.close(index);
                        }
                    });
                }
            });
        });

        $('#tagAdd').on('click', function (res) {
            layer.open({
                type: 1
                , title: '添加'
                , content: '<div class="unit_list_input_div"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称<input id="tagName" type="text" class="layui-input unit_list_input_div_input"></div>'
                , maxmin: true
                , area: ['400px', '200px']
                , btn: ['确定', '取消']
                , yes: function (index, layero) {
                    var tagName = $("#tagName").val();
                    if (tagName == '') {
                        layer.msg('请输入名称');
                        return ;
                    }
                    admin.req({
                        type: "post"
                        , url: '/admin/config/add-tag'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            tagName: tagName
                        }
                        , done: function () {
                            $('#tagAdd').before('<div class="unit_list_div2_out"><div class="unit_list_div2_out_div">' + tagName + '</div><div class="unit_list_div2_out_div2"><i class="layui-icon layui-icon-edit" name="tagUpdate" style="font-size: 30px; color: rgb(25, 190, 107);"></i><i class="layui-icon layui-icon-delete" name="tagDelete" style="font-size: 30px; color: rgb(25, 190, 107);margin-left: 5px;"></i></div></div>');
                            layer.close(index);
                        }
                    });
                }
            });
        });
        
        $('.unit_list_div2').on('mouseover mouseout', '.unit_list_div2_out', function (event) {
            if (event.type == 'mouseover') {
                // 鼠标悬停
                $(this).find('.unit_list_div2_out_div2').css('display', 'flex');
            } else {
                // 鼠标离开
                $(this).find('.unit_list_div2_out_div2').css('display', 'none');
            }
        });

        $('.unit_list_div2').on('click', '[name = unitUpdate]', function () {
            var unitNode = $(this).parent().parent().find('.unit_list_div2_out_div');
            var unitName = $.trim(unitNode.html());
            layer.open({
                type: 1
                , title: '修改'
                , content: '<div class="unit_list_input_div"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称<input id="unitName" type="text" class="layui-input unit_list_input_div_input" value="' + unitName + '"></div>'
                , maxmin: true
                , area: ['400px', '200px']
                , btn: ['确定', '取消']
                , yes: function (index, layero) {
                    var unitName2 = $("#unitName").val();
                    if (unitName2 == '') {
                        layer.msg('请输入名称');
                        return ;
                    }
                    admin.req({
                        type: "post"
                        , url: '/admin/config/update-unit'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            oldUnitName: unitName,
                            unitName: unitName2
                        }
                        , done: function () {
                            unitNode.html(unitName2);
                            layer.close(index);
                        }
                    });
                }
            });
        });

        $('.unit_list_div2').on('click', '[name = unitDelete]', function () {
            var unitOutNode = $(this).parent().parent();
            var unitNode = unitOutNode.find('.unit_list_div2_out_div');
            var unitName = $.trim(unitNode.html());
            layer.confirm('确定删除该单位', {icon: 3, title:'提示'}, function (index) {

                admin.req({
                    type: "post"
                    , url: '/admin/config/delete-unit'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        unitName: unitName
                    }
                    , done: function () {
                        unitOutNode.remove();
                        layer.close(index);
                    }
                });
            })
        });

        $('.unit_list_div2').on('click', '[name = tagUpdate]', function () {
            var tagNode = $(this).parent().parent().find('.unit_list_div2_out_div');
            var tagName = $.trim(tagNode.html());
            layer.open({
                type: 1
                , title: '修改'
                , content: '<div class="unit_list_input_div"><span style="color: #ff6d6d;margin-right: 5px;">*</span>名称<input id="tagName" type="text" class="layui-input unit_list_input_div_input" value="' + tagName + '"></div>'
                , maxmin: true
                , area: ['400px', '200px']
                , btn: ['确定', '取消']
                , yes: function (index, layero) {
                    var tagName2 = $("#tagName").val();
                    if (tagName2 == '') {
                        layer.msg('请输入名称');
                        return ;
                    }
                    admin.req({
                        type: "post"
                        , url: '/admin/config/update-tag'
                        , dataType: "json"
                        , cache: false
                        , data: {
                            oldTagName: tagName,
                            tagName: tagName2
                        }
                        , done: function () {
                            tagNode.html(tagName2);
                            layer.close(index);
                        }
                    });
                }
            });
        });

        $('.unit_list_div2').on('click', '[name = tagDelete]', function () {
            var tagOutNode = $(this).parent().parent();
            var tagNode = tagOutNode.find('.unit_list_div2_out_div');
            var tagName = $.trim(tagNode.html());
            layer.confirm('确定删除该单位', {icon: 3, title:'提示'}, function (index) {

                admin.req({
                    type: "post"
                    , url: '/admin/config/delete-tag'
                    , dataType: "json"
                    , cache: false
                    , data: {
                        tagName: tagName
                    }
                    , done: function () {
                        tagOutNode.remove();
                        layer.close(index);
                    }
                });
            })
        });
    });
</script>
