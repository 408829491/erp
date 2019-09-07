<style>
    .layui-card {
        padding: 10px;
    }

    .layui-card:hover {
        background-color: #fbfbfb;
    }

</style>
<div class="layui-fluid layui-form" style="line-height:38px">
    <div class="layui-card">
        <div class="layui-form-item">
            <div class="layui-row">
                <div class="layui-col-xs4">
                    客户类型：
                </div>
                <div class="layui-col-xs8" style="text-align: left">
                    <select lay-filter="c_type" name="typeId">
                        <option value="1">B端客户</option>
                        <option value="2">门店</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="layui-row">
            <div class="layui-col-xs4">
                计算方式：
            </div>
            <div class="layui-col-xs4" style="text-align: left">
                <select lay-filter="type" name="typeId">
                    <option value="1">加价值</option>
                    <option value="2">成本加价率</option>
                    <option value="3">销售加价率</option>
                </select>
            </div>
        </div>
    </div>
    <hr size="1" noshade="noshade" style="border:1px #cccccc">
    <div class="layui-form-item">
        <div style="display:inline-flex;" name="formula">
            进价 + 加价值"<input type="text" class="layui-input" name="price" value="0" style="width:100px;margin:0 5px 0 5px;">= 销售价
        </div>
    </div>
</div>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'laydate', 'form', 'element'], function () {
        var $ = layui.$
            , admin = layui.admin
            , form = layui.form;

        //智能定价
        form.on('select(type)', function (obj) {
            var val = obj.value
                , formulaObj = $(this).parents('.layui-card').find('[name=formula]')
                , label;
            if (val === '1') {
                label = '进价 + 加价值';
                tail = '';
            } else if (val === '2') {
                label = '进价 + 进价*加价率';
                tail = '%';
            } else {
                label = '成本 ÷（1 - 销售加价率）';
                tail = '%';
            }
            formulaObj.html(label + ' <input type="text" class="layui-input" name="price" value="0" style="width:100px;margin:0 5px 0 5px;">' + tail + ' = 销售价');
            calculate($(this), val)
        });


        //根据类型计算价格
        function calculate(obj, type) {
            var calculateDetailObj = obj.parents('.layui-card').find('[name=calculateDetail]')
                , inPrice = parseFloat(obj.parents().find('input[name=in_price]').val())
                , price = parseFloat(obj.parents('.layui-card').find('input[name=price]').val())
                , label = '计算明细:';
            switch (type) {
                case '1':
                    calculateDetailObj.html(label + inPrice + ' + ' + price + ' = ' + parseFloat(inPrice + price).toFixed(2));
                    break;
                case '2':
                    calculateDetailObj.html(label + inPrice + ' + (' + inPrice + ' * ' + (price) + '%) = ' + parseFloat(inPrice + inPrice * (price / 100)).toFixed(2));
                    break;
                case '3':
                    calculateDetailObj.html(label + inPrice + ' ÷ (1 - ' + (price) + '%) = ' + parseFloat(inPrice / (1 - price / 100)).toFixed(2));
                    break;
            }
        }

    });
</script>