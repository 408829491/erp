
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <input type="hidden" value="<?= $pid ?>" name="pid">
    <div class="layui-form-item">
        <label class="layui-form-label">分类名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="必填"
                   autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序号</label>
        <div class="layui-input-inline">
            <input type="text" name="sequence" style="width: 300px;" value="0"
                   autocomplete="off" class="layui-input">
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
        index: 'lib/index', // 主入口模块
        treetable: 'treetable-lay/treetable'
    }).use(['index', 'form', 'upload'], function () {
        var $ = layui.$
            , layer = layui.layer
            , form = layui.form
            , admin = layui.admin
            , upload = layui.upload;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段

            var index = parent.layer.getFrameIndex(window.name); // 先得到当前iframe层的索引

            // 提交 Ajax 成功后，关闭当前弹层并重载表格
            admin.req({
                type: "post"
                , url: '/admin/commodity-category/save'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.treetable.render({
                        treeColIndex: 2,          // treetable新增参数
                        treeSpid: 0,             // treetable新增参数
                        treeIdName: 'id',       // treetable新增参数
                        treePidName: 'pid',     // treetable新增参数
                        treeDefaultClose: true,   // treetable新增参数
                        treeLinkage: true,        // treetable新增参数
                        elem: '#list',
                        url: '/admin/commodity-category/index-data',
                        cols: [[
                            {type: 'checkbox', fixed: 'left', minWidth: 100}
                            , {field: 'id', width: 100, title: 'ID'}
                            , {field: 'name', title: '分类名称', minWidth: 100}
                            , {title: '操作', minWidth: 250, align: 'center', fixed: 'right', toolbar: '#operation'}
                        ]],
                        done: function () {
                            layer.closeAll('loading');
                        }
                    });
                    parent.layui.table.reload('list'); // 重载表格

                    parent.layer.close(index); // 再执行关闭
                }
            });
        });

    })
</script>