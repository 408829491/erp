<div class="layui-fluid">
    <div class="layui-row">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="请输入线路名称/司机号码查询" autocomplete="off" class="layui-input" style="width: 300px;">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit  lay-filter="search-clear">
                            清除查询条件
                        </button>
                    </div>
                </div>
            </div>
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-xs4">
                            线路列表
                            <hr>
                            <div class="grid-demo grid-demo-bg1">
                                <table class="layui-hide" id="order-list" lay-filter="order-list"></table>
                            </div>
                        </div>
                        <div class="layui-col-xs8">
                            地图
                            <hr>
                            <div class="grid-demo">
                                <div id="container" style="width: 100%;height: 700px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <!-- 加载地图JSAPI脚本 -->
    <script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.13&key=f8b1e3ed79dd56680cfc4f316ee8a20a"></script>
    <script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
    <script>
        layui.config({
            base: '/admin/plugins/layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use(['index', 'table', 'laydate','element','form','yutons_sug'], function(){
            var table = layui.table
                ,laydate = layui.laydate
                ,element = layui.element
                ,form = layui.form;

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

            // 地图
            var map, mapMarkIcon;

            map = new AMap.Map('container', {
                resizeEnable: true
            });

            mapMarkIcon = new AMap.Icon({
                size: new AMap.Size(40, 18),    // 图标尺寸
                image: '/admin/imgs/car.png',  // Icon的图像
                imageSize: new AMap.Size(40, 18)   // 根据所设置的大小拉伸或压缩图片
            });

            table.render({
                elem: '#order-list'
                ,url: '/admin/delivery-line/get-line-list'
                ,title: '线路'
                ,cols: [[
                    {field:'name', title:'配送线路'}
                    ,{field:'driver_name', title:'司机'}
                    ,{field:'location', title:'定位',templet:function (d) {
                       if(d.lat){
                           return '<span style="color:green">正常</span>';
                       }else{
                           return '<span style="color:red">离线</span>';
                       }
                    }}
                ]]
                ,toolbar:false
                ,page:false
                ,done: function (res) {
                    res.data.forEach(function (item) {
                        if (item.lng != 0) {
                            // 添加点标记
                            var marker = new AMap.Marker({
                                position: new AMap.LngLat(item.lng, item.lat),   // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
                                offset: new AMap.Pixel(-10, -10),
                                icon: mapMarkIcon,
                                title: '',
                                zoom: 13
                            });

                            // 添加标记的点击事件
                            marker.on('click', function () {

                                // 创建 infoWindow 实例
                                var infoWindow = new AMap.InfoWindow({
                                    anchor: 'bottom-left',
                                    content: '路线：' + item.name + ' 司机：' + item.driver_name,
                                });

                                // 打开信息窗体
                                infoWindow.open(map, [item.lng, item.lat]);
                            });

                            map.add(marker);
                        }
                    });
                }
            });

            table.render({
                elem: '#order-list2'
                ,title: '用户数据表'
                ,url: '/admin/order/order-commodity-detail-data'
                ,cols: [[
                    {field:'order_no', title:'订单号',width:200}
                    ,{field:'delivery_date', title:'发货日期'}
                    ,{field:'name', title:'商品'}
                    ,{field:'unit', title:'单位', sort: true}
                    ,{field:'num', title:'发货数量'}
                    ,{field:'refund_num', title:'退货数量', sort: true}
                    ,{field:'total_price', title:'实际金额'}
                ]]
                ,toolbar:false
                ,page:false
            });

            //监听Tab页
            element.on('tab(component-tabs)', function(){
                var _this = this;
                //数据重载
                table.reload('order-list', {
                    page: {
                        curr: 1 //重新从第 1 页开始
                    }
                    ,where: {
                        status:_this.getAttribute('lay-data')
                    }
                });
            });

            //监听行点击
            table.on('row(order-list)', function(obj){
                if (obj.data.lng == 0) {
                    return ;
                }
                map.setCenter([obj.data.lng, obj.data.lat]);
            });

            //清除查询条件
            form.on('submit(search-clear)', function(obj){
                form.val("layui-form", {
                    'delivery_date': '',
                    'is_pay': '',
                    'create_time': '',
                    'keyword': '',
                    'source':''
                })
            });

            //发货日期
            laydate.render({
                elem: '#test-laydate-range-date'
            });


            //监听搜索
            form.on('submit(search)', function(data){
                var field = data.field;

                //执行重载
                table.reload('order-list', {
                    where: field
                });
            });


            /*//点击行checkbox选中
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
            });*/

        });
    </script>