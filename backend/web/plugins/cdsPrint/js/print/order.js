/* auther:陈端生 | 15111222285 ; createtime:2017-05-25 
 * 订单打印
 * 该文件为中间适配层【介于应用实现层 和 底层通用打印封装包之间】
 * 作用是将数据获取并处理之后， 再传送给底层通用打印封装包
 * */

'use strict'

var P={}, isPrev=false, GLOB_CURRENT_ORDER=[], GLOB_TIMELY=true;

$(function(){
    //GLOB_TPLS = print.getTpl();
})

$(document).on("click", ".tpl_print_order,.tpl_print_view_order", function(){
    switch ($(this).hasClass('tpl_print_order')){
        case true:
            order_print.startPrint({clickObj:$(this)});
            break;
        case false:
            order_print.preview({clickObj:$(this)});
            break;
    }

});


var order_print = {
    init(){
        order_print.createPage();
    },
    createPage(){
        P=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
        if (!(P.webskt && P.webskt.readyState==1)){
            //setTimeout('order_print.createPage()', 500);
            return;
        }
        P.SET_SHOW_MODE("LANGUAGE",0);
    },
    initData(){
    },
    preview(obj){
        isPrev = true;
        order_print.start(obj);
    },
    startPrint(obj){
        isPrev = false;
        order_print.start(obj);
    },
    start(obj){
        order_print.initData();
        var tpl={}, machine_index = 0;
        machine_index = localStorage['orderPrinter'];

        if(!machine_index){
            alert('请选择打印机');
            window.location.href='/admin/print/print-setting';
            return;
        }
        var id = obj.order_id ? obj.order_id : obj.clickObj.closest('[data-id]').attr('data-id');
        var reg = /,$/gi;
        id = id.replace(reg,'');

        order_print.getRemoteData({
            idArr: [id],
            is_merge:obj.is_merge!=undefined?obj.is_merge:1,
            machine_index: machine_index,
            isPrev: isPrev,
            after_func: order_print.afterPrint
        });
    },
    dataFactory(data,is_merge){

        var order = data.order;
        var item = data.order.details;
        var user = data.user;
        var shop = data.shop;
        order.email          = order.nick_name || '';
        order.cus_name       = order.nick_name || '';
        order.account_tel    = order.receive_tel || '';
        order.address_detail = order.address_detail || '';
        order.seller_name    = user.seller_name || '';
        order.seller_mobile  = user.seller_mobile || '';
        order.delivery_time  = order.receive_name || '';
        order.receive_time   = order.delivery_time_detail || '';
        order.stopgap_code   = shop.stopgap_code||'';
        order.name           = shop.name || '';
        order.driver         = order.driver_name || '';
        order.driver_mobile  = shop.driver_mobile || '';
        order.shop_name      = order.nick_name||shop.name|| '';
        console.log(order)
        return [data.order]
    },
    cloneObj(obj){
        var str, newobj = obj.constructor === Array ? [] : {};
        if (typeof obj !== 'object') {
            return;
        } else if (window.JSON) {
            str = JSON.stringify(obj),
                newobj = JSON.parse(str);
        } else {
            for (var i in obj) {
                newobj[i] = typeof obj[i] === 'object' ? this.cloneObj(obj[i]) : obj[i];
            }
        }
        return newobj;
    },
    getRemoteData(obj){
        var idArr = obj.idArr;
        $.ajax({
            type: "get",
            url: "/admin/order/get-print-data",
            async: false,
            dataType: 'json',
            data: {
                id: idArr.toString(),
                is_merge:obj.is_merge
            },
            success: function(result)
            {
                var data = result.data;
                var records = order_print.dataFactory(data,obj.is_merge);
                console.log(records);
                if(records){
                        var GLOB_TPLS = order_print.getTpl();
                        if(!GLOB_TPLS){
                            alert('此单对应用户所设置模板不存在或没有设置默认模板');
                            return;
                        }
                        obj.tpl = GLOB_TPLS.INCLU_ITEM;
                        obj.records= records;
                        delete(obj.idArr);
                        public_printer.main(obj);

                }
            },
            error: function(response, textStatus)
            {
                alert('服务器错误 | 打印订单时!');
            }
        });
    },
    getTpl(param=null){

        var re = '';
        var data = { type: 'ORDER' };
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
                alert('服务器错误 | 获取订单打印模板时!');
            }
        });
        return re;
    },
	/*打印后执行 | 回调函数*/
    afterPrint(){
        //在这里输入逻辑
    }
}


var printComponents = {
    printSelectedUser(obj){
        var type = obj.type;
        var order_id = '';
        var reg = /,$/gi;
        if(type=='select'){
            var tt=$('.ivu-checkbox-checked:visible',$(obj.clickObj));
        }else if(type=='all'){
            var tt=$('.ivu-checkbox:visible',obj.clickObj);
        }

        tt.each(function(){
            var t = $(this);
            var tr_order_id = t.parents('tr').find('[order_id]').attr('order_id');
            if(tr_order_id){
                tr_order_id = tr_order_id.replace(reg,'');
                order_id += tr_order_id + ',';
            }
        });

        order_id = order_id.replace(reg,'');
        if(order_id==""){
            alert('请勾选需要打印的订单');
            return;
        }
        var data={order_id:order_id};
        if(obj.is_merge!=undefined){
            data.is_merge=obj.is_merge;
        }
        order_print.startPrint(data);
    },
    printTotal(obj){

        $.ajax({
            type: "get",
            url: '/superAdmin/line/totalPrintData',
            data: {line_id:obj.line_id,delivery_date:obj.delivery_date},
            async: false,
            dataType: 'json',
            success: function(result)
            {
                if(result.status==0){
                    alert(result.message);
                    return false;
                }
                var total_price=0;
                var data = result.data;
                var main_data=data.main;
                var detail_data=data.detail;

                var html='<table width="97%" style="WORD-BREAK: break-all;font-size:16px;" align="center"> <tbody> <tr> <td align="center" colspan="9"><b><font size="3">{{company_name}}购物清单</font></b></td> </tr> <tr style="font-size:18px;"> <td colspan="3">线路:{{line_name}}</td> <td colspan="3">司机：{{driver_name}}</td> <td colspan="3">发货日期:{{delivery_date}}</td> </tr> <tr> <td width="5%" height="30" align="left"><b><font size="3">序号</font></b></td> <td width="15%" height="30" align="left"><b><font size="3">名称</font></b></td> <td width="10%" height="30" align="left"><b><font size="3">客户编码</font></b></td> <td width="15%" height="30" align="left"><b><font size="3">地址</font></b></td> <td width="10%" height="30" align="left"><b><font size="3">号码</font></b></td> <td width="5%" height="30" align="center"><b><font size="3">小计</font></b></td> <td width="10%" height="30" align="center"><b><font size="3">签字</font></b></td> <td width="10%" height="30" align="center"><b><font size="3">备注</font></b></td> </tr>';
                for(var i in main_data){
                    if(main_data[i]==null){
                        html=html.replace("{{"+i+"}}","");
                        continue;
                    }
                    html=html.replace("{{"+i+"}}",main_data[i]);
                }
                for(var j in detail_data) {
                    html += '<tr> <td width="5%" height="30" align="left"><font size="2">{{index}}</font></td> <td width="15%" height="30" align="left"><font size="2">{{user_name}}</font></td> <td width="10%" height="30" align="left"><font size="2">{{user_code}}</font></td> <td width="15%" height="30" align="left"><font size="2">{{address_detail}}</font></td> <td width="10%" height="30" align="left"><font size="2">{{receive_tel}}</font></td> <td width="5%" height="30" align="center"><font size="2">{{sub_price}}</font></td> <td width="10%" height="30" align="center"><font size="2"></font></td> <td width="10%" height="30" align="center"><font size="2"></font></td> </tr>';
                    total_price+=parseFloat(detail_data[j].sub_price);
                    for(var k in detail_data[j]){
                        html=html.replace("{{"+k+"}}",detail_data[j][k]);
                    }
                }
                total_price=Number(total_price).toFixed(2);
                html +='<tr> <td align="left" colspan="9" style="border-top: 2px dashed #000"><b><font size="3">总金额：¥'+total_price+'</font></b> </td> </tr> </tbody> </table>';
                order_print.init();
                P.PRINT_INIT("发货单打印"+(Math.random()*100000000));
                P.ADD_PRINT_HTM(10,1,"100%","90%","<!DOCTYPE>"+html);
                P.PREVIEW();

            },
            error: function(response, textStatus)
            {
                alert('服务器错误 | 获取打印数据失败');
            }
        });
    }

}