<form class="layui-form" style="padding: 15px;">
    <div class="layui-card">
        <div class="layui-card-header">基本信息</div>
        <div class="layui-card-body">
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>客户账号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" lay-verify="required"  placeholder="请为客户创建账号（客户账号为手机号，不能修改）" value="<?=$model->username?>" autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真写姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="nickname" placeholder="请输入真实姓名" value="<?=$model->nickname?>" autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">店铺名称</label>
                <div class="layui-input-block">
                    <input type="text" name="shop_name" placeholder="请输入店铺名称" value="<?=$model->shop_name?>" autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">客户类型</label>
                <div class="layui-input-block">
                    <select name="c_type" disabled>
                        <?php foreach ($formInfo['c_type'] as $item) : ?>
                            <option value="<?=$item->name?>" <?php if($item->name==$model->c_type):?>selected<?php endif;?>><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>货到付款</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="is_pay_on" <?php if($model->is_pay_on==1): ?>checked=""<?php endif;?> lay-skin="switch" lay-filter="switchTest" lay-text="支持|不支持" disabled>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">业务员</label>
                <div class="layui-input-block">
                    <select name="sale_man" disabled>
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
                    <input type="checkbox" <?php if($model->is_check==1): ?>checked=""<?php endif;?> name="is_check" lay-skin="switch" lay-filter="switchTest" lay-text="已审核|未审核" disabled>
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
                    <select name="area_name" lay-verify="required" disabled>
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
                    <select name="line_name" lay-verify="required" disabled>
                        <option value="">请选择线路</option>
                        <?php foreach ($formInfo['line'] as $item) : ?>
                            <option value="<?=$item->name?>" <?php if($item->name==$model->line_name):?>selected<?php endif;?>><?=$item->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>联系人</label>
                <div class="layui-input-block">
                    <input type="text" name="contact_name" lay-verify="required" value="<?=$model->contact_name?>" placeholder="请填写联系人"  autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>收货手机</label>
                <div class="layui-input-block">
                    <input type="text" name="receive_mobile" lay-verify="required|phone" value="<?=$model->receive_mobile?>" placeholder="请填写收货手机"  autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>详细地址</label>
                <div class="layui-input-block">
                    <input type="text" name="address" lay-verify="required" value="<?=$model->address?>" placeholder="请填写收货详细地址" autocomplete="off" class="layui-input layui-disabled">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>送货时间</label>
                <div class="layui-input-inline">
                    <select name="delivery_time" disabled>
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


<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'laydate','form','element'], function(){
        var $ = layui.$
    });
</script>