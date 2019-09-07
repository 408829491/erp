<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="nickname" lay-verify="required"  placeholder="请填写姓名" value="<?=$model->nickname?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">仓库</label>
                <div class="layui-input-inline">
                    <select name="stock_name" >
                        <option value="">请选择仓库</option>
                        <option value="默认仓库" selected>默认仓库</option>
                    </select>
                </div>
                <label class="layui-form-label">角色</label>
                <div class="layui-input-inline">
                    <select name="r_id" disabled>
                        <option value="10" selected>分拣员</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">收货信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>登录账户</label>
                <div class="layui-input-inline">
                    <input type="text" name="username" lay-verify="required"  placeholder="请设置登录账户" value="<?=$model->username?>" autocomplete="off" class="layui-input layui-disabled">
                </div>
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                <div class="layui-input-inline">
                    <input name="password" type="text"  placeholder="请设置登录密码"  autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">设备地址</label>
                <div class="layui-input-inline">
                    <input type="text" name="device_addr" placeholder="请设置设备地址" value="<?=$model->device_addr?>" autocomplete="off" class="layui-input">
                </div>
                <label class="layui-form-label">设备密码</label>
                <div class="layui-input-inline">
                    <input name="device_pwd" type="text"  placeholder="请设置设备密码" autocomplete="off" class="layui-input" autocomplete="off" value="<?=$model->device_pwd?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">打印编号</label>
                <div class="layui-input-inline">
                    <input type="text" name="print_num"  placeholder="请设置打印编号" value="<?=$model->print_num?>" autocomplete="off" class="layui-input">
                </div>
                <label class="layui-form-label">打印密码</label>
                <div class="layui-input-inline">
                    <input name="print_pwd" type="text"  value="<?=$model->print_pwd?>" placeholder="请设置打印密码" autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>

            <div class="layui-form-item layui-hide">
                <input type="hidden" name="sort_id" id="sort_id">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">分拣任务分配</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <div id="root" style="width: 1200px;"></div>
            </div>
        </div>
    </div>
</form>


<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'laydate','form','element','transfer'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,transfer = layui.transfer
            ,form = layui.form;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var index = parent.layer.getFrameIndex(window.name);
            var ids = transfer.get(tb1,'r','id');
            if(ids && !(ids instanceof Array)){
                data.field.sort_id = ids;
            }else{
                data.field.sort_id = 0;
            }

            $.post({
                type: "post"
                , url: '/admin/sort-sorter/save?id=<?=$model->id?>'
                , dataType:'json'
                , data: data.field
                , success: function (e) {
                    if(e.code === 200){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }else{
                        layer.msg(e.data);
                    }
                }
            });
        });

        //数据源
        var data1 = [];
        var data2 = [];
        $.ajaxSettings.async = false;
        $.get({
            type: "post"
            , url: '/admin/sort-sorter/get-cate-list?id=<?=$model->id?>'
            , dataType:'json'
            , success: function (e) {
                data1=e.data.dataCate;
                data2=e.data.dataSort;
            }
        });

        var tb1 = transfer.render({
            elem: "#root", //指定元素
            cols: [
                {type: 'checkbox', fixed: 'left'},
                {field: 'id', title: 'ID', width: 80, sort: true},
                {field: 'name', title: '未选分类'}
            ],
            cols2: [
                {type: 'checkbox', fixed: 'left'},
                {field: 'id', title: 'ID', width: 80, sort: true},
                {field: 'name', title: '已选分类'}
            ],
            data: [data1,data2],
            tabConfig: {'page':false,'limit':100,'height':400,skin: 'line'}
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


    });
</script>