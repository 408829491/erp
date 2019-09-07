	'use strict';
	
	var TYPE_CONF = {}, ID = 0, CDS={};
	setConf();
	
	CDS.GLOB_EDITOR = {
			CONSTS:{
				DFT_FONT_FAMILY:'微软雅黑',
				DFT_FONT_SIZE:12,
				DFT_FONT_COLOR:'#000'
			},
			selected: null,
			sql_field: {
				base_field: [],
				goods_field: []
			}
	};
	
	$(function(){
		
	    
		$('body').bind("selectstart",function(){return false;});
	    
		$('#rightHtml').load(TYPE_CONF.PATH.GET_FIELD+'?t='+Date.parse(new Date()),'',function(){
			
			$('.tplDesignRight .tplCateTitle').find('span').click(function(){
				$(this).parent().next().slideToggle();
				if($(this).hasClass('ui-icon-triangle-1-s')){
					$(this).removeClass('ui-icon-triangle-1-s');
					$(this).addClass('ui-icon-triangle-1-e');
				} else {
					$(this).addClass('ui-icon-triangle-1-s');
					$(this).removeClass('ui-icon-triangle-1-e');
				}
			})
			
			$( ".tplItemWrap input[type='checkbox']" ).checkboxradio();
			
			$( ".oneCate" ).draggable({ axis: "x", handle: ".tplCateTitle" });
			
			$("#rightHtml .tplItemWrap:not([pre='goods']) input[type='checkbox']").change(function(){
				addItem(this,'base');
		    });
			
		    $("#rightHtml .tplItemWrap[pre='goods'] input[type='checkbox']").change(function(){
				addItem(this,'goods');
		    });
		    $('.cus_define #add_words,#line_h,#line_s,#rect,#add_barcode_cus,#add_empty_column,#add_image').click(function(){
		    	var that=$(this),_id=that.attr("id");
				var img_count=$(".paper img").length;
				if(_id=="add_image"){
					if(img_count>=5){
						w2alert("服务器资源有限：最多添加5张图片");
						return false;
					}
				}
				addItem(this,'cus_define');
		    })
		    
		    set_height_by_js();
		    $(window).resize(function(){ set_height_by_js() });
		    
		});
		
		if(ID && ID >0){
			setData(ID);
		}
	    
	    var font_family_list = ['Microsoft YaHei', '黑体', '宋体', '楷体'];
	    var font_family_select = $("input[id='font-family']").w2field('select', { items: font_family_list }).on('change', function () {
	    });
	    
	    $('#color').click(function(){
	    	$('#color').w2color({ color: 'EA9899', transparent: true }, function (color) {
	    		color = '#'+ color;
	            $('#color').css('background',color);
		    	$('.a_field.selected').css('color',color);
		    	if($.inArray($('.a_field.selected').attr('field'),['rect']) !=-1){
		    		$('.a_field.selected').css('border-color',color);
				}
				if($('.a_field.selected').hasClass('a_goods_field')){
					$('.a_field.selected').siblings('.a_field').css('color',color);
				}
	        });
	    });
	    
	    $( "#font-size,#letter-spacing,#line-height" ).spinner({
	    	
	        spin: function( event, ui ) {
	        	var max = 100; var min = 5;
		    	switch($(this).attr('id')){
		    	case 'font-size':
		    		max = 100; min = 10;
		    		break;
		    	case 'line-height':
		    		max = 100; min = 10;
		    		break;
		    	case 'letter-spacing':
		    		max = 20; min = 0;
		    		break;
		    	}
		    	if ( ui.value > max ) {
	                $( this ).spinner( "value", min );
	                return false;
	            } else if ( ui.value < min ) {
	                $( this ).spinner( "value", max );
	                return false;
	            }
	        }
	    });
		//禁止设计器缩放
	    /*$(".paper").draggable().resizable().resize(function(){
	    	var scale = $('#scale').width();
	    	$("input[name='page_width']").val($(this).width()/scale);
	    	$("input[name='page_height']").val($(this).height()/scale);
	    	$("#goods_container_wrap").width($(this).width()-2);
	    });*/
	    
	    $('#top_setting_arg #text-align input').checkboxradio({ icon: false });
	    
	    $('.w2float').w2field('float', { autoFormat: false });
	    
	    makeHtml();
	    
	    showTip();
	    
		define_field_attr();
		
		set_field_attr();
		
		CDS.GLOB_EDITOR.doData();
		
		bindEvent();
	})
	
	
	
    var a_div = '';
	
	function addItem(obj, from){
		var objJq = $(obj); 
		var pre = objJq.parent().attr('pre');
		if(from=='goods'){pre='goods'}
		var field = objJq.attr('name');
		var type = objJq.attr('_type');
		
		if(!pre || !field || !from){
			w2alert('调试参数缺失!');
			console.log(pre+'|',field+'|',from);
			return;
		}
		
		if(from=='cus_define'){
			
			if(field == 'tx'){
				var append_place = '.paper';
				a_div =
					"<div field='tx' type='"+type+"' pre='"+pre+"' class='a_field a_base_field'>"+
					objJq.prev().val()+
					"</div>";
				doAddItem(objJq, a_div, append_place);
				$(".a_field").click(function(){
					var ta=$(this);
					if(ta.attr("type")!="undefined"&&ta.attr("type")=="tx"){
						$(".cus_define #words").val(ta.prop('firstChild').nodeValue);
						$("#setText").show();
					}else{
						$("#setText").hide();
					}
				});

			}else if(field=="image"){
				var _id=objJq.attr("_id");
				if($("#"+_id).val()==""){
					w2alert("先上传图片才能添加");
					return false;
				}
				 var append_place = '.paper';
				 a_div =
				 "<div field='image' type='"+type+"' pre='"+pre+"' class='a_field a_base_field'>"+
				"<img src='"+$("#"+_id).val()+"' style='width:100%;height:100%;'/>"+
				 "</div>";
				 doAddItem(objJq, a_div, append_place);


			}else if(field == 'line_h' || field == 'line_s'){
				var line_type = objJq.parent().find('#line_type').val();
				var line_weight = objJq.parent().find('#line_weight').val();
				
				var append_place = '.paper';
				var line_style='';
				
				if(field == 'line_h'){
					line_style = "height: "+(line_weight+5)+"px; width:150px; border-top:"+line_weight+"px "+line_type+" #000";
				}
				
				if(field == 'line_s'){
					line_style = "width: "+(line_weight+5)+"px; height:150px; border-left:"+line_weight+"px "+line_type+" #000";
				}
				
				a_div="<div type='"+type+"' pre='"+pre+"' class='a_field a_base_field a_field_line a_field_"+field+"' field='"+field+
						"' style='"+line_style+";top:10px;left:10px'></div>";//;margin-top:5px;margin-left:5px
				doAddItem(objJq, a_div, append_place);
			}
			
			else if(field == 'rect'){
				var append_place = '.paper';
				var rect_type = objJq.parent().find('#rect_type').val();
				var rect_weight = objJq.parent().find('#rect_weight').val();
				var rect_style = "border:"+rect_weight+"px "+rect_type+" #000";
				a_div =
					"<div type='"+type+"' pre='"+pre+"' class='a_field a_base_field a_field_rect' field='rect' style='"+rect_style+"'>"+
					"</div>";
				doAddItem(objJq, a_div, append_place);
			}
			
			else if(field == 'empty_column'){
				if(!$('#goods_container_wrap').html()){
	    			goodsTool.creatGoodsContainer();
	    		}
	    		goodsTool.creatGoodsItem(objJq.prev(), field);
			}
			
			else if(field == 'barcode_cus'){
				var append_place = '.paper';
				a_div =
					"<div field='barcode_user_defined' type='"+type+"' pre='"+pre+"' class='a_field a_base_field'>"+
					objJq.prev().val()+
					"</div>";
				doAddItem(objJq, a_div, append_place);
			};
			
			return;
		}
		
		var field_in_paper = $('.paper').find("[pre='"+pre+"'][field='"+field+"']");
		var field_in_paper_html = field_in_paper.html();
		
		if(obj.checked==true) {
			if(!field_in_paper_html){
				var append_place = '.paper';
				switch(from){
				
		    	case 'base':
		    		
		    		append_place = '.paper';
		    		a_div = 
						"<div type='"+type+"' pre='"+pre+"' class='a_field a_base_field' field='"+field+"'>"+
						objJq.prev().text()+
						"</div>";
		    		doAddItem(objJq, a_div, append_place);

		    		break;
		    	
		    	case 'goods':
		    		if(!$('#goods_container_wrap').html()){
		    			goodsTool.creatGoodsContainer();
		    		}
		    		goodsTool.creatGoodsItem(objJq.prev(), field);
		    		break;
		    	}
			}
		} else if(field_in_paper_html){
			$(field_in_paper).remove();
		}
		return;
	}
	function doAddItem(objJq, a_div, append_place){
		
		var width = '180px';
		$(a_div)
		.draggable({ containment: append_place, revert: "valid" })
		.resizable({ containment: append_place, revert: "valid" })
		.css({width: width, position: 'absolute', fontFamily:CDS.GLOB_EDITOR.CONSTS.DEFAULT_FONT_FAMILY})
		.appendTo(append_place);
		bindEvent();

	}
	
	
	CDS.GLOB_EDITOR.doData = function(){
		
		
		$('#tb_editorH5_toolbar1_item_deleteSelected').click(function(){
			var sed = $('.paper').find('.a_field.selected');
			if(sed.html()){
				sed.remove();
				
				$('#rightHtml div[pre="'+sed.attr('pre')+'"] #'+sed.attr('field')).click();
				if(sed.attr('pre') == 'goods'){
					$('#rightHtml div[pre="'+sed.attr('pre')+'"] #goods-'+sed.attr('field')).click();
				}
			} else{
				w2alert('需要选择一个打印项进行删除');
			}
		})
		
		
		$('#tb_editorH5_toolbar1_item_preview').click(function(){
			var P2=getLodop(document.getElementById('LODOP_OB2'),document.getElementById('LODOP_EM2'));
					
			P2.PRINT_INITA(4,10,665,600,"打印控件功能演示_Lodop功能_显示模式");
			P2.ADD_PRINT_TEXTA('a',83,78,75,20,"姓名");
			P2.ADD_PRINT_TEXT(134,90,238,35,"地址");
			
			P2.SET_SHOW_MODE("LANGUAGE",0);
			
			P2.PREVIEW();
			return false;
		})
		
		
		$('#tb_editorH5_toolbar1_item_saveData').click(function(){
			
			var tpl_data = CDS.GLOB_EDITOR.serilize();
			if(tpl_data.length < 1){
				w2alert('模板内容为空！');
				return;
			}
			var img_count=$(".paper img").length;
			if(img_count>5){
				//$("#img_layer").hide();
				$("#img_file").val("");
				w2alert("服务器资源有限：最多上传5张图片");
				return false;
			}
			tpl_data = JSON.stringify(tpl_data);
			var other_data = getOtherData();
			if(!other_data) return;
				$("#form_show_header").val($("#show_header").val());
				$("#form_show_sign").val($("#show_sign").val());
				$("#form_type").val(TYPE_CONF.TYPE);
				$("#form_tpl_data").val(tpl_data);
				$("#form_id").val(ID ? ID : 0);
				$("#form_tpl_style").val($("#prt_tpl_style").val());
				$("#form_is_merge").val($("#prt_tpl_is_merge").val());
				$("#form_row_length").val($("#row_length").val());
				$("#form_is_show_border").val($("#is_show_border").val()?$("#is_show_border").val():1);
				$("#form_is_show_page_num").val($("#is_show_page_num").val()?$("#is_show_page_num").val():1);
				$("#form_is_fill_height").val($("#is_fill_height").val()?$("#is_fill_height").val():0);
				$("#form_is_show_goods_header").val($("#is_show_goods_header").val()?$("#is_show_goods_header").val():1);
				$("#form_other_data").val(JSON.stringify(other_data));
				$("#form_base_field").val(CDS.GLOB_EDITOR.sql_field.base_field.toString());
				$("#form_goods_field").val(CDS.GLOB_EDITOR.sql_field.goods_field.toString());
				$("#form_extral").val("cross");
				$("#form_edit").attr("action",TYPE_CONF.PATH.SAVE_DATA);
				$("#form_edit").submit();

			/*$.ajax({
		        type: "get",
		        url: TYPE_CONF.PATH.SAVE_DATA,
		        dataType: 'json',
		        data: {
		        	type: TYPE_CONF.TYPE,
		        	tpl_data:tpl_data,
		        	id: ID ? ID : 0,
		        	other_data: other_data,
		        	base_field: CDS.GLOB_EDITOR.sql_field.base_field.toString(),
		        	goods_field: CDS.GLOB_EDITOR.sql_field.goods_field.toString()
		        },
		        success: function(result)
		        {
		        	w2alert('保存成功!');
		        	console.log('service return result:',result);
		        },
		        error: function(response, textStatus)
		        {
		        	w2alert('服务器错误!');
		        }
		    });*/
		})
	}
	
	CDS.GLOB_EDITOR.serilize = function() {
		
        var v = [];
        //item
        $('.paper').find('.a_base_field').each(function() {
        	var a = {};
            var t = $(this);
            
            a.field = t.attr('field');
            a.pre = t.attr('pre');
            if(t.attr('type') !='undefined'){
            	a.type = t.attr('type');
            }
            a.style = CDS.GLOB_EDITOR.css2json(t, a.field);
            
            
            if($.inArray(a.field,['line_h','line_s','rect']) != -1){
            	a.title = 'shape';
            }else{
            	a.title = t.text();
            	CDS.GLOB_EDITOR.sql_field.base_field.push(a.field);
            }
            
            if (a.field == 'image') {
                a.src = t.find('img').attr('src');
            }
            
            v.push(a);
        });
        
        //注意：必须将它生成的内容push到最后[原因见public_printer的cutTpl方法] | 陈端生
        if($('#goods_container').html()){
            var g = {title: '', field: 'goods', style: CDS.GLOB_EDITOR.css2json($('#goods_container_wrap'), ''), items: []};
            
            g.style.border_color = document.getElementById('goods_container').style.borderColor;
            var x = 0;
            $('.paper #goods_container').find('.a_goods_field').each(function() {
                
                var t = $(this);
                if(t.height() < 20) t.height('20');
                
                var a = {
                		title: t.text(),
                		field: t.attr('field'),
                		style: CDS.GLOB_EDITOR.css2json(t)
                };
                
                g.items[x++] = a;
                CDS.GLOB_EDITOR.sql_field.goods_field.push(a.field);//.replace('g-','')
            });
            v.push(g);
        }
        return v;
    };
    
    
	CDS.GLOB_EDITOR.css2json = function(o, field) {
        var s = {};
        
        s.top = pI(o.css('top') || 0);
        s.left = pI(o.css('left') || 0);
        
        if($.inArray(field,['line_h','line_s','rect']) != -1){
        	var bs = '_'+o.css('border-style');
        	if(bs.indexOf("solid") > 0){
        		s.border_style = 0
        	}else if(bs.indexOf("dotted") > 0){
        		s.border_style = 2;
        	}
        	s.border_width = pI(o.css('border-width'));
        	s.border_color = o.css('border-color');
        	
        	if ( field == 'line_h' ) {
            	s.width = pI(o.css('width'));
            }
            else if (field == 'line_s') {
            	s.height = pI(o.css('height'));
            	s.border_width = pI(o.css('border-left-width'));
            }
            else if ( field == 'rect' ) {
            	s.width = pI(o.css('width'));
            	s.height = pI(o.css('height'));
            }
        }
        else{
        	
            s.width = pI(o.css('width'));
            s.height = pI(o.css('height'));
            
            s.font_weight = o.css('font-weight');
            s.font_size = pI(o.css('font-size'));
            s.font_family = o.css('font-family').replace(/"/g, '').replace(/'/g, '');
            
            s.font_style = o.css('font-style') || 'normal';
            s.color = o.css('color');
            
            s.line_height = pI(o.css('line-height'));
            s.letter_spacing = pI(o.css('letter-spacing')) || 0;
            s.text_align = o.css('text-align') || 'left';
            s.text_decoration = o.css('text-decoration') || '';
        }
        
        return s;
    }
    
	
	function setData(id) {
		$.ajax({
	        type: "get",
	        url: TYPE_CONF.PATH.GET_ONE,
	        dataType: 'json',
	        data: {id: id},
	        success: function(result)
	        {
	        	var data = result.data;
				var is_show_temp_code=data.is_show_temp_code;
				var is_open_discount=data.is_open_discount;
				var bill_open_discount=data.bill_open_discount;
				var open_user_float_price=data.open_user_float_price;
				$("#show_header").val(data.is_show_header);
				$("#prt_tpl_style").val(data.tpl_style);
				$("#prt_tpl_is_merge").val(data.is_merge);
				$("#row_length").val(data.row_length);
				$("#is_show_border").val(data.is_show_border);
				$("#is_show_page_num").val(data.is_show_page_num);
				$("#is_fill_height").val(data.is_fill_height);
				$("#is_show_goods_header").val(data.is_show_goods_header);
				$("#show_sign").val(data.is_show_sign);
	        	var tpl_data = JSON.parse(data.tpl_data);
				var tpl_type=data.type;
				if(tpl_type=='PICK'){
					if(is_show_temp_code>0){
						//是分捡单 且开启了临时编码 才显示
						$("#sort_code").show();
						$("#sort_code_label").show();
					}else{
						$("#sort_code").hide();
						$("#sort_code_label").hide();
					}
				}else if(tpl_type=='ORDER'){
					$("#sort_code").hide();
					$("#sort_code_label").hide();
					$("#org_price_col").hide();
					$("#protocol_org_price").hide();
					$("#protocol_discount_col").hide();
					$("#protocol_discount").hide();
					$("#price_float_col").hide();
					$("#price_float_rate").hide();
					if(is_show_temp_code>0){
						$("#sort_code").show();
						$("#sort_code_label").show();
					}
					if(is_open_discount>0){
						$("#org_price_col").show();
						$("#protocol_org_price").show();
						$("#protocol_discount_col").show();
						$("#protocol_discount").show();
					}
					if(open_user_float_price>0){
						$("#price_float_col").show();
						$("#price_float_rate").show();
					}
				}else if(tpl_type=='SOA'){
					$("#protocol_org_price_col").hide();
					$("#protocol_org_price").hide();
					$("#protocol_discount_desc_col").hide();
					$("#protocol_discount_desc").hide();
					if(bill_open_discount){
						$("#protocol_org_price_col").show();
						$("#protocol_org_price").show();
						$("#protocol_discount_desc_col").show();
						$("#protocol_discount_desc").show();
					}
				}
	        	setOtherData(data);
	        	
	        	for (var i in tpl_data){
	        		
	        		var val = tpl_data[i];
	        		var field = val.field;
	        		var type = val.type;
	        		var pre = val.pre;
	        		var style = val.style;
	        		
	        		var s = printCommon.json2css({style:val.style, field:field});
	        		
	        		var append_place = '.paper';
	        		
	    			if(field == 'line_h' || field == 'line_s'){
	    				a_div =
	    					"<div type="+type+" pre="+pre+" class='a_field a_base_field a_field_line a_field_"+field+"' field='"+field+"' style='"+s+"'>"+
	    					"</div>";
	    				doSetData(a_div, append_place);
	    					
	    			}else if(field == 'rect'){
	    				a_div =
	    					"<div type="+type+" pre="+pre+" class='a_field a_base_field a_field_rect' field='"+field+"' style='"+s+"'>"+
	    					"</div>";
	    				doSetData(a_div, append_place);
	    					
	    			}else if(field == 'goods'){
	    				goodsTool.creatGoodsContainer();
	    				$('#goods_container_wrap').attr('style', s + "").css('border-color',val.style.borderColor);
	    				if (val.items) {
	                        $.each(val.items, function(i, it) {
	                            var is = printCommon.json2css({style:it.style});
	                            
	                            a_div = $("<li type="+type+" pre="+pre+" class='a_field a_goods_field' field='" + it.field + "' style='" + is + "'>" + 
	                            		it.title + 
	                            		"</li>"
	                            		);
	                            goodsTool.creatGoodsItem(a_div, it.field, it);
	                            $('#rightHtml div[pre="goods"] input[name="'+it.field+'"]').click();
	                        });
	                    }
	    			}else if(field=='image'){
						a_div =
							"<div type="+type+" pre="+pre+" class='a_field a_base_field' field='"+field+"' style='"+s+"'>"+
							"<img src='"+val.src+"' style='border；0px;width:100%;height:100%;'>"
							"</div>";
						doSetData(a_div, append_place);
	    			}else{
	    				a_div =
	    					"<div type="+type+" pre="+pre+" class='a_field a_base_field' field='"+field+"' style='"+s+"'>"+
	    					val.title+
	    					"</div>";
	    				doSetData(a_div, append_place);
	    					
	    			};
	    			
	    			if(field != 'goods'){
	    				$('#rightHtml div[pre="'+pre+'"] input#'+field).click();
	    			}
	        	}
				$(".a_field").click(function(){
					var ta=$(this);
					if(ta.attr("type")!="undefined"&&ta.attr("type")=="tx"){
						$(".cus_define #words").val(ta.prop ('firstChild').nodeValue);
						$("#setText").show();
					}else{
						$("#setText").hide();
					}
				});
	        },
	        error: function(response, textStatus)
	        {
	        	console.log(response);
	        	alert('服务器错误3!');
	        }
	    });
	};
	function doSetData(a_div, append_place){
		$(a_div)
		.draggable({ containment: append_place, revert: "valid" })
		.resizable({ containment: append_place, revert: "valid" })
		.css({position: 'absolute'})
		.appendTo(append_place);
	}
	
	function CreatePage() {
		P=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));  
		
		LODOP.SET_LICENSES("","332D3C455C6A2E47D6E0C067886DDEF9","C94CEE276DB2187AE6B65D56B3FC2848","");
		P.PRINT_INITA(0,0,736,578,'cds设计');
		P.SET_SHOW_MODE("LANGUAGE",0);
	};
	
	function makeHtml(){
		
		var printer_list = ['George Washington', 'John Adams'];
	    $('#printer_list').w2field('list', { items: printer_list });
	    
	    var paper_list = ['George Washington', 'John Adams'];
	    $('#paper_list').w2field('list', { items: paper_list });
	    
	    var printer_border = ['George W', 'John Adams'];
	    $('#printer_border').w2field('list', { items: printer_border });
	    
	    var print_direct = ['George', 'John Adams'];
	    $('#print_direct').w2field('list', { items: print_direct });
	    
	    var font_family_list = ['微软雅黑', '黑体', '宋体', '楷体'];
	    var maker_line_select = $("input[id='font-family']").w2field('list', { items: font_family_list }).on('change', function () {
	    });
	    
	    $('#editorH5_toolbar1').w2toolbar({
	        name: 'editorH5_toolbar1',
	        items: [
		        { type: 'html',  id: 'saveData',
                    html: function (item) {
                        var html =
                          '<button type="button" id="form-submit" class="btn btn-primary btn-xs" style="display:none;"><span class="glyphicon glyphicon-star"></span> 保存</button>';
                        return html;
                    }
                },
	            //{ type: 'button', id: 'preview', text: '预览', icon: 'glyphicon glyphicon-eye-open' },
                /*暂时屏蔽后续开发 | 陈端生
	            { type: 'html',  id: 'preview',
                    html: function (item) {
                        var html =
                          '<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-eye-open"></span> 预览</button>';
                        return html;
                    }
                },*/
	            { type: 'html',  id: 'deleteSelected',
                    html: function (item) {
                        var html =
                          '<button type="button" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> 删除选中项</button>';
                        return html;
                    }
                }
	        ],
	        onClick: function (event) {
	        }
	    });
	}
	
	function showTip(){
		
		$("input[id='font-family']").parent().mouseover(function(){
	    	$(this).w2tag('【字体类型】 调整选中项的字体类型', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
	    
		$("input[id='color']").parent().mouseover(function(){
	    	$(this).w2tag('【字体颜色 或 线条颜色】 调整文本选中项的字体颜色 或 方框选中项的线条颜色', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("input[id='font-size']").parent().parent().mouseover(function(){
	    	$(this).w2tag('【字体大小 或 线条粗细】 调整文本选中项的字体大小 或 直线和方框选中项的线条粗细', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='font-weight']").mouseover(function(){
	    	$(this).w2tag('【字体加粗】 调整选中项的字体加粗', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='text-decoration']").mouseover(function(){
	    	$(this).w2tag('【字体下划线】 调整选中项的字体下划线', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='font-style']").mouseover(function(){
	    	$(this).w2tag('【字体倾斜】 调整选中项的字体倾斜', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='text-align'][value='left']").mouseover(function(){
	    	$(this).w2tag('【文字向左靠齐】 调整选中项的文字内容向左靠齐', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='text-align'][value='center']").mouseover(function(){
	    	$(this).w2tag('【文字向中靠齐】 调整选中项的文字内容向中靠齐', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("button[id='text-align'][value='right']").mouseover(function(){
	    	$(this).w2tag('【文字向右靠齐】 调整选中项的文字内容向右靠齐', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("input[id='letter-spacing']").parent().parent().mouseover(function(){
	    	$(this).w2tag('【字间距】 调整选中项的字与字之间的距离', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("input[id='line-height']").parent().parent().mouseover(function(){
	    	$(this).w2tag('【行间距】 调整选中项的行与行之间的距离', { position: 'top' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("input[name='page_width']").mouseover(function(){
	    	$(this).w2tag('请量取纸张宽多少毫米<br/>[输入数字]', { position: 'bottom' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    }).change(function(){
	    	$('.paper,#goods_container_wrap').width($(this).val()+'mm')
	    });
		
		$("input[name='page_height']").mouseover(function(){
	    	$(this).w2tag('请量取纸张高多少毫米<br/>[输入数字]', { position: 'bottom' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    }).change(function(){
	    	$('.paper').height($(this).val()+'mm')
	    });
		
		$("input[name='page_top']").mouseover(function(){
	    	$(this).w2tag('在纸张顶部留出一定<br/>高度的空白不打印<br/>[输入数字]', { position: 'bottom' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("input[name='page_left']").mouseover(function(){
	    	$(this).w2tag('在纸张左右<br/>两侧边留出<br/>一定宽度的<br/>空白不打印<br/>[输入数字]', { position: 'bottom' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$("select[name='print_direct']").mouseover(function(){
	    	$(this).w2tag('用途: 通过调整打印内容在打印纸张上的排版方向,从而使得纸张的高宽和打印内容的高宽更加吻合', { position: 'left' });
	    }).mouseout(function(){
	    	$(this).w2tag();
	    });
		
		$(document).on("mouseover", ".paper .a_base_field", function(){
        	$(this).w2tag('点击选中后，<br/>按上下左右方向键(←↑→)可移动, 也可以直接用鼠标拖动<br/>按del键可删除,也可点击"删除选中项"键删除', { position: 'top' });
        }).on("mouseout", ".a_base_field", function(){
        	$(this).w2tag('', { position: 'top' });
        }).on("mousedown", ".a_base_field", function(){
        	$(this).w2tag();
        	$(document).on("mouseover", ".a_base_field", function(){
            	$(this).w2tag();
            })
        })
	}
	
	function define_field_attr(){
		$(document).on('mousedown','.paper .a_field',function(){
			$('.paper .selected').removeClass('selected');
			$(this).addClass('selected');
			CDS.GLOB_EDITOR.selected = $(this);
			
			var s = getAFiledStyle(this);
			if($.inArray($(this).attr('field'),['rect', 'line_h', 'line_s']) !=-1){
				s.color = $(this).css('border-color');
				s.font_size = pI($(this).css('border-width'));
			}
			$('.field_maker#font-family').val(s.font_family||CDS.GLOB_EDITOR.CONSTS.DEFAULT_FONT_FAMILY);
			$('.field_maker#color').css('background', s.color);
			$('.field_maker#font-size').val(s.font_size);
			$('.field_maker#letter-spacing').val(s.letter_spacing);
			$('.field_maker#line-height').val(s.line_height);
		});
	}
	
	function set_field_attr(){
		$('.field_maker#font-family,#font-size,#letter-spacing,#line-height').change(function(){
			var a_str = 'px';
			if($(this).attr('id') == 'font-family'){
				a_str = '';
			}
			$('.a_field.selected').css($(this).attr('id'),$(this).val() + a_str);
			
			if($('.a_field.selected').hasClass('a_goods_field')){
				$('.a_field.selected').siblings('.a_field').css($(this).attr('id'),$(this).val() + a_str);
			}
		})
		$('button.field_maker').click(function(){
			var that=$(this);
			console.log(that.attr('id'));
			console.log(that.attr('value'));
			var t=new RegExp(that.attr('value'),'g');
			if( t.test($('.a_field.selected').css(that.attr('id')))){
				console.log(1);
				$('.a_field.selected').css(that.attr('id'),'');
				
				if($('.a_field.selected').hasClass('a_goods_field')){
					$('.a_field.selected').siblings('.a_field').css(that.attr('id'),'');
				}
			}else{
				$('.a_field.selected').css(that.attr('id'),that.attr('value'));
				
				if($('.a_field.selected').hasClass('a_goods_field')){
					$('.a_field.selected').siblings('.a_field').css(that.attr('id'),that.attr('value'));
				}
			}
		})
		$('.ui-spinner-button').click(function(){
			$('.a_field.selected').css($(this).siblings('input').attr('id'),$(this).siblings('input').val()+'px');
			
			if($('.a_field.selected').hasClass('a_goods_field')){
				$('.a_field.selected').siblings('.a_field').css($(this).siblings('input').attr('id'),$(this).siblings('input').val()+'px');
			}
			if($.inArray($('.a_field.selected').attr('field'),['rect', 'line_h', 'line_s']) !=-1
					&& $(this).siblings('input').attr('id') == 'font-size'){
	    		$('.a_field.selected').css('border-width',$(this).siblings('input').val());
			}
		});
	}
	
	
	function getAFiledStyle(obj){
		var o = $(obj);
		return {
			font_family: o.css('font-family'),
			color: o.css('color'),
			font_size: pI(o.css('font-size')),
			letter_spacing: pI(o.css('letter-spacing')),
			line_height: pI(o.css('line-height'))
		};
	}
	
	function set_height_by_js(){
		if($('.tplDesignRight').height()>$(window).height()){
			$('.tplDesignRight').height($(window).height());
			$('.tplDesignRight').css({'overflow-y':'auto','overflow-x':'hidden'});
			setTimeout("$('.oneCate').css('width','99%')",100);
		} else if($('.tplDesignRight').height()<$(window).height()){
			$('.tplDesignRight').height('auto');
			$('.tplDesignRight').attr('style','');
			setTimeout("$('.oneCate').css('width','98.5%')",100);
		}
		
		
	    $('.work_place_wrap').height($(window).height()-80);
	    $(window).resize(function(){
	    	$('.work_place_wrap').height($(window).height()-80);
	    })
	    var this_tow_margin = $('.work_place_wrap').height() - $('.paper').height();
	    if(this_tow_margin>0){
	    	$('.paper').css('top',this_tow_margin/2)
	    }else{
	    	$('.paper').css('top','5px')
	    }
	    $('.paper').fadeIn();
	}
	
	
	function getOtherData(){
		
		var right_data = {
				name:  	 $("input[name='name']").val(),
				print_direct:$("select[name='print_direct']").val(),
				page_width:  $("input[name='page_width']").val(),
				page_height: $("input[name='page_height']").val(),
				page_top:    $("input[name='page_top']").val(),
				page_left:   $("input[name='page_left']").val()
		}
		
		if(!right_data.name){
			w2alert('模板名字不能为空');{
				$("input[name='name']").focus();
				return;
			}
		}
		if(!right_data.page_width){
			w2alert('纸张宽度不能为空');{
				$("input[name='page_width']").focus();
				return;
			}		
		}
		if(!right_data.page_height){
			w2alert('纸张高度不能为空');{
				$("input[name='page_height']").focus();
				return;
			}
		}
		
		return right_data;
	}
	
	function setOtherData(r){
		$("input[name='name']").val(r.name);
		$("select[name='print_direct']").val(r.print_direct);
		$("input[name='page_width']").val(r.page_width); $('.paper').width(r.page_width+'mm');
		$("input[name='page_height']").val(r.page_height); $('.paper').height(r.page_height+'mm');
		$("input[name='page_top']").val(r.page_top);
		$("input[name='page_left']").val(r.page_left);
	}
	
	function bindEvent(){
		
        $("body").bind('keydown', function(e) {
            var k = e.keyCode || e.which;
            if($.inArray(k,[37,38,39,40,46]) !=-1){
            	event.preventDefault();
            } else{
            	return;
            }
            
            var sed = CDS.GLOB_EDITOR.selected;
            
            if(!sed.hasClass("a_base_field")){
            	if(k == 46) $('#tb_editorH5_toolbar1_item_deleteSelected').click();
            	return;
            }
            
			var a = pI(sed.css("left").replace('px','')) - 1;
			var b = pI(sed.css("top").replace('px','')) - 1;
			var c = pI(sed.css("left").replace('px','')) + 1;
			var d = pI(sed.css("top").replace('px','')) + 1;
			switch (k){
			case 37:
				sed.css('left', a + 'px');
				break;
			case 38:
				sed.css('top', b + 'px');
				break;
			case 39:
				sed.css('left', c + 'px');
				break;
			case 40:
				sed.css('top', d + 'px');
				break;
			case 46:
				 $('#tb_editorH5_toolbar1_item_deleteSelected').click();
				break;
			}
            sed = null;
        });
	}
	
	
	/*公共函数开始-----*/
	
	
	var goodsTool={};
	
	var pI = function (s) {
		var r = parseInt(s, 10);
		return parseInt(s, 10) || 0;  
	};
	
	goodsTool.creatGoodsContainer = function (){
		$("<div id='goods_container_wrap'></div>")
		.draggable({ containment: ".paper"})
		.disableSelection()
		.css({position: 'absolute'})
		.appendTo('.paper');
		
		$( "<ul id='goods_container'></ul>" )
		.height('100%')
		.sortable({revert: true})
		.disableSelection()
		.droppable()
	    .appendTo('#goods_container_wrap');
	}
	
	goodsTool.creatGoodsItem = function(o, field, it){
		var type = o.attr('_type') || o.next().attr('_type');
		var style = null;
		var text = null;
		if(field == 'empty_column'){
			console.log(it);
			text = o.val() || (it&&it.title!=undefined?it.title:"");
		} else{
			text = o.text();
		}
		if(it){
			style = it.style;
		}else{
			style = 'width: 80px; height:23px';
		}
		
		a_div = 
			"<li type='"+type+"' pre='goods' class='a_field a_goods_field' field='"+field+"' style='"+printCommon.json2css({style:style,is_item:true})+"'><span>"+
			text+
			"</span></li>";
		var append_place = '#goods_container';
		$(a_div)
		.resizable({ containment: ".paper"})
		.css({width: o.width(), height: $('#goods_container').height(), position: 'relative'})
		.resize(function(){
			$(this).siblings('.a_field').css({height:$(this).height()+2})
			$(this).parent().css({height: $(this).height()+4}).parent().css({height: $(this).height()+6})
		})
		.appendTo(append_place);
	}
	
	
	function setConf(){
		TYPE_CONF = JSON.parse(getQueryString('TYPE_CONF').replace(/\'/g,'"'));
		ID = getQueryString('id');
	}
	