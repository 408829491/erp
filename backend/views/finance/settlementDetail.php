<style>
    .image_div {
        width:100px;
        height: 100px;
        display: flex;
        justify-items: center;
        align-items: center;
        margin-left: 20px;
    }
    .image_div_img {
        width:100px;
        height: 100px;
    }
    .image_div_close_icon {
        position: absolute;
        margin-top: -40px;
        margin-left: 80px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header">基础信息</div>
        <div class="layui-card-body">
            <div class="layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width: 300px">
                        结算单号:  <?=$settle_no?>
                    </div>
                    <div class="layui-input-inline" style="width: 300px">
                        单据日期:  <?=date('Y-m-d H:i:s',$create_time)?>
                    </div>
                    <div class="layui-input-inline" style="width: 300px">
                        交款人:  <?=$pay_user?>
                    </div>

                    <div class="layui-input-inline" style="width: 300px">
                        单据金额: <?=$price?>
                    </div>
                    <div class="layui-input-inline">
                        实收金额: <?=$actual_price?>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width: 300px">
                        制单人:  <?=$create_user?>
                    </div>                    <div class="layui-input-inline" style="width: 300px">
                        付款方式:  <?=$pay_way_text?>
                    </div>
                    <div class="layui-input-inline" style="width: 300px">
                        抹零金额:  <?=$reduction_price?>
                    </div>
                    <div class="layui-input-inline" style="width: 300px">
                        客户名称:  <?=$user_name?>
                    </div>

                </div>
                <div class="layui-form-item layui-hide">
                    <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">结算单列表</div>
            <div class="layui-card-body">


            <div class="layui-card">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
                <script type="text/html" id="operation">
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
                </script>

            </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-inline">
                        凭证图片<div style="display: flex;flex-flow: wrap;margin-left: 100px;" id="mainPicture" onclick=show_img("<?=$pic?>")>
                            <?php foreach (explode(':;', $pic) as $item) : ?>
                                <?php if($item):?>
                                    <div class="image_div">
                                        <image class="image_div_img" src="<?= $item?>"></image>
                                        <i class="layui-icon layui-icon-close image_div_close_icon"></i>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            备注：<?=$remark?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/html" id="actual_priceTpl">
            <input type="text" name="actual_price" placeholder="" autocomplete="off" class="layui-input order_c" id="purchase_price" lay-filter="actual_price" style="height: 28px" value="{{d.need_pay}}">
        </script>
        <script type="text/html" id="reduction_priceTpl">
            <input type="text" name="reduction_price" placeholder="" autocomplete="off" class="layui-input order_c" id="small_price" lay-filter="reduction_price" style="height: 28px" value="0">
        </script>
        <script type="text/html" id="remarkTpl">
            <input type="text" name="remark" placeholder="" autocomplete="off" class="layui-input" style="height: 28px" value="">
        </script>

        <script type="text/html" id="imgTpl">
            <img style="display: inline-block; width: 50%; height: 100%;" src= '{{d.pic}}?x-oss-process=image/resize,h_50'>
        </script>

        <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
        <script>
            layui.config({
                base: '/admin/plugins/layuiadmin/' //静态资源所在路径
            }).extend({
                index: 'lib/index' //主入口模块
            }).use(['index', 'table', 'laydate','form','element','yutons_sug','upload'], function(){
                var $ = layui.$
                    ,admin = layui.admin
                    ,table = layui.table
                    ,laydate = layui.laydate
                    ,form = layui.form
                    ,element = layui.element
                    ,yutons_sug  = layui.yutons_sug
                    ,upload  = layui.upload
                    ,table_name = []
                    ,table_username = [];

                element.render();

                //table设置默认参数
                table.set({
                    page: true
                    , parseData: function (res) {
                        return {
                            "code": 0,
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
                        statusCode: 0
                    }
                    , toolbar: true
                    , limit: 50
                    , limits: [10, 15, 20, 25, 30]
                    , text: {
                        none: '暂无相关数据'
                    }
                });

                //展示已知数据
                var subList = [<?=json_encode($detail)?>];
                table.render({
                    elem: '#subList'
                    ,cols: [[ //标题栏
                        {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                        ,{field: 'refer_no', title: '原始单号', width: 225, unresize: true}
                        ,{field: 'bill_type_text', title: '业务类型', minWidth: 120}
                        ,{field: 'should_price', title: '应收金额',totalRow: true}
                        ,{field: 'actual_price', title: '实收金额', unresize: true,totalRow: true}
                        ,{field: 'reduction_price', title: '抹零金额', unresize: true,totalRow: true}
                        ,{field: 'remark', title: '备注', unresize: true}
                    ]]
                    , data: subList
                    , totalRow: true
                    , page: false
                    , toolbar: false
                    , limit: Number.MAX_VALUE
                    , title: "列表"
                    ,done: function(res, curr, count){
                        tableDataTemp = res;
                    }
                });

                // 监听提交
                form.on('submit(layuiadmin-app-form-submit)', function (data) {
                    var field = data.field; // 获取提交的字段
                    field.list = layui.table.cache['subList'];

                    // 获取图片列表数据
                    var imgListData = "";
                    $(".image_div_img").each(function () {
                        imgListData += this.src;
                        imgListData += ":;";
                    });
                    if (imgListData !== undefined) {
                        imgListData = imgListData.substring(0,imgListData.length-2);
                        field['pic'] = imgListData;
                    }

                    var index = parent.layer.getFrameIndex(window.name);
                    admin.req({
                        type: "post"
                        , url: '/admin/finance/save'
                        , dataType: "json"
                        , cache: false
                        , data: field
                        , done: function () {
                            parent.layui.table.reload('order-list'); // 重载表格
                            parent.layer.close(index); // 再执行关闭
                        }
                    });
                });



                //监听工具条
                table.on('tool(subList)', function (obj) {
                    var data = obj.data;
                    if (obj.event === 'del') {
                        obj.del();
                    }
                });


                //发货日期
                laydate.render({
                    elem: '#test-laydate-range-date',
                    min:minDate()
                });


                var action = {
                    copyOrder: function(){
                        top.layui.admin.popupRight({
                            id: 'LAY_adminPopupLayerTest'
                            ,success: function(){
                                $('#'+ this.id).html('<div style="padding: 20px;">放入内容</div>');
                            }
                        });
                    },
                    addProductList: function(){
                        var create_window = layer.open({
                            id:'main',
                            type: 2,
                            content: '/admin/commodity-list/list',
                            area: ['1000px', '630px'],
                            title:'请选择商品',
                            maxmin: false,
                            btn: ['确定', '取消'],yes: function(index, layero){
                                var sonTable = layero.find('iframe')[0].contentWindow.layui.table.checkStatus('list').data;
                                sonTable.forEach(function (e) {
                                    e.purchase_price = e.price;
                                    e.purchase_num = 1;
                                    e.total_price = toDecimal2(e.purchase_price * e.purchase_num );
                                });
                                table.reload('subList', {
                                    data: layui.table.cache['subList'].concat(sonTable)
                                });
                                layer.close(index);
                            }
                        });
                    },
                    addProductSingle: function(){
                        table_name.num=$('#count').val();
                        table_name.total_price = table_name.num * table_name.price;
                        table.reload('subList', {
                            data: layui.table.cache['subList'].concat(table_name)
                        });
                    },
                    saveOrder:function(){
                        //保存订单数据
                    }
                };


                $('.layui-btn').on('click', function(e){
                    var type = $(this).data('type');
                    action[type] && action[type].call(this);
                    console.log(e);
                });

                //初始化姓名输入提示框
                yutons_sug.render({
                    id: "name", //设置容器唯一id
                    height: "200",
                    width:"400",
                    cols: [
                        [{
                            field: 'name',
                            title: '商品名称'
                        },
                            {
                                field: 'price',
                                title: '价格'
                            },
                            {
                                field: 'unit',
                                title: '单位'
                            }]
                    ], //设置表头
                    params: [
                        {
                            name: 'name',
                            field: 'name'
                        },
                        {
                            name: 'price',
                            field: 'price'
                        }, {
                            name: 'unit',
                            field: 'unit'
                        }],//设置字段映射，适用于输入一个字段，回显多个字段
                    type: 'sugTable', //设置输入框提示类型：sug-下拉框，sugTable-下拉表格
                    url: '/admin/commodity-list/index-data?keyword=' //设置异步数据接口,url为必填项,params为字段名
                });

                //获取商品筛选数据
                table.on('row(yutons_sug_name)', function(obj) {
                    var data = obj.data;
                    table_name = data;
                    $("#name").val(data.name);
                    $("#yutons_sug_name").next().hide().html("");
                });


                //监听单元格编辑
                table.on('edit(subList)', function(obj){
                    var value = obj.value;//得到修改后的值
                    obj.update({
                        total_price : (obj.data.price * value).toFixed(2)
                    });
                    table.reload('subList',{
                        data:table.cache[subList]
                    });
                });


            });

            //格式化价格
            function toDecimal2(x) {
                var f = parseFloat(x);
                if (isNaN(f)) {
                    return false;
                }
                var s = f.toString();
                var rs = s.indexOf('.');
                if (rs < 0) {
                    rs = s.length;
                    s += '.';
                }
                while (s.length <= rs + 2) {
                    s += '0';
                }
                return s;
            }

            // 设置最小可选的日期
            function minDate(){
                var now = new Date();
                now.setDate(now.getDate()+1);
                return now.getFullYear()+"-" + (now.getMonth()+1) + "-" + (now.getDate());
            }

            //显示表格大图
            function show_img(pic) {
                //页面层
                layer.open({
                    title:'商品图片',
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['800px', '645px'], //宽高
                    shadeClose: true, //开启遮罩关闭
                    content: '<div style="text-align:center"><img src="'+pic+'?x-oss-process=image/resize,w_800"/></div>'
                });
            }

        </script>