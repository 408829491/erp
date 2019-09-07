'use strict'

var P={}, isPrev=false, GLOB_TPLS=new Object(), GLOB_CURRENT_ORDER=[], GLOB_TIMELY=true;

$(function(){
	//GLOB_TPLS = print.getTpl();
})

$(document).on("click", ".tpl_print_summary,.tpl_print_view_summary", function(){
	switch ($(this).hasClass('tpl_print_summary')){
	case true:
		summary_print.startPrint({clickObj:$(this)});
		break;
	case false:
		summary_print.preview({clickObj:$(this)});
		break;
	}
	
});


var summary_print = {
		init(){
			summary_print.createPage();
		},
		createPage(){
			P=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
			if (!(P.webskt && P.webskt.readyState==1)){
				//setTimeout('summary_print.createPage()', 500);
				return;
			}
			P.SET_SHOW_MODE("LANGUAGE",0);
		},
		initData(){
			GLOB_CURRENT_ORDER=[];
			if(group) group= null;
		},
		preview(obj){
			isPrev = true;
			summary_print.start(obj);
		},
		startPrint(obj){
			isPrev = false;
			summary_print.start(obj);
		},
		start(obj){
			summary_print.initData();
			var tpl={}, machine_index = 0;
			machine_index = localStorage['summaryPrinter'];
			
			if(!machine_index){
				alert('请选择打印机');
				window.location.href='/admin/print/print-setting';
				return;
			}
			var id = obj.order_id ? obj.order_id : obj.clickObj.closest('[data-id]').attr('data-id');
			var reg = /,$/gi;
			id = id.replace(reg,'');
			summary_print.getRemoteData({
				idArr: [id],
				is_merge:obj.clickObj.attr("is_merge")?obj.clickObj.attr("is_merge"):0,
				machine_index: machine_index,
				isPrev: isPrev,
				after_func: summary_print.afterPrint
			});
		},
		dataFactory(res,is_merge){
			var items=[];
			items[0]=res;
			res.item=res.detail;
			delete res.detail;
			return items;
		},
		getRemoteData(obj){
			var idArr = obj.idArr;
			$.ajax({
		        type: "get",
		        url: "/admin/print/pick-print-data-all",
		        async: false,
		        dataType: 'json',
		        data: {
					is_merge:obj.is_merge,
					order_commodity_id: idArr.toString()
		        },
		        success: function(result)
		        {
		        	var data = result.data;
		        	var records = summary_print.dataFactory(data,obj.is_merge);
		        	
		        	//GLOB_TIMELY：（boolean）是实时获取还是性能优先，[ture：实时, false:性能优先]。
		        	if(GLOB_TIMELY== true){
		        		var param = {};
		        		//param.user_id = data.order.user_id;
		        		GLOB_TPLS = summary_print.getTpl(param);
		        	}
		        	if(!GLOB_TPLS){
		        		alert('此单对应用户所设置模板不存在或没有设置默认模板');
		        		return;
		        	}
		        	obj.tpl = GLOB_TPLS.INCLU_ITEM;
		        	obj.records = records;
		        	delete(obj.idArr);
		        	public_printer.main(obj);
		        },
		        error: function(response, textStatus)
		        {
		        	alert('服务器错误!');
		        }
		    });
		},
		getTpl(param=null){
			
			var re = '';
			var data = { type: 'SUMMARY' };
			if(param){
				if(param.user_id){
					data.user_id = param.user_id;
				}
			}
			$.ajax({
			    type: "get",
			    url: '/admin/print/get-tpl',
			    data: data,
			    async: false,
			    dataType: 'json',
			    success: function(result)
			    {
					if(result.status=='error'){

						alert(result.message);
						return false;
					}
					var data = result.data;
			    	for(var i in data){
			    		data[i].tpl_data = JSON.parse(data[i].tpl_data);
			    	}
			    	re = data;
			    },
			    error: function(response, textStatus)
			    {
			    	alert('服务器错误 | 获取打印模板时!');
			    }
			});
			return re;
		},
		/*打印后执行 | 回调函数*/
		afterPrint(){
			//在这里输入逻辑
		}
}