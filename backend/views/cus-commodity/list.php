<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list">
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
            </div>
        </div>

        <div class="layui-card-body">
            <table id="list" lay-filter="list"></table>
            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
            </div>
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
        </div>
    </div>
</div>
</div>
<script type="text/html" id="imgTpl">
    <img style="display: inline-block; width: 50%; height: 100%;" src= "{{ d.pic.split(':;')[0] }}?x-oss-process=image/resize,h_50">
</script>

<script type="text/html" id="count-Tpl">
     <input type="text" name="num" lay-filter="num" autocomplete="off" class="layui-input" style="height: 28px;text-align:center" value="{{d.num}}">
</script>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'laydate','form','element'], function(){
        var  table = layui.table
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
                    "data": res.data.list
                }
            }
            , request: {
                pageName: 'pageNum',
                limitName: 'pageSize'
            }
            , response: {
                statusCode: 200
            }
            , toolbar: false
            , limit: 10
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        var field1 = {};
        var tempData = [];
        parent.layui.table.cache['subList'].forEach(function (e) {
            if (!$.isEmptyObject(e)) {
                tempData.push(e.id);
            }
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            var field = {};
            data.field['ids'] = tempData;
            field.filterProperty = JSON.stringify(data.field);

            //执行重载
            table.reload('list', {
                where: field
            });
        });

        // 获取分类
        var typeDataList;

        admin.req({
            type: "get"
            , url: '/admin/cus-commodity-category/first-tier-data'
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
            , url: '/admin/cus-commodity/find-commodity-unit-data-list'
            , cols: [[
                {type: 'checkbox', fixed: 'left', minWidth: 100}
                , {field: 'id', width: 100, title: 'ID', sort: true}
                 , {field: 'pic', title: '商品图片', width: 100,templet: function(d){
                     return '<div onclick=show_img("'+d.pic+'") ><img src="'+d.pic+'?x-oss-process=image/resize,h_50" alt="" width="40px" height="30px"></a></div>';
                 }}
                , {field: 'type_name', title: '商品分类', minWidth: 100}
                , {field: 'name', title: '商品名称', minWidth: 100}
                , {field: 'unit', title: '单位', minWidth: 80}
                , {field: 'price', title: '价格', minWidth: 100}
                , {field: 'num', title: '数量', minWidth: 100,templet: '#count-Tpl',edit: 'number'}
            ]]
            , title: "列表"
             , where: {'filterProperty': JSON.stringify({'ids': tempData})}
        });

        //点击行checkbox选中
        $(document).on("click",".layui-table-body table.layui-table tbody tr", function () {
            var index = $(this).attr('data-index');
            var tableBox = $(this).parents('.layui-table-box');
            //存在固定列
            if (tableBox.find(".layui-table-fixed.layui-table-fixed-l").length>0) {
                tableDiv = tableBox.find(".layui-table-fixed.layui-table-fixed-l");
            } else {
                tableDiv = tableBox.find(".layui-table-body.layui-table-main");
            }
            var checkCell = tableDiv.find("tr[data-index=" + index + "]").find("td div.laytable-cell-checkbox div.layui-form-checkbox I");
            if (checkCell.length>0) {
                checkCell.click();
            }
        });

        $(document).on("click", "td div.laytable-cell-checkbox div.layui-form-checkbox", function (e) {
            e.stopPropagation();
        });
        //监听数量操作

    });

    //显示表格大图
    function show_img(pic) {
        //页面层
        layer.open({
            title:'商品图片',
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['400px', '350px'], //宽高
            shadeClose: true, //开启遮罩关闭
            content: '<div style="text-align:center"><img src="'+pic+'?x-oss-process=image/resize,h_800" style="width: 400px;height:300px;"/></div>'
        });
    }
</script>
