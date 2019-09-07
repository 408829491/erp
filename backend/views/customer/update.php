<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>客户账号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required"  placeholder="请为客户创建账号（客户账号为手机号，不能修改）" value="<?=$model->username?>" autocomplete="off" class="layui-input" disabled>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>密码</label>
                <div class="layui-input-block">
                    <input name="password" type="text"  placeholder="请设置登录密码,不修改可以留空"  autocomplete="off" class="layui-input" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真写姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="nickname" placeholder="请输入真实姓名" value="<?=$model->nickname?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">店铺名称</label>
                <div class="layui-input-block">
                    <input type="text" name="shop_name" placeholder="请输入店铺名称" value="<?=$model->shop_name?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">客户类型</label>
                <div class="layui-input-block">
                    <select name="c_type_id">
                        <?php foreach ($formInfo['c_type'] as $item) : ?>
                        <option value="<?=$item->id?>" <?php if($item->id==$model->c_type_id):?>selected<?php endif;?>><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>货到付款</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="is_pay_on" <?php if($model->is_pay_on==1): ?>checked=""<?php endif;?> lay-skin="switch" lay-filter="switchTest" lay-text="支持|不支持">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">业务员</label>
                <div class="layui-input-block">
                    <select name="sale_man">
                        <option value="无">无</option>
                        <?php foreach ($formInfo['sale_man'] as $item) : ?>
                            <option value="<?=$item->name?>" <?php if($item->name==$model->sale_man):?>selected<?php endif;?>><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="checkbox" <?php if($model->is_check==1): ?>checked=""<?php endif;?> name="is_check" lay-skin="switch" lay-filter="switchTest" lay-text="已审核|未审核">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">地址经度</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <input type="text" name="lng" style="width: 300px;" value="<?= $model->lng ?>" placeholder="长度<128个字" autocomplete="off" class="layui-input">
                </div>
                <label class="layui-form-label" style="width: 150px">地址纬度</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <input type="text" name="lat" style="width: 300px;" value="<?= $model->lat ?>" placeholder="长度<128个字" autocomplete="off" class="layui-input">
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
                            <option value="<?=$item->area_name?>" <?php if($item->area_name==$model->area_name):?>selected<?php endif;?>><?=$item->area_name?></option>
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
                            <option value="<?=$item->id?>:;<?=$item->name?>" <?php if($item->id==$model->line_id):?>selected<?php endif;?>><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系人</label>
                <div class="layui-input-block">
                    <input type="text" name="contact_name" lay-verify="required" value="<?=$model->contact_name?>" placeholder="请填写联系人"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>收货手机</label>
                <div class="layui-input-block">
                    <input type="text" name="receive_mobile" lay-verify="required|phone" value="<?=$model->receive_mobile?>" placeholder="请填写收货手机"  autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>详细地址</label>
                <div class="layui-input-block">
                    <input type="text" name="address" lay-verify="required" value="<?=$model->address?>" placeholder="请填写收货详细地址" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>送货时间</label>
                <div class="layui-input-inline">
                    <select name="delivery_time" >
                        <option value="">请选择送货时间</option>
                        <?php foreach ($formInfo['delivery_time'] as $item) : ?>
                            <option value="<?=$item->time_range?>" <?php if($item->time_range==$model->delivery_time):?>selected<?php endif;?>><?=$item->time_range?></option>
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

        // 地图
        var map, geolocation, positionPicker;

        map = new AMap.Map('container', {
            resizeEnable: true
            ,zoom: 16
            ,center: [<?= $model->lng ?>, <?= $model->lat ?>]
        });

        AMapUI.loadUI(['misc/PositionPicker'], function(PositionPicker) {
            positionPicker = new PositionPicker({
                mode: 'dragMap',
                map: map,
                iconStyle:{// 自定义外观
                    url:'/admin/imgs/a2_me.png',//图片地址
                    size:[20, 30],  // 要显示的点大小，将缩放图片
                    ancher:[10, 30],// 锚点的位置，即被size缩放之后，图片的什么位置作为选中的位置
                }
            });
            positionPicker.on('success', function(positionResult) {
                var loc = positionResult.position;
                setPosition(loc.lng, loc.lat);
            });
            positionPicker.start();
        })


        // 加载PoiPicker，loadUI的路径参数为模块名中 'ui/' 之后的部分
        AMapUI.loadUI(['misc/PoiPicker'], function(PoiPicker) {
            var poiPicker = new PoiPicker({
                input: 'pickerInput' //输入框id
            });
            // 监听poi选中信息
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