
<!-- auther:陈端生 15111222285 | 476210724@qq.com; createtime:2017-05-25 -->
<!-- 说明：打印设计页面 -->

<!DOCTYPE html>
<html>
<head>
    <title>打印模板设计</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="../plugins/cdsPrint/common/js/print/pFuncs.js"></script>
    <script src="../plugins/cdsPrint/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="../plugins/cdsPrint/ui/main.1.5.js" defer="defer"></script>
    <link rel="stylesheet" type="text/css" href="../plugins/cdsPrint/ui/main.1.5.css" />
    <script type="text/javascript" src="../plugins/cdsPrint/common/js/common.js"></script>

    <link rel="stylesheet" href="../plugins/cdsPrint/common/css/common.css" />
    <link rel="stylesheet" type="text/css" href="../plugins/cdsPrint/ui/main.1.5.change.css" />
    <script type="text/javascript" src="../plugins/cdsPrint/ui/jui/jui-1.12.1.min.js"></script>
    <link rel="stylesheet" href="../plugins/cdsPrint/ui/jui/jui-1.12.1.min.css" />
    <link rel="stylesheet" href="../plugins/cdsPrint/css/bootstrap.min.css">
    <link rel="stylesheet" href="../plugins/cdsPrint/app/setting/printTpl/printEditorH5.css" />
    <script type="text/javascript" src="../plugins/cdsPrint/js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="../plugins/cdsPrint/common/js/print/printCommon.js"></script>
    <script type="text/javascript" src="../plugins/cdsPrint/app/setting/printTpl/printEditorH5.js"></script>
    <style>
        html, body, .h100 {
            height: 100%;
        }
        .scroll {
            overflow: auto;
        }
    </style>
</head>
<body>
<div class='tplDesignRight h100' style="padding:10px;">
    <div class="scroll h100">
        <div class='oneCate' style='margin-top: 0px;'>
            <div class='tplCateTitle'><span class='ui-icon ui-icon-triangle-1-s'></span>打印设置</div>
            <div class='tplItemWrap' style="display: block;">
                <div class='tplItemRow'>
                    <span>打印样式配置:</span>
                    <select id='prt_tpl_style' onchange="change_style(this)" class='w2ui-select w2field w2ui-input'/>
                    <option value="0">默认样式</option>
                    <option value="1">双列样式</option>
                    <option value="2">分类汇总样式</option>
                    </select>
                </div>
                <div class='tplItemRow' id="pre_row">
                    <span>每页显示行数:</span><input id="row_length" type='text' maxlength="3" class='w2ui-input w2field' style='width:40px'/>
                </div>
                <div class='tplItemRow' style="display: none;">
                    <span>是否合并打印:</span>
                    <select id='prt_tpl_is_merge' class='w2ui-select w2field w2ui-input'/>
                    <option value='1'>是</option>
                    </select>
                </div>
            </div>
            <div class='tplItemWrap'>
                <div class='tplItemRow'>
                    <span>是否显示边框:</span>
                    <select id='is_show_border' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
                <div class='tplItemRow'>
                    <span>是否每页底部显示分页栏信息:</span>
                    <select id='is_show_page_num' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
            </div>
            <div class='tplItemWrap'>
                <div class='tplItemRow'>
                    <span>是否每页都显示商品列头:</span>
                    <select id='is_show_goods_header' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
                <div class='tplItemRow' id="is_fill_height_info" style="display: none;">
                    <span>是否每页填充剩余行数:</span>
                    <select id='is_fill_height' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
            </div>


            <div class='tplItemWrap'>
                <div class='tplItemRow'>
                    <span>是否每页都显示表头:</span>
                    <select id='show_header' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
                <div class='tplItemRow'>
                    <span>是否每页底部显示收货人签名:</span>
                    <select id='show_sign' class='w2ui-select w2field w2ui-input' style='width:39px'/>
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                    </select>
                </div>
            </div>
            <div class='tplItemWrap'>
                <div class='tplItemRow'>
                    <span>模板名称:</span><input name='name' type='text' class='w2ui-input w2field' style='width:218px'/>
                </div>
            </div>
            <div class='tplItemWrap'>
                <div class='tplItemRow'>
                    <span>打印方向:</span>
                    <select name='print_direct' id="print_direct" class='w2ui-select w2field w2ui-input' onchange="change_direct(this);"/>
                    <option value='1'>正向（定宽定高）</option>
                    <option value='2'>横向(定宽定高)</option>
                    <option value='3'>正向(根据打印内容高度自动切纸)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class='oneCate'>
            <div class='tplCateTitle'><span class='ui-icon ui-icon-triangle-1-s'></span>自定义文件</div>
            <div class='tplItemWrap'>
                <div class='tplItemRow'>
				<span>
					<iframe style="width:0px;height: 0px;" name="fileupload"></iframe>
					<form enctype="multipart/form-data" action="//liguoqiang.sdongpo.com/superAdmin/printerConfig/ImportFromFile" method="post" target="fileupload">
						<input type="file" id="file" class="btn-xs" name="tpl"><input type="hidden" id="tid" name="tid">
				<button  type="submit"  class=" btn btn-success btn-xs"><span class="glyphicon glyphicon-ok"></span> 上传文件</button>
				</form>
				</span>

                </div>
            </div>
        </div>

        <div id='rightHtml'></div>

        <div class='oneCate cus_define'>
            <div class='tplCateTitle'><span class='ui-icon ui-icon-triangle-1-s'></span>自定义打印项</div>
            <div class='tplItemWrap'>
                <div class='tplItemRow' pre='cus_define'>
                    <input id='words' placeholder='输入文字内容' type='text'/>
                    <button name='tx' _type='tx' id='add_words' type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-ok"></span> 添加文字</button>
                    <button id="setText"  class="btn btn-info btn-xs" style="display: none;"><span class="glyphicon glyphicon-ok"></span> 修改文字</button>
                </div>

                <div class='tplItemRow' pre='cus_define'>
                    <select id='line_type'>
                        <option value='solid'>实线</option>
                        <option value='dotted'>虚线</option>
                    </select>
                    <span class='input_warp'>线粗<input id='line_weight' value='1' class='field_maker'/>像素</span>

                    <button id='line_h' name='line_h' type="button" _type='shape' class="btn btn-default btn-xs"><span class="glyphicon glyphicon-ok"></span> 添加横线</button>
                </div>

                <div class='tplItemRow' pre='cus_define'>
                    <select id='line_type2'>
                        <option value='solid'>实线</option>
                        <option value='dotted'>虚线</option>
                    </select>
                    <span class='input_warp'>线粗<input id='line_weight2' value='1' style="width:20px;" class='field_maker'/>像素</span>

                    <button id='line_s' name='line_s' type="button" _type='shape'  class="btn btn-default btn-xs"><span class="glyphicon glyphicon-ok"></span> 添加竖线</button>
                </div>

                <div class='tplItemRow' pre='cus_define'>
                    <select id='rect_type'>
                        <option value='solid'>实线</option>
                        <option value='dotted'>虚线</option>
                    </select>
                    <span class='input_warp'>线粗<input id='rect_weight' value='1' class='field_maker'/>像素</span>
                    <button id='rect' name='rect' type="button" _type='shape' class="btn btn-default btn-xs"><span class="glyphicon glyphicon-ok"></span> 添加方框</button>
                </div>

                <div class='tplItemRow' pre='cus_define'>
                    <input id='barcode_cus' placeholder='输入条码值' type='text' style='width: 126px;'/>
                    <button id='add_barcode_cus' name='barcode_cus' type="button" _type='barcode' class="btn btn-default btn-xs"><span class="glyphicon glyphicon-ok"></span> 添加自定义条码</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class='tplDesignLeft'><div>
        <!-- s1ui toolbar -->
        <div id="editorH5_toolbar1" style="border: 1px solid #dfdfdf; border-radius: 3px"></div>
        <div id="editorH5_form" style="display:none;">
            <iframe style="width:0px;height:0px;" name="edit_form"></iframe>
            <form host="localhost" action="" id="form_edit" method="post" target="edit_form">
                <input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" name="_csrf-backend" >
                <input type="hidden" id="form_type" name="type">
                <input type="hidden" id="form_tpl_data" name="tpl_data">
                <input type="hidden" id="form_id" name="id">
                <input type="hidden" id="form_show_header" name="is_show_header">
                <input type="hidden" id="form_show_sign" name="is_show_sign">
                <input type="hidden" id="form_extral" name="extral">
                <input type="hidden" id="form_other_data" name="other_data">
                <input type="hidden" id="form_base_field" name="base_field">
                <input type="hidden" id="form_goods_field" name="goods_field">
                <input type="hidden" id="form_tpl_style" name="tpl_style">
                <input type="hidden" id="form_is_merge" name="is_merge">
                <input type="hidden" id="form_row_length" name="row_length">
                <input type="hidden" id="form_is_show_border" name="is_show_border">
                <input type="hidden" id="form_is_show_page_num" name="is_show_page_num">
                <input type="hidden" id="form_is_fill_height" name="is_fill_height">
                <input type="hidden" id="form_is_show_goods_header" name="is_show_goods_header">
            </form>
        </div>

        <!-- jqui toolbar -->
        <div id='top_setting_arg'>

            <div class='s_setting_item' style='vertcal-align: middle'>

                <div class="input-group s_setting_item">
                    <span class="input-group-addon" id="basic-addon1" style='padding: 0px;'>字体</span>
                    <input id='font-family' class='field_maker' style='width:70px; height: 29px;'/>
                </div>

                <div class="btn-group s_setting_item">
                    <label id="basic-addon2" class="input-group-addon" style='padding: 0px;'>颜色</label>
                    <select class='field_maker' id='color' style='width:28px; height:28px; background: #000; border: 2px solid #aaa; border-radius: 2px; cursor: pointer'></select>
                </div>

                <div class="input-group s_setting_item">
                    <span class="input-group-addon" id="basic-addon3" style='padding: 0px;'>大小</span>
                    <input id='font-size' class='field_maker' value='' style='width: 20px;'>
                </div>


                <div class="btn-group btn-group-sm">
                    <button id='font-weight' value='700' type="button" class="btn btn-default field_maker"><span class="glyphicon glyphicon-bold"></span></button>
                    <button id='text-decoration' value='underline'  type="button" class="btn btn-default field_maker">U</button>
                    <button id='font-style' value='italic'  type="button" class="btn btn-default field_maker"><span class="glyphicon glyphicon-italic"></span></button>
                </div>

                <div class="btn-group btn-group-sm" style='margin-left: 10px;'>
                    <button id="text-align" value='left' type="button" class="btn btn-default field_maker"><span class="glyphicon glyphicon-align-left"></span></button>
                    <button id="text-align" value='center' type="button" class="btn btn-default field_maker"><span class="glyphicon glyphicon-align-center"></span></button>
                    <button id="text-align"  value='right' type="button" class="btn btn-default field_maker"><span class="glyphicon glyphicon-align-right"></span></button>
                </div>

                <div class="input-group s_setting_item">
                    <span class="input-group-addon" id="basic-addon4" style='padding: 0px;'>字间距</span>
                    <input id='letter-spacing' value='' class='field_maker' style='width: 20px;'>
                </div>

                <div class="input-group s_setting_item">
                    <span class="input-group-addon" id="basic-addon5" style='padding: 0px;'>行高</span>
                    <input id='line-height' value='' class='field_maker' style='width: 20px;'>
                </div>

            </div>
        </div>

        <div class='work_place_wrap' style='overflow-y:scroll;'>
            <div class='work_place'>
                <div class='paper' style='display:none'>
                </div>
            </div>
        </div>
    </div></div>

<div id='scale' style='display: none; width: 1mm'></div>
<!-- <div id='conf' style='display: none; width: 1mm'></div> -->
<script>
    function getLocalInfo(key){
        if(window.localStorage){
            return localStorage.getItem(key);
        }else{
            alert("读取出错，请联系管理员");
            return false;
        }
    }
    function change_direct(obj){
        if(parseInt($(obj).val())==3){
            $("#row_length").val(0);
            $("#is_fill_height").val(0);
            $("#pre_row").hide();
            $("#is_fill_height_info").hide();
        }else{
            $("#pre_row").show();
            $("#is_fill_height_info").show();
        }
    }
    function change_style(obj){
        if(parseInt($(obj).val())==0){
            $("#pre_row").show();
            $("#is_fill_height_info").show();
        }else{

            $("#row_length").val(0);
            $("#is_fill_height").val(0);
            $("#pre_row").hide();
            $("#is_fill_height_info").hide();
        }
    }
    function setLocalInfo(key,value){
        if(window.localStorage){
            localStorage.setItem(key,value);
        }else{
            alert("保存出错，请联系管理员");
            return false;
        }
    }
    $(function(){
        var is_show_header=getLocalInfo("show_header");
        if(is_show_header){
            $("#show_header").val(is_show_header);
        }
        $("#save_show_header").click(function(){
            setLocalInfo("show_header",$.trim($("#show_header").val()));
            w2alert("设置成功");
        });
        $("#tid").val(location.search);
        setTimeout(function(){
            change_style('#prt_tpl_style');
            change_direct('#print_direct');},"2200");

    });
    function do_pars(a){
        console.log(a);
        w2alert("上传成功");
        $("#add_image").show();
        var url=decodeURIComponent(a.url);
        $("#image_file").val("");
        $("#img_url").val(url);
    }

</script>

</body>
</html>