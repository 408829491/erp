<form class="layui-form"  style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header">配置信息</div>
        <div class="layui-card-body">

                    <div class="layui-form-item">
                        <label class="layui-form-label lay">订单发货单</label>
                        <div class="layui-input-inline">
                            <select name="orderPrinter" class="print">
                                <option value="">请选择打印机</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="orderTpl">
                                <option value="1">订单发货单模板</option>
                            </select>
                        </div>
                    </div>

                <div class="layui-form-item">
                    <label class="layui-form-label lay">订单退货单</label>
                    <div class="layui-input-inline">
                        <select name="orderReturnPrinter" class="print">
                            <option value="">请选择打印机</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="orderReturnTpl" >
                            <option value="1">订单退货单模板</option>
                        </select>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label lay">分拣单</label>
                    <div class="layui-input-inline">
                        <select name="pickPrinter" class="print">
                            <option value="">请选择打印机</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="pickTpl" >
                            <option value="1">分拣模板</option>
                        </select>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label lay">采购单</label>
                    <div class="layui-input-inline">
                        <select name="purchasePrinter" class="print">
                            <option value="">请选择打印机</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="purchaseTpl" >
                            <option value="1">采购单模板</option>
                        </select>
                    </div>
                </div>



                <div class="layui-form-item">
                    <label class="layui-form-label lay">采购收货单</label>
                    <div class="layui-input-inline">
                        <select name="purchaseTakePrinter" class="print">
                            <option value="">请选择打印机</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="purchaseTakeTpl">
                            <option value="1">采购收货单模板</option>
                        </select>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label lay">采购退货单</label>
                    <div class="layui-input-inline">
                        <select name="purchaseReturnPrinter" class="print">
                            <option value="">请选择打印机</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="purchaseReturnTpl" >
                            <option value="1">采购退货单模板</option>
                        </select>
                    </div>
                </div>

            <div class="layui-form-item">
                <label class="layui-form-label lay">入库单</label>
                <div class="layui-input-inline">
                    <select name="inStoragePrinter" class="print">
                        <option value="">请选择打印机</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="inStorageTpl" >
                        <option value="1">入库单模板</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label lay">盘点单</label>
                <div class="layui-input-inline">
                    <select name="checkPrinter" class="print">
                        <option value="">请选择打印机</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="checkTpl" >
                        <option value="1">盘点单模板</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label lay">拣货单</label>
                <div class="layui-input-inline">
                    <select name="summaryPrinter" class="print">
                        <option value="">请选择打印机</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="summaryTpl" >
                        <option value="1">拣货单模板</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label lay">客户对账单</label>
                <div class="layui-input-inline">
                    <select name="soaPrinter" class="print">
                        <option value="">请选择打印机</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="soaTpl" >
                        <option value="1">客户对账单模板</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="padding-left:110px;">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认" class="layui-btn layui-btn-sm layui-btn-normal">
                <div class="layui-inline" style="margin-left:30px;">
                    <ul>
                        <li><a href="/uploads/CLodop.zip">下载打印控件</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</form>
<script src="http://localhost:8000/CLodopfuncs.js?priority=1"></script>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script src="/admin/plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'laydate','form','element'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,form = layui.form;

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            layui.each(data.field, function(index, item){
                localStorage.setItem(index,item);
            });
            layer.msg('成功');
        });
    });

    //装载本地打印机

    var LODOP=getCLodop();
    var count = LODOP.GET_PRINTER_COUNT();
    var selected;
    $(".print").each(function(index,element) {
        for(var i=0;i<count;i++){
            var option = document.getElementById('print');
            if(LODOP.GET_PRINTER_NAME(i) === localStorage.getItem($(element).attr('name'))){
                selected = 'selected';
            }else{
                selected = '';
            }
            $(element).append('<option value="'+LODOP.GET_PRINTER_NAME(i)+'" '+selected+'>'+LODOP.GET_PRINTER_NAME(i)+'</option>');
        }
    });

</script>