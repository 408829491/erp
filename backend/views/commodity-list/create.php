<style>
    .table_th {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table_tr {
        display: flex;
    }

    .table_td {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px 10px;
    }

    .table_operation_add {
        width: 20px;
        height: 20px;
    }

    .table_operation_sub {
        width: 20px;
        height: 20px;
        margin-top: 5px;
    }

    .table_td_div {
        display: flex;
        align-items: center;
    }

    .table_td_div_div {
        width: 80px;
        margin-top: 1px;
    }

    .table_td_div_div input {
        border: 0;
        background-color: #F2F2F2;
    }

    .table_td_div2 {
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .table_td_div2_div {
        display: flex;
        align-items: center;
    }

    .table_td_div2_div_div {
        width: 80px;
        margin-top: 1px;
    }

    .table_td_div2_div_div2 {
        padding-left: 5px;
        padding-right: 5px;
        width: 70px;
    }

    .table_td_div2_div_div2 input {
        border: 0;
        height: 30px;
    }

    .table_td_div2_div_div input {
        border: 0;
        background-color: #F2F2F2;
    }

    .image_div {
        width: 100px;
        height: 100px;
        display: flex;
        justify-items: center;
        align-items: center;
        margin-left: 20px;
    }

    .image_div_img {
        width: 100px;
        height: 100px;
    }

    .image_div_close_icon {
        position: absolute;
        margin-top: -40px;
        margin-left: 80px;
    }

    .unit_price_div {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .unit_price_div_text {
        width: 80px;
    }
</style>
<div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list"
     style="padding: 20px 30px 0 0;">
    <div class="layui-form-item">
        <label class="layui-form-label">是否上架</label>
        <div class="layui-input-inline">
            <input type="checkbox" name="is_online" lay-skin="switch" lay-text="是|否" checked>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>商品分类</label>
        <div class="layui-input-inline" style="width: 350px">
            <div style="width: 300px;display: flex;">
                <select lay-filter="parentType" lay-verify="required" name="type_first_tier_id">
                    <option value="">一级分类</option>
                </select>
                <select name="type_id" lay-verify="required">
                    <option value="">二级分类</option>
                </select>
            </div>
        </div>

        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>商品编码</label>
        <div class="layui-input-inline" style="display: flex;width: 350px">
            <input type="text" name="commodity_code" style="width: 300px;" placeholder="长度<30,必填" lay-verify="required"
                   autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>商品名称</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="name" style="width: 300px;" lay-verify="required" placeholder="长度<30个字,必填,如:白菜"
                   autocomplete="off" class="layui-input">
        </div>
        <label class="layui-form-label">商品别名</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="alias" style="width: 300px;" placeholder="长度<20个字" autocomplete="off"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">商品品牌</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="brand" style="width: 300px;" placeholder="长度<20个字" autocomplete="off"
                   class="layui-input">
        </div>
        <label class="layui-form-label">商品产地</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="product_place" style="width: 300px;" placeholder="长度<30个字" autocomplete="off"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">助记码</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="pinyin" style="width: 300px;" placeholder="长度<128个字" autocomplete="off"
                   class="layui-input">
        </div>
        <label class="layui-form-label">损耗率</label>
        <div class="layui-input-inline" style="display:flex;width: 350px;align-items: center">
            <input type="text" name="loss_rate" style="width: 300px;" placeholder="长度<30个字" autocomplete="off"
                   class="layui-input">
            <span style="font-size: 20px">%</span>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">存储方式&保质期</label>
        <div class="layui-input-inline" style="width: 350px">
            <input type="text" name="durability_period" style="width: 300px;" placeholder="填写保质期" autocomplete="off"
                   class="layui-input">
        </div>
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>采购类型</label>
        <div class="layui-input-inline" style="display:flex;width: 650px;align-items: center">
            <div class="layui-inline">
                <div id="purchase_ids">
                </div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">描述</label>
        <div class="layui-input-inline" style="width: 350px">
            <textarea type="text" name="summary" style="width: 300px;" placeholder="填写描述" autocomplete="off"
                      class="layui-textarea"></textarea>
        </div>
      <!-- <label  class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>进货价</label>
       <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="in_price" style="width: 300px;" lay-verify="required" placeholder="" autocomplete="off"
                  class="layui-input">
        </div>-->
    </div>
    <div class="layui-form-item" style="display: flex">
        <label class="layui-form-label">标签</label>
        <div class="layui-input-inline" id="tagList" style="width: auto;">

        </div>
    </div>
    <div class="layui-form-item" style="display: flex;flex-direction: column">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>商品主图</label>
        <div style="display: flex;flex-flow: wrap;" id="mainPicture">
            <div style="margin-left:20px;width:100px;height: 100px;border:1px dashed #ffffff;display: flex;align-items: center;justify-content: center;"
                 id="addMainPicture">
                <i class="layui-icon layui-icon-camera-fill" style="font-size: 30px; color: #000000;"></i>
            </div>
        </div>
    </div>
    <div class="layui-form-item" style="display: flex;flex-direction: column">
        <label class="layui-form-label"><span style="color: #ff6d6d;margin-right: 5px;">*</span>商品价格</label>
        <div style="display: flex;padding-left: 20px">
            <table style="width: 100%;" id="unitTable">
                <tr style="display: flex;background-color: #E7E7E7;height:45px;">
                    <th class="table_th">操作</th>
                    <th class="table_th">单位</th>
                    <th class="table_th">描述</th>
                    <th class="table_th">进货价</th>
                    <th class="table_th">市场价</th>
                    <th class="table_th">客户价格</th>
                    <th class="table_th">是否可售卖</th>
                </tr>
                <tr class="table_tr">
                    <td class="table_td">
                        <image src="/admin/imgs/add.png" class="table_operation_add" name="addUnit"></image>
                    </td>
                    <td class="table_td">
                        <div class="table_td_div">
                            <span>基础单位</span>
                            <input type="hidden" value="0" name="unit_base_self_ratio">
                            <input type="hidden" value="1" name="unit_is_basics_unit">
                            <div class="table_td_div_div">
                                <select lay-filter="basicUnit" name="unit_unit">

                                </select>
                            </div>
                        </div>
                    </td>
                    <td class="table_td">
                        <textarea name="unit_desc" placeholder="长度<20个字" class="layui-textarea"></textarea>
                    </td>
                    <td class="table_td">
                        <input name="in_price" placeholder="进货价" class="layui-input" value="0" style="width: 100px">
                    </td>
                    <td class="table_td">
                        <input name="price2" placeholder="市场价" class="layui-input" value="0" style="width: 100px">
                    </td>
                    <td class="table_td" name="unitPrice">

                    </td>
                    <td class="table_td">
                        <input type="checkbox" name="unit_is_sell" lay-skin="switch" checked>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">商品排序</label>
        <div class="layui-input-inline" style="width: 350px;">
            <input type="text" name="sequence" style="width: 300px;" autocomplete="off" value="0" class="layui-input">
        </div>
        <div class="layui-input-inline" style="display:flex;width: 350px;align-items: center">
            <input type="radio" name="is_rough" value="Y" title="标品">
            <input type="radio" name="is_rough" value="N" title="非标品" checked>
            <input type="checkbox" name="is_time_price" title="是否时价" checked>
        </div>
    </div>
    <!--<div class="layui-form-item">
        <label class="layui-form-label">图文详情</label>
    </div>-->
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-submit" id="layuiadmin-app-form-submit"
               value="确认添加">
        <input type="button" lay-submit lay-filter="layuiadmin-app-form-edit" id="layuiadmin-app-form-edit"
               value="确认编辑">
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'form', 'upload', 'selectN'], function () {
        var $ = layui.$
            , layer = layui.layerX
            , form = layui.form
            , admin = layui.admin
            , selectN = layui.selectN
            , upload = layui.upload;

        var productCategory;
        var productCategoryElement = $("[name=type_first_tier_id]");
        var typeIdElement = $("[name=type_id]");
        // 商品分类
        admin.req({
            type: "get"
            , url: '/admin/commodity-category/index-data'
            , dataType: "json"
            , cache: false
            , done: function (res) {
                productCategory = res.data;
                for (var i = 0; i < productCategory.length; i++) {
                    if (productCategory[i].pid == 0) {
                        productCategoryElement.append('<option value="' + productCategory[i].id + '">' + productCategory[i].name + '</option>');
                    }
                }
                form.render();
            }
        });

        form.on('select(parentType)', function (data) {
            typeIdElement.html('<option value="">二级分类</option>');
            for (var i = 0; i < productCategory.length; i++) {
                if (productCategory[i].pid == data.value) {
                    typeIdElement.append('<option value="' + productCategory[i].id + '">' + productCategory[i].name + '</option>');
                }
            }
            form.render();
        });

        // 标签查询
        var tagList;
        admin.req({
            type: "get"
            , url: '/admin/config/tag-list'
            , dataType: "json"
            , cache: false
            , done: function (res) {
                tagList = res.data;
                for (var i = 0; i < tagList.length; i++) {
                    $("#tagList").append(
                        '<input type="checkbox" name="tag" title="' + tagList[i] + '" checked value="' + tagList[i] + '">'
                    )
                }
                form.render();
            }
        });

        // 监听提交
        form.on('submit(layuiadmin-app-form-submit)', function (data) {
            var field = data.field; // 获取提交的字段
            field['provider_name'] = catIns1.lastName;
            field['agent_name'] = catIns1.lastName;
            // 获取单位子表数据
            var unit_unit = $('[name=unit_unit] option:selected');
            var unit_is_basics_unit = $('[name=unit_is_basics_unit]');
            var unit_base_self_ratio = $('[name=unit_base_self_ratio]');
            var unit_desc = $('[name=unit_desc]');
            var unit_is_sell = $('[name=unit_is_sell]');
            var unitPrice = $('[name=unitPrice]');
            var unitPrice2 = $('[name=price2]');
            var in_price = $('[name=in_price]');

            var temp = [];
            for (var i = 0; i < unit_unit.length; i++) {
                if (unit_is_basics_unit[i].value == 1) {
                    // 基础单位
                    field['unit'] = unit_unit[i].value;
                    field['price'] = unitPrice2[i].value;
                }
                var temp1 = {};
                temp1['price'] = unitPrice2[i].value;
                temp1['in_price'] = in_price[i].value;
                temp1['unit_unit'] = unit_unit[i].value;
                temp1['unit_is_basics_unit'] = unit_is_basics_unit[i].value;
                temp1['unit_base_self_ratio'] = unit_base_self_ratio[i].value;
                temp1['unit_desc'] = unit_desc[i].value;
                temp1['unit_is_sell'] = unit_is_sell[i].checked;
                // 组装价格
                var temp2 = [];
                for (var j = 0; j < unitPrice.eq(i).find('div').length; j++) {
                    var temp3 = {};
                    temp3['type'] = unitPrice.eq(i).find('div').eq(j).find('[name=priceType]').val();
                    temp3['price'] = unitPrice.eq(i).find('div').eq(j).find('[name=price]').val();
                    temp2.push(temp3);
                }
                temp1['priceList'] = JSON.stringify(temp2);

                temp[i] = JSON.stringify(temp1);
            }

            field['unitList'] = temp;

            // 获取图片列表数据
            var imgListData = "";
            $(".image_div_img").each(function () {
                imgListData += this.src;
                imgListData += ":;";
            });
            if (imgListData != undefined) {
                imgListData = imgListData.substring(0, imgListData.length - 2);
                field['pic'] = imgListData;
            }

            // 获取标签的值
            var tags = [];
            $("input:checkbox[name='tag']:checked").each(function (i) {
                tags[i] = $(this).val();
            });
            field.tag = tags.join(":;");

            var index = parent.layer.getFrameIndex(window.name); // 先得到当前iframe层的索引

            // 提交 Ajax 成功后，关闭当前弹层并重载表格
            admin.req({
                type: "post"
                , url: '/admin/commodity-list/save'
                , dataType: "json"
                , cache: false
                , data: field
                , done: function () {
                    parent.layui.table.reload('list'); // 重载表格
                    parent.layer.close(index); // 再执行关闭
                }
            });
        });

        // 主图上传
        upload.render({
            elem: '#addMainPicture'
            , url: '/admin/upload-file/index' // 必填项
            , method: 'post'  // 可选项。HTTP类型，默认post
            , acceptMime: 'image/*'
            , accept: 'images'
            , done: function (res, index, upload) {
                $("#mainPicture").append(
                    '<div class="image_div">\n' +
                    '                <image class="image_div_img" src="' + res.data.file + '"></image>\n' +
                    '                <i class="layui-icon layui-icon-close image_div_close_icon"></i>\n' +
                    '            </div>'
                );
            }
        });

        // 删除图片
        $("body").on('click', '.image_div_close_icon', function () {
            $(this).parent().remove();
        });

        var unitTable = $("#unitTable");
        // 添加单位
        $("body").on('click', '[name=addUnit]', function () {
            unitTable.append(
                '<tr class="table_tr">\n' +
                '                    <td class="table_td">\n' +
                '                        <image src="/admin/imgs/add.png" class="table_operation_add" name="addUnit"></image>\n' +
                '                        <image src="/admin/imgs/sub.png" class="table_operation_sub" name="subUnit"></image>\n' +
                '                    </td>\n' +
                '<td class="table_td">\n' +
                '                        <div class="table_td_div2">\n' +
                '<input type="hidden" value="0" name="unit_is_basics_unit">' +
                '                            <span>辅助单位1</span>\n' +
                '                            <div class="table_td_div2_div">\n' +
                '                                <div class="table_td_div2_div_div">\n' +
                '                                    <select name="unit_unit">\n' +
                unitListElementText +
                '                                    </select>\n' +
                '                                </div>\n' +
                '                                <div>=</div>\n' +
                '                                <div class="table_td_div2_div_div2"><input type="number" name="unit_base_self_ratio" class="layui-input"/></div>\n' +
                '                                <div name="selectedBasicUnit">' + basicUnitText + '</div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </td>' +
                '                    <td class="table_td">\n' +
                '                        <textarea name="unit_desc" placeholder="长度<20个字" class="layui-textarea"></textarea>\n' +
                '                    </td>\n' +
                '                    <td class="table_td">\n' +
                '                        <input name="in_price" placeholder="进货价" class="layui-input" value="0" style="width: 100px;">\n' +
                '                    </td>\n' +
                '                    <td class="table_td">\n' +
                '                        <input name="price2" placeholder="市场价" class="layui-input" value="0" style="width: 100px;">\n' +
                '                    </td>\n' +
                '                    <td class="table_td" name="unitPrice">\n' +
                customTypeElementText +
                '                    </td>\n' +
                '                    <td class="table_td">\n' +
                '                        <input type="checkbox" name="unit_is_sell" lay-skin="switch" checked>\n' +
                '                    </td>\n' +
                '                </tr>'
            );
            form.render();
        })
        // 删除单位
        $("body").on('click', '[name=subUnit]', function () {
            $(this).parent().parent().remove();
            form.render();
        });

        // 可选单位查询
        var unitList;
        var unitListElementText;
        var basicUnitText;
        admin.req({
            type: "get"
            , url: '/admin/config/unit-list'
            , dataType: "json"
            , cache: false
            , done: function (res) {
                unitList = res.data;

                basicUnitText = unitList[0];
                unitListElementText += '<option value="' + unitList[0] + '">' + unitList[0] + '</option>';
                for (var i = 1; i < unitList.length; i++) {
                    unitListElementText += '<option value="' + unitList[i] + '">' + unitList[i] + '</option>';
                }
                $("[name=unit_unit]").append(unitListElementText);
                form.render();
            }
        });

        // 可选客户类型查询
        var customTypeList;
        var customTypeElementText = "";
        admin.req({
            type: "get"
            , url: '/admin/config/find-custom-type'
            , dataType: "json"
            , cache: false
            , done: function (res) {
                customTypeList = res.data;

                for (var i = 0; i < customTypeList.length; i++) {
                    customTypeElementText += '<div class="unit_price_div"><input type="hidden" name="priceType" value="' + customTypeList[i].id + '"><span class="unit_price_div_text">' + customTypeList[i].name + '</span><input type="text" name="price" style="width:100px" autocomplete="off" value="0" class="layui-input"></div>'
                }
                $("[name=unitPrice]").append(customTypeElementText);
                form.render();
            }
        });

        form.on('select(basicUnit)', function (data) {
            basicUnitText = data.value;
            $("[name=selectedBasicUnit]").each(function () {
                $(this).html(basicUnitText);
            })
        });

        var catIns1 = selectN({
            elem: '#purchase_ids'
            , name: 'purchase_ids'
            , search: [false, true]
            , verify: 'required'
            , data: '/admin/purchase/purchase-type'
            , selected: [1]
        });

    })
</script>