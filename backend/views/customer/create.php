<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
                <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>客户账号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required"  placeholder="请为客户创建账号（客户账号为手机号，不能修改）" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                <div class="layui-input-block">
                    <input name="password" type="text"  placeholder="请设置登录密码" lay-verify="required" autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真写姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="nickname" placeholder="请输入真实姓名" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">店铺名称</label>
                <div class="layui-input-block">
                    <input type="text" name="shop_name" placeholder="请输入店铺名称" value="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">客户类型</label>
                <div class="layui-input-block">
                    <select name="c_type_id">
                        <?php foreach ($formInfo['c_type'] as $item) : ?>
                            <option value="<?=$item->id?>"><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>货到付款</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="is_pay_on" lay-skin="switch" lay-filter="switchTest" lay-text="支持|不支持">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">业务员</label>
                <div class="layui-input-block">
                    <select name="sale_man">
                        <option value="无">无</option>
                        <?php foreach ($formInfo['sale_man'] as $item) : ?>
                            <option value="<?=$item->name?>"><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="checkbox" checked="" name="is_check" lay-skin="switch" lay-filter="switchTest" lay-text="已审核|未审核">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">地址经度</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <input type="text" name="lng" style="width: 300px;" placeholder="长度<128个字" autocomplete="off" class="layui-input">
                </div>
                <label class="layui-form-label" style="width: 150px">地址纬度</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <input type="text" name="lat" style="width: 300px;" placeholder="长度<128个字" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div id="map">
                <div id="container" style="height: 500px"></div>
                <div id="pickerBox" style="z-index: 9999;position: absolute; margin-top: -450px; margin-left: 30px; width: 300px;">
                    <input id="pickerInput" placeholder="输入关键字选取地点" />
                    <div id="poiInfo"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">收货信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>区域</label>
                <div class="layui-input-block">
                    <select name="area_name" lay-verify="required">
                        <option value="">请选择区域</option>
                        <?php foreach ($formInfo['area'] as $item) : ?>
                            <option value="<?=$item->area_name?>"><?=$item->area_name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>线路</label>
                <div class="layui-input-block">
                    <select name="line_name" lay-verify="required">
                        <option value="">请选择线路</option>
                        <?php foreach ($formInfo['line'] as $item) : ?>
                            <option value="<?=$item->id?>:;<?=$item->name?>"><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系人</label>
                <div class="layui-input-block">
                    <input type="text" name="contact_name" lay-verify="required" placeholder="请填写联系人"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>收货手机</label>
                <div class="layui-input-block">
                    <input type="text" name="receive_mobile" lay-verify="required|phone" placeholder="请填写收货手机"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>详细地址</label>
                <div class="layui-input-block">
                    <input type="text" name="address" lay-verify="required" placeholder="请填写收货详细地址" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>送货时间</label>
                <div class="layui-input-inline">
                    <select name="delivery_time" >
                        <option value="">请选择送货时间</option>
                        <?php foreach ($formInfo['delivery_time'] as $item) : ?>
                            <option value="<?=$item->time_range?>"><?=$item->time_range?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item layui-hide">
                <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认">
            </div>
        </div>
    </div>
</form>

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.13&key=f8b1e3ed79dd56680cfc4f316ee8a20a"></script>
<script src="//webapi.amap.com/ui/1.0/main.js?v=1.0.11"></script>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
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
            var index = parent.layer.getFrameIndex(window.name);

            var field = data.field;
            var lineName = field['line_name'].split(":;");
            field['line_id'] = lineName[0];
            field['line_name'] = lineName[1];

            $.post({
                type: "post"
                , url: '/admin/customer/save'
                , dataType:'json'
                , data: field
                , success: function (e) {
                    console.log(e);
                    if(e.code === 200){
                        layer.msg('提交成功', {time:2000});
                        parent.layui.table.reload('customer-list'); // 重载表格
                        parent.layer.close(index); // 再执行关闭
                    }else{
                        layer.msg(e.data);
                    }
                }
            });
        });

        // 地图
        var map, geolocation, positionPicker;

        map = new AMap.Map('container', {
            resizeEnable: true
        });

        map.plugin('AMap.Geolocation', function() {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,//是否使用高精度定位，默认:true
                timeout: 10000,          //超过10秒后停止定位，默认：无穷大
                buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                buttonPosition:'RB',
                expandZoomRange:true,
                zooms:[3, 20]
            });
            map.addControl(geolocation);
            geolocation.getCurrentPosition();
            //AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
            //AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
        });

        AMapUI.loadUI(['misc/PositionPicker'], function(PositionPicker) {
            positionPicker = new PositionPicker({
                mode: 'dragMap',
                map: map,
                iconStyle:{//自定义外观
                    url:'/admin/imgs/a2_me.png',//图片地址
                    size:[20, 30],  //要显示的点大小，将缩放图片
                    ancher:[10, 30],//锚点的位置，即被size缩放之后，图片的什么位置作为选中的位置
                }
            });
            positionPicker.on('success', function(positionResult) {
                var loc = positionResult.position;
                setPosition(loc.lng, loc.lat);
            });
            positionPicker.start();
        });


        //加载PoiPicker，loadUI的路径参数为模块名中 'ui/' 之后的部分
        AMapUI.loadUI(['misc/PoiPicker'], function(PoiPicker) {
            var poiPicker = new PoiPicker({
                input: 'pickerInput' //输入框id
            });
            //监听poi选中信息
            poiPicker.on('poiPicked', function(poiResult) {
                var loc = poiResult.item.location;
                map.setCenter(loc);
                setPosition(loc.lng, loc.lat);
            });
        });

        function setPosition(lng, lat){
            $("input[name='lng']").val(lng);
            $("input[name='lat']").val(lat);
        }

    });
</script>