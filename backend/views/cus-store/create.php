
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>门店名称</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="长度<128个字" autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label" style="width: 150px"><span style="color: #ff6d6d;margin-right: 5px;">*</span>店铺地址</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="address" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>门店类型</label>
        <div class="layui-input-inline" style="width: 350px">
            <div style="width: 300px;display: flex;">
                <select lay-verify="required" name="type">
                    <option value="0">农村福祉店</option>
                    <option value="1">城市新零售</option>
                </select>
            </div>
        </div>
        <label class="layui-form-label" style="width: 150px"><span style="color: #ff6d6d;margin-right: 5px;">*</span>可配送距离（米）</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="limit_delivery_meter" lay-verify="required|number" style="width: 300px;" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">联系人</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="relation_people" style="width: 300px;" placeholder="" autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label" style="width: 150px">联系电话</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="relation_phone" style="width: 300px;" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>店长头像</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="relation_face_url" readonly style="width: 300px;" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-input-inline layui-btn-container" style="width: auto;">
            <button type="button" class="layui-btn layui-btn-primary" id="LAY_avatarUpload">
                <i class="layui-icon">&#xe67c;</i>上传图片
            </button>
            <button class="layui-btn layui-btn-primary" id="lookImg">查看图片
            </button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>门店大图</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="img" style="width: 300px;" readonly lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-input-inline layui-btn-container" style="width: auto;">
            <button type="button" class="layui-btn layui-btn-primary" id="LAY_avatarUpload2">
                <i class="layui-icon">&#xe67c;</i>上传图片
            </button>
            <button class="layui-btn layui-btn-primary" id="lookImg2">查看图片
            </button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>配送费</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="delivery_cost" style="width: 300px;" placeholder="金额(元)" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label"style="width: 150px"><span style="color: #ff6d6d;margin-right: 5px;">*</span>免配送费金额<i id="tips1" class="layui-icon layui-icon-about" style="font-size: 15px; color: #434343b5;"></i></label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="limit_send_price" style="width: 300px;" lay-verify="required" placeholder="金额(元)" autocomplete="off" class="layui-input">
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
    <div class="layui-form-item">
        <label class="layui-form-label">appId</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="app_id" style="width: 300px;" placeholder="请输入appId" autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label" style="width: 150px">appKey</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="app_key" style="width: 300px;" placeholder="请输入appKey" autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label" style="width: 150px">开启数据同步</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="checkbox" name="is_sync" value="1" lay-skin="switch" lay-text="开启|关闭" lay-filter="switch-check">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea name="info" placeholder="请输入内容" class="layui-textarea"></textarea>
        </div>
    </div>
    <div id="map">
        <div id="container" style="height: 500px"></div>
        <div id="pickerBox" style="z-index: 9999;position: absolute; margin-top: -450px; margin-left: 30px; width: 300px;">
            <input id="pickerInput" placeholder="输入关键字选取地点" />
            <div id="poiInfo"></div>
        </div>
    </div>
    <div style="margin-top: 30px;margin-left: 40px;font-size: 20px;color: red;margin-bottom: 30px;">管理员账号</div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>用户名</label>
        <div class="layui-input-inline" style="width: 350px;display: flex;align-items: center;">
            <input type="text" name="username" style="width: 300px;" lay-verify="username" placeholder="" autocomplete="off" class="layui-input">
            <div id="usernameHint"></div>
        </div>
        <label class="layui-form-label" style="width: 150px"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="password" name="password" style="width: 300px;" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit" value="确认添加">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit" value="确认编辑">
    </div>
</div>

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.13&key=f8b1e3ed79dd56680cfc4f316ee8a20a"></script>
<script src="//webapi.amap.com/ui/1.0/main.js?v=1.0.11"></script>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form', 'upload'], function () {
        var $ = layui.$
            , layer = layui.layerX
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
                , url: '/admin/cus-store/save'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
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

        // 添加用户名验证
        var usernameIsUsed = false;
        form.verify({
            username: function(value, item){ //value：表单的值、item：表单的DOM对象
                if (value == '') {
                    return '用户名不能为空';
                }

                if (!usernameIsUsed) {
                    return '用户名已经存在';
                }

            }
        });

        $('[name=username]').on('change', function () {
            admin.req({
                type: "post"
                , url: '/admin/cus-store/check-username-is-used'
                , dataType: "json"
                , cache: false
                , data: {
                    username: this.value
                }
                , done: function (data) {
                    if (data.msg == "ok") {
                        usernameIsUsed = true;
                        $("#usernameHint").html("<span>可用</span>");
                    } else {
                        usernameIsUsed = false;
                        $("#usernameHint").html("<span style='color: red'>不可用</span>");
                    }
                }
            });
        });

        upload.render({
            elem: '#LAY_avatarUpload'
            , url: '/admin/upload-file/index'
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            , done: function (res) {
                //上传完毕 添加保存字段
                $("[name=relation_face_url]").val(res.data.file);
            }
        });

        //查看图片
        $("#lookImg").on("click", function () {
            var src = $("[name=relation_face_url]").val();
            layui.layer.photos({
                photos: {
                    "title": "查看头像" //相册标题
                    , "data": [{
                        "src": src //原图地址
                    }]
                }
                , shade: 0.01
                , closeBtn: 1
                , anim: 5
            });
        });

        upload.render({
            elem: '#LAY_avatarUpload2'
            , url: '/admin/upload-file/index'
            ,method: 'post'  // 可选项。HTTP类型，默认post
            ,acceptMime: 'image/*'
            ,accept: 'images'
            , done: function (res) {
                //上传完毕 添加保存字段
                $("[name=img]").val(res.data.file);
            }
        });

        //查看图片
        $("#lookImg2").on("click", function () {
            var src = $("[name=img]").val();
            layui.layer.photos({
                photos: {
                    "title": "查看头像" //相册标题
                    , "data": [{
                        "src": src //原图地址
                    }]
                }
                , shade: 0.01
                , closeBtn: 1
                , anim: 5
            });
        });

        //tips绑定
        $("#tips1").on("mouseenter",function () {
            layui.layer.tips('下单金额到达指定金额之后免除配送费', this, {
                tips: [3, '#78BA32']
            });
        })

    })
</script>