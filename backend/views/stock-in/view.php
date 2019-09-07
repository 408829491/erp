<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 15px">
    <div class="layui-card">
        <div class="layui-card-header">基本信息<div style="float:right" class="layui-hide"><button class="layui-btn layui-btn-xs layui-btn-normal" id="copy-order" data-type="copyOrder">复制已有订单</button></div>
        </div>
        <div class="layui-card-body">
            <div class="layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">入库仓库</label>
                        <div class="layui-input-inline">
                            <select name="store_id_name">
                                <option value="默认仓库">默认仓库</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">入库类型</label>
                        <div class="layui-input-inline">
                            <select name="type" disabled>
                                <option value="2">其它类型</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">制单人</label>
                        <div class="layui-input-inline">
                            <input type="text" name="operator" id="operator" autocomplete="off" value="<?=Yii::$app->user->identity['username']?>" class="layui-input layui-disabled" style="width:210px" lay-verify="required" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">收货商品清单</div>
        <div class="layui-card-body">

            <div class="layui-card">
                <table class="layui-hide" id="subList" lay-filter="subList"></table>
                <script type="text/html" id="operation">
                    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
                </script>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-inline">
                        <label class="layui-form-label">备注</label>
                        <div class="layui-input-inline">
                            <input type="text" name="remark" id="remark" placeholder="请输入备注" autocomplete="off" class="layui-input" style="width:850px" value="<?=$remark?>">
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/html" id="imgTpl">
                <img style="display: inline-block; width: 50%; height: 100%;" src= '{{d.pic}}?x-oss-process=image/resize,h_50'>
            </script>

            <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
            <script>
                layui.config({
                    base: '/admin/plugins/layuiadmin/' //静态资源所在路径
                }).extend({
                    index: 'lib/index' //主入口模块
                }).use(['index', 'table', 'laydate','form','element','yutons_sug','selectN','selectY'], function(){
                    var $ = layui.$
                        ,admin = layui.admin
                        ,table = layui.table
                        ,form = layui.form
                        ,element = layui.element;

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
                    var subList = <?=json_encode($details)?>;
                    table.render({
                        elem: '#subList'
                        ,cols: [[ //标题栏
                            {field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true, totalRowText: '合计:'}
                            ,{field: 'pic', title: '商品图片', width: 100,templet: '#imgTpl', unresize: true}
                            ,{field: 'name', title: '商品名称', minWidth: 100}
                            ,{field: 'unit', title: '单位', width: 80}
                            ,{field: 'price', title: '单价（元）', width: 150}
                            ,{field: 'num', title: '入库数量',width: 120, unresize: true,}
                            ,{field: 'total_price', title: '总金额（元）',width: 140, unresize: true,totalRow: true}
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

                });


            </script>