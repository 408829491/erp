<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px;">选择导出时间:</label>
                    <div class="layui-input-inline">
                        <input type="text" name='delivery_date' class="layui-input" lay-verify="required"
                               id="laydate-range-date" placeholder="请选择发货日期">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px;">选择导出字段:</label>
                    <div class="layui-inline" style="padding-left:30px;">
                        <input type="checkbox" name="ex_field[]" value="order_no" title="订单号" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="nick_name" title="客户名称" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="price" title="下单金额" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="delivery_date" title="发货日期" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="status" title="订单状态" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="is_pay_text" title="付款状态" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="source_text" title="订单来源" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="line_name" title="线路" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="driver_name" title="司机" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="address_detail" title="收货地址" lay-skin="primary" checked>
                        <input type="checkbox" name="ex_field[]" value="receive_name" title="收货人" lay-skin="primary" checked>
                    </div>
                </div>
            </div>

            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit"
                       id="layuiadmin-app-form-submit" value="确认添加">
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
    }).use(['index', 'table', 'laydate', 'form', 'element'], function () {
        var $ = layui.$
            , admin = layui.admin
            , table = layui.table
            , laydate = layui.laydate
            , form = layui.form
            , element = layui.element;

        //发货日期
        laydate.render({
            elem: '#laydate-range-date'
            ,range: true
            ,theme: 'molv'

        });

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段
            admin.req({
                type: "post"
                , url: '/admin/order/export-order-list'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function (d) {
                    if(d.code == 200){
                        window.location.href=d.data.fileName;
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    }else{
                        layer.msg('导出失败');
                    }

                }
            });
        });
    });
</script>