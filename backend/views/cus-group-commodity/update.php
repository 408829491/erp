<style>
    .layui-table-cell {
        height: auto;
        line-height: 28px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">团购名称</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="请输入活动名称, <5个字" autocomplete="off" class="layui-input" value="<?= $model->name ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">活动时间</label>
        <div class="layui-input-inline" style="width: 350px">
            <div style="width: 300px;display: flex;">
                <input type="text" name="date" lay-verify="required" class="layui-input" id="activityTime" readonly value="<?= $model->date ?>">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">购买限制</label>
        <div class="layui-input-inline" style="width: 600px;height:40px;display: flex">
            <div style="display: flex">
                <input type="radio" name="is_limit_buy_num" value="0" title="不限制" checked>
                <input type="radio" name="is_limit_buy_num" value="1" title="限制" <?php if ($model->is_limit_buy_num == 1) : ?>checked<?php endif; ?>>
            </div>
            <div style="margin-left: 10px;margin-top: 10px">是否限制单个用户团购份数(选择不限制，单人团购数将不生效)</div>
        </div>
    </div>
    <div class="layui-form-item">
        <label style="display: flex;margin-left: 20px;align-items: center">活动商品列表<button class="layui-btn layui-btn-sm" id="addSubList" style="margin-left: 15px">新增</button></label>
        <div class="layui-input-inline" style="width: 100%;margin-left: 20px;margin-top: 15px">
            <table id="subList" lay-filter="subList"></table>
            <script type="text/html" id="activityPrice">
                {{# if (d.activity_price != undefined){ }}
                <input type="text" name="activity_price" class="layui-input" value="{{d.activity_price}}">
                {{#  } else { }}
                <input type="text" name="activity_price" class="layui-input" value="0">
                {{#  } }}
            </script>
            <script type="text/html" id="limitBuy">
                {{# if (d.limit_buy != undefined){ }}
                <input type="text" name="limit_buy" class="layui-input" value="{{d.limit_buy}}">
                {{#  } else { }}
                <input type="text" name="limit_buy" class="layui-input" value="0">
                {{#  } }}
            </script>
            <script type="text/html" id="successNum">
                <input type="text" name="success_num" class="layui-input" value="{{d.success_num}}">
            </script>
            <script type="text/html" id="operation">
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form', 'upload', 'laydate', 'table'], function () {
        var $ = layui.$
            , form = layui.form
            , admin = layui.admin
            , upload = layui.upload
            , table = layui.table
            , laydate = layui.laydate;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段

            field.id = <?= $model->id ?>;

            // 子表商品保存
            var activity_prices = $("[name=activity_price]");
            var limit_buys = $("[name=limit_buy]");
            var success_nums = $("[name=success_num]");
            field.subList = layui.table.cache['subList'];
            var group_commoditys_name = "";
            var subListIndex = 0;
            field.subList.forEach(function (e) {
                // 不为空填充
                if (!$.isEmptyObject(e)) {
                    e.activity_price = activity_prices[subListIndex].value;
                    e.limit_buy = limit_buys[subListIndex].value;
                    e.success_num = success_nums[subListIndex].value;
                    e.cus_commodity_id = e.id;
                    group_commoditys_name += e.name;
                    group_commoditys_name += ",";
                    subListIndex += 1;
                }
            });
            field.group_commoditys_name = group_commoditys_name.substring(0, group_commoditys_name.length-1);

            var index = parent.layer.getFrameIndex(window.name); // 先得到当前iframe层的索引

            // 提交 Ajax 成功后，关闭当前弹层并重载表格
            admin.req({
                type: "post"
                , url: '/admin/cus-group-commodity/edit'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
                }
            });
        });

        laydate.render({
            elem: '#activityTime'
            ,type: 'date'
        });

        // 渲染subList
        var subList = <?= $subList ?>;

        table.render({
            elem: '#subList'
            , cols: [[
                {field: 'id', width: 100, title: 'ID'}
                , {field: 'name', title: '商品名称', minWidth: 100}
                , {field: 'unit', title: '单位', minWidth: 100}
                , {field: 'price', title: '市场价', minWidth: 100, style:'height:50px;'}
                , {field: 'activity_price', title: '活动价格', minWidth: 100, templet:'#activityPrice'}
                , {field: 'limit_buy', title: '单人限购', minWidth: 100, templet:'#limitBuy'}
                , {field: 'success_num', title: '成团人数', minWidth: 100, templet:'#successNum'}
                , {title: '操作', minWidth: 100, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , data: subList
            , page: false
            , limit: Number.MAX_VALUE
            , title: "列表"
            , style:'height:50px;'
        });

        //监听工具条
        table.on('tool(subList)', function (obj) {
            var data = obj.data;
            if (obj.event === 'del') {
                obj.del();
            }
        });

        $("#addSubList").click(function () {
            layer.open({
                type: 2
                , title: '商品选择'
                , content: '/admin/cus-commodity/list'
                , maxmin: true
                , area: ['1000px', '700px']
                , btn: ['确定', '取消']
                , yes: function (index, layero) {
                    var sonTable = layero.find('iframe')[0].contentWindow.layui.table.checkStatus('list').data;
                    table.reload('subList', {
                        data: layui.table.cache['subList'].concat(sonTable)
                    });
                    layer.close(index);
                }
            });
        })
    })
</script>