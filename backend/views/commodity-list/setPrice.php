<style>
    .layui-card {
        padding: 10px;
    }

    .layui-card:hover {
        background-color: #fbfbfb;
    }

</style>
<div class="layui-fluid layui-form" style="line-height:38px">
    <input type="hidden" name="in_price" value="<?= Yii::$app->request->get('in_price') ?>">
    <?php foreach ($setting as $v): ?>
        <div class="layui-card">
            <input type="hidden" name="cType" value="<?=$v['id']?>">
            <div class="layui-form-item">
                <div class="layui-row">
                    <div class="layui-col-xs4">
                        计算方式：
                    </div>
                    <div class="layui-col-xs4" style="text-align: left">
                        <select lay-filter="type" name="typeId">
                            <option value="1" <?= $v['type'] == 1 ? 'selected' : '' ?>>加价值</option>
                            <option value="2" <?= $v['type'] == 2 ? 'selected' : '' ?>>成本加价率</option>
                            <option value="3" <?= $v['type'] == 3 ? 'selected' : '' ?>>销售加价率</option>
                        </select>
                    </div>
                    <div class="layui-col-xs4" style="text-align: right">
                        <?= $v['name'] ?>
                    </div>
                </div>
            </div>
            <hr size="1" noshade="noshade" style="border:1px #cccccc">
            <div class="layui-form-item">
                <div style="display:inline-flex;" name="formula">
                    <?php if ($v['type'] == 1) {
                        echo "进价 + 加价值";
                    } else if ($v['type'] == 2) {
                        echo "进价 + 进价*加价率";
                    } else {
                        echo "成本 ÷（1 - 销售加价率）";
                    } ?> <input type="text" class="layui-input" name="price" value="<?= $v['add_value'] ?>"
                                style="width:100px;margin:0 5px 0 5px;">= 销售价
                </div>
                <hr size="1" noshade="noshade" style="border:1px #cccccc">
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs8" name="calculateDetail">
                            计算明细:
                            <?php
                            $in_price = Yii::$app->request->get('in_price');
                            if ($v['type'] == 1) {
                                echo $in_price . " + " . $v['add_value'] . ' = ' . ($in_price + $v['add_value']);
                            } else if ($v['type'] == 2) {
                                echo $in_price . " + (" . $in_price . ' * ' . round($v['add_value'] / 100, 2) . '%) = ' . round($in_price + $in_price * $v['add_value'] / 100, 2);
                            } else {
                                echo $in_price . " ÷ (1-" . round($v['add_value'] / 100, 2) . '%) = ' . round($in_price / (1 - $v['add_value'] / 100), 2);
                            }
                            ?>
                        </div>
                        <div class="layui-col-xs4" style="text-align:right">
                            最近价格: <?= $v['recent_price'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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

            //绑定回车
            $('body').on('keypress blur input', 'input[name=price]', function (event) {
                //event.type === 'focusout'
                var type = $(this).parents('.layui-card').find('[name=typeId]').val();
                var c_type = $(this).parents('.layui-card').find('[name=cType]').val();
                var unit = $(this).parents('.layui-card').find('[name=unit]').val();
                if (event.keyCode === 13) {
                    var field = {'type':type,'add_value':$(this).val(),'commodity_id':<?=Yii::$app->request->get('id')?>,'unit':'<?=Yii::$app->request->get('unit')?>','c_type':c_type,'in_price':<?=Yii::$app->request->get('in_price')?>};
                    admin.req({
                        type: "post"
                        , url: '/admin/commodity-list/set-price-save'
                        , cache: false
                        , data: field
                        , done: function (e) {
                            if (e.code === 200) {
                                layer.msg('保存成功');
                            }else{
                                layer.msg('保存失败');
                            }
                        }
                    });
                }
                calculate($(this), type);
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