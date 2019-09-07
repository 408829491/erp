/*auther:陈端生15111222285; createtime:2017-05-25*/
/*打印和打印模板 的 公共方法*/

//声明为全局变量
var printCommon = {}; 
var cds={
		DFT_ITEM_WIDTH: 20,
		DFT_ITEM_HEIGHT: 20,
		DFT_ITEM_FONTFAMILY: '微软雅黑',
		DFT_ITEM_FONTSIZE: 12,
		DFT_ITEM_LINEHEIGHT: 16,
};

//需根据环境是打印还是编辑进行优化|陈端生
printCommon={
		init: function(){
			$('body').after('<div id="get_scale" style="width:1000mm;display:none;"></div>');
			cds.WIDTH_SCALE = $('#get_scale').width()/1000;
		},
		json2css: function(obj) {
			if(obj.is_item==true){
				return printCommon.json2css_item(obj);
			}
			var o = obj.style, field=obj.field||null;
		    var s = 'position: absolute; display: block;';
			s  += 'top:' + (o.top || 0) + 'px;'
		        + 'left:' + (o.left || 0) + 'px;';
			
			if($.inArray(field,['line_h','line_s','rect']) != -1){
				
				if(o.border_style==0){
		    		o.border_style = 'solid';
		    	}else if(o.border_style==2){
		    		o.border_style = 'dotted';
		    	}
				
				if ( field == 'line_h' )
		        {
		        	s += 'width:' + (o.width || cds.DFT_ITEM_WIDTH) + 'px;';
		        	s += "border-top:" + o.border_width + 'px ' + o.border_style + ' ' + o.border_color + ';';
		        	
		        }
		        else if ( field == 'line_s' )
		        {
		        	s += 'height:' + (o.height || cds.DFT_ITEM_HEIGHT) + 'px;';
		        	s += "border-left:" + o.border_width + 'px ' + o.border_style + ' ' + o.border_color + ';';
		        }
		        else if ( field == 'rect' )
		        {
		        	s += 'width:' + (o.width || cds.DFT_ITEM_WIDTH) + 'px;';
		        	s += 'height:' + (o.height || cds.DFT_ITEM_HEIGHT) + 'px;';
		        	s += "border:" + o.border_width + 'px ' + o.border_style + ' ' + o.border_color + ';';
		        }
			}
			
		    else
		    {

				s += 'width:' + (o.width || cds.DFT_ITEM_WIDTH) + 'px;'
		        + 'height:' + (o.height || cds.DFT_ITEM_HEIGHT) + 'px;'
		        + 'font-weight:' + (o.font_weight || 'normal') + ';'
		        + 'font-size:' + (o.font_size || cds.DFT_ITEM_FONTSIZE) + 'px;'
		        + 'font-family:' + (o.font_family || cds.DFT_ITEM_FONTFAMILY) + ' ' + ';'
		        + 'font-style:' + (o.font_style || 'normal') + ';'
		        + 'color:' + (o.color || cds.DFT_ITEM_COLOR) + ';'
		        + 'line-height:' + (o.line_height || cds.DFT_ITEM_LINEHEIGHT) + 'px;'
		        + 'letter-spacing:' + (o.letter_spacing || 0) + 'px;'
		        + 'text-align:' + (o.text_align || 'center')+ ';'
		        + 'text-decoration:' + (o.text_decoration || 'none')+ ';';
		    }
		    return s;
		},
		json2css_item: function(obj){
			var o = obj.style, field=obj.field||null, s='';
			if(obj.field == 'table'){
			} else{
				s += 'width:' + (o.width || cds.DFT_ITEM_WIDTH) + 'px;';
			}
	    	s += 'height:' + (o.height || cds.DFT_ITEM_HEIGHT) + 'px;'
	        + 'font-weight:' + (o.font_weight || 'normal') + ';'
	        + 'font-size:' + (o.font_size || cds.DFT_ITEM_FONTSIZE) + 'px;'
	        + 'font-family:' + (o.font_family || cds.DFT_ITEM_FONTFAMILY) + '' + ';'
	        + 'font-style:' + (o.font_style || 'normal') + ';'
	        + 'color:' + (o.color || cds.DFT_ITEM_COLOR) + ';'
	        + 'line-height:' + (o.line_height || cds.DFT_ITEM_LINEHEIGHT) + 'px;'
	        + 'letter-spacing:' + (o.letter_spacing || 0) + 'px;'
	        + 'text-align:' + (o.text_align || 'center')+ ';'
	        + 'text-decoration:' + (o.text_decoration || 'left')+ ';';
	    	return s;
		},
	    get_scale: function(){
	    	$('body').after('<div id="get_scale" style="width:1000mm;display:none;"></div>');
	    	var scale = $('#get_scale').width()/1000;
	    	return scale;
	    }
}

printCommon.init();


var  b=a=function(a){
	return w2utils.base64decode(w2utils.base64decode(a));
}

/*转换大写*/
function changeMoneyToChinese(money){  
    var cnNums = new Array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"); //汉字的数字  
    var cnIntRadice = new Array("","拾","佰","仟"); //基本单位  
    var cnIntUnits = new Array("","万","亿","兆"); //对应整数部分扩展单位  
    var cnDecUnits = new Array("角","分","毫","厘"); //对应小数部分单位  
    //var cnInteger = "整"; //整数金额时后面跟的字符  
    var cnIntLast = "元"; //整型完以后的单位  
    var maxNum = 999999999999999.9999; //最大处理的数字  
      
    var IntegerNum; //金额整数部分  
    var DecimalNum; //金额小数部分  
    var ChineseStr=""; //输出的中文金额字符串  
    var parts; //分离金额后用的数组，预定义  
    if( money == "" ){  
        return "";  
    }  
    money = parseFloat(money);  
    if( money >= maxNum ){  
        $.alert('超出最大处理数字');  
        return "";  
    }  
    if( money == 0 ){  
        //ChineseStr = cnNums[0]+cnIntLast+cnInteger;  
        ChineseStr = cnNums[0]+cnIntLast  
        //document.getElementById("show").value=ChineseStr;  
        return ChineseStr;  
    }  
    money = money.toString(); //转换为字符串  
    if( money.indexOf(".") == -1 ){  
        IntegerNum = money;  
        DecimalNum = '';  
    }else{  
        parts = money.split(".");  
        IntegerNum = parts[0];  
        DecimalNum = parts[1].substr(0,4);  
    }  
    if( parseInt(IntegerNum,10) > 0 ){//获取整型部分转换  
        zeroCount = 0;  
        IntLen = IntegerNum.length;  
        for( i=0;i<IntLen;i++ ){  
            n = IntegerNum.substr(i,1);  
            p = IntLen - i - 1;  
            q = p / 4;  
            m = p % 4;  
            if( n == "0" ){  
                zeroCount++;  
            }else{  
                if( zeroCount > 0 ){  
                    ChineseStr += cnNums[0];  
                }  
                zeroCount = 0; //归零  
                ChineseStr += cnNums[parseInt(n)]+cnIntRadice[m];  
            }  
            if( m==0 && zeroCount<4 ){  
                ChineseStr += cnIntUnits[q];  
            }  
        }  
        ChineseStr += cnIntLast;  
        //整型部分处理完毕  
    }  
    if( DecimalNum!= '' ){//小数部分  
        decLen = DecimalNum.length;  
        for( i=0; i<decLen; i++ ){  
            n = DecimalNum.substr(i,1);  
            if( n != '0' ){  
                ChineseStr += cnNums[Number(n)]+cnDecUnits[i];  
            }  
        }  
    }  
    if( ChineseStr == '' ){  
        //ChineseStr += cnNums[0]+cnIntLast+cnInteger;  
        ChineseStr += cnNums[0]+cnIntLast;  
    }/* else if( DecimalNum == '' ){ 
        ChineseStr += cnInteger; 
        ChineseStr += cnInteger; 
    } */  
    return ChineseStr;  
}  