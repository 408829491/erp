/*
 * 订单打印
 * 该文件为中间适配层【介于应用实现层 和 底层通用打印封装包之间】
 * 作用是将数据获取并处理之后， 再传送给底层通用打印封装包
 * */

'use strict'

var P={}, isPrev=false, GLOB_TPLS=new Object(), GLOB_CURRENT_ORDER=[], GLOB_TIMELY=true;

$(function(){
    //GLOB_TPLS = pur_print.getTpl();
})

$(document).on("click", ".tpl_print_pur,.tpl_print_view_pur", function(){

    switch ($(this).hasClass('tpl_print_pur')){
        case true:
            pur_print.startPrint({clickObj:$(this)});
            break;
        case false:
            pur_print.preview({clickObj:$(this)});
            break;
    }

});


var pur_print = {
    init(){
        pur_print.createPage();
    },
    createPage(){
        P=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
        if (!(P.webskt && P.webskt.readyState==1)){
            //setTimeout('pur_print.createPage()', 500);
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
        pur_print.start(obj);
    },
    startPrint(obj){
        isPrev = false;
        pur_print.start(obj);
    },
    start(obj){
        pur_print.initData();
        var tpl={}, machine_index = 0;
        machine_index = localStorage['purchasePrinter'];

        if(!machine_index){
            alert('请选择打印机');
            window.location.href='/admin/print/print-setting';
            return;
        }
        var id = obj.purchase_id ? obj.purchase_id : obj.clickObj.closest('[data-id]').attr('data-id');
        var reg = /,$/gi;
        id = id.replace(reg,'');
        pur_print.getRemoteData({
            idArr: [id],
            machine_index: machine_index,
            isPrev: isPrev,
            after_func: pur_print.afterPrint
        });
    },
    dataFactory(data){
        var item = data.item;
        var v = data.order;
        var date = new Date();
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentDate = date.getFullYear() + "-" + month + "-" + strDate
            + " " + date.getHours() + ":" + (date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes()) + ":" + date.getSeconds();
        v.print_time=currentDate;
        v.plan_date=v.plan_date==null?'':v.plan_date;
        v.send_date=v.send_date==null?'':v.send_date;
        v.actual_total_pay_str_capital = changeMoneyToChinese(v.purchase_price);
        for(var i in item){
            if(!v.item){
                v.item = new Array();
            }
            item[i].all_need=item[i].purchase_num;//待采购数量
            item[i].un_take_num=item[i].unreceive;//未采购数量
            item[i].all_actual=item[i].num;//实际采购数量
            item[i].goods_remark=item[i].remark;//备注
            item[i].all_order=item[i].purchase_id;//采购单ID
            item[i].all_existing=item[i].stock;//库存
            item[i].need_purchase_price=item[i].purchase_price;//采购金额
            item[i].purchase_price=item[i].purchase_price;//采购金额
            item[i].in_price=item[i].purchase_price;//采购金额
            item[i].commodity_pay=item[i].total_price;//采购总金额
            item[i].goods_purchase_price=2;
            v.item.push(item[i]);
        }
        console.log(data.order);
        return [data.order];
    },
    getRemoteData(obj){
        var idArr = obj.idArr;
        var type=0;
        if($("#printPrice").prop("checked")){
          type=1;
        }
        $.ajax({
            type: "get",
            url: "/admin/purchase/get-print-data?type="+type,
            async: false,
            dataType: 'json',
            data: {
                id: idArr.toString()
            },
            success: function(result)
            {
                console.log(result);
                var data = result.data;
                var records = pur_print.dataFactory(data);

                //GLOB_TIMELY：（boolean）是实时获取还是性能优先，[ture：实时, false:性能优先]。
                if(GLOB_TIMELY== true){
                    var param = {};
                    GLOB_TPLS = pur_print.getTpl(param);
                }
                if(!GLOB_TPLS){
                    alert('此单对应用户所设置模板不存在或没有设置默认模板');
                    return;
                }
                obj.tpl = GLOB_TPLS.INCLU_ITEM;
                obj.records = records;
                delete(obj.idArr);

                console.log(obj);
                public_printer.main(obj);
            },
            error: function(response, textStatus)
            {
                alert('服务器错误');
            }
        });
    },
    getTpl(param=null){
        var re = '';
        var data = { type: 'PUR' };
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
                if(re.length<1) {
                    alert('没有设置采购单打印模板,点击确定前往设置。');
                    window.location.href='/superAdmin/view#/printConfig';
                }
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