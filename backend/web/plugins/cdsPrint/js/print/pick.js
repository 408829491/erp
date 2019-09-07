/* auther:陈端生 | 15111222285 ; createtime:2017-05-25 
 * 订单打印
 * 该文件为中间适配层【介于应用实现层 和 底层通用打印封装包之间】
 * 作用是将数据获取并处理之后， 再传送给底层通用打印封装包
 * */

'use strict'

var P = {}, isPrev = false, GLOB_TPLS_PICK = new Object(), GLOB_TIMELY_PICK = true;

$(function () {
    //为了减少不必要的查询 只在分拆 摘果页使用
    //if(/sort/.test(location.href)){
    //	GLOB_TPLS_PICK = pick_print.getTpl();
    //}

})

$(document).on("click", ".tpl_print_pick,.tpl_print_view_pick", function (e) {
    switch ($(this).hasClass('tpl_print_pick')) {
        case true:
            pick_print.startPrint({clickObj: $(this)});
            break;
        case false:
            pick_print.preview({clickObj: $(this)});
            break;
    }

});

var pick_print = {
    init(){
        pick_print.createPage();
    },
    createPage(){
        P = getLodop(document.getElementById('LODOP_OB'), document.getElementById('LODOP_EM'));
        if (!(P.webskt && P.webskt.readyState == 1)) {
            //setTimeout('pick_print.createPage()', 500);
            return;
        }
        P.SET_SHOW_MODE("LANGUAGE", 0);
    },
    initData(){
    },
    preview(obj){
        isPrev = true;
        pick_print.start(obj);
    },
    startPrint(obj){
        isPrev = false;
        pick_print.start(obj);
    },
    start(obj){
        pick_print.initData();
        var tpl = {}, machine_index = 0;
        machine_index = localStorage['pickPrinter'];
        if (!machine_index) {
            alert('请选择打印机');
            window.location.href = '/admin/print/print-setting';
            return;
        }
        var id = obj.order_id ? obj.order_id : obj.clickObj.closest('[data-id]').attr('data-id');
        var is_mulit_pick = obj.clickObj.closest('[data-id]').attr('mulit_pick') ? 1 : 0;
        console.log(is_mulit_pick);
        var reg = /,$/gi;

        pick_print.getDataAndPrint({
            idArr: [id],
            is_mulit_pick: is_mulit_pick,
            clickObj: obj.clickObj,
            machine_index: machine_index,
            isPrev: isPrev,
            after_func: pick_print.afterPrint
        });
    },
    getDataFromThePage(obj){

        var records = [];
        var idArr = obj.idArr;
        var pick_url = "/admin/print/print-data";
        if (obj.is_mulit_pick) {
            pick_url = "/admin/print/print-many-data";
        }
        $.ajax({
            type: "get",
            url: pick_url,
            async: false,
            dataType: 'json',
            data: {
                order_commodity_id: idArr.toString()
            }, success: function (result) {
                    console.log(result);
                var datas = result.data;
                for (var j in datas) {
                    var data = result.data[j];
                    var record = {};
                    record.no = 'P' + (parseInt(j) + 101001001);
                    record.email = data['nick_name'];                                                 //店铺
                    record.shop_name = data['line_name'];                                         		//线路
                    record.user_code = data['user_name'];                                               	//用户编码
                    record.goods_name = data['commodity_name'];                                         //商品名称
                    record.amount_info = data['num'];//带单位的分拣数量
                    record.actual_amount = data['actual_num'];                                          //分拣数量
                    record.unit = data['unit'];                                  				//单位
                    record.date = data['delivery_date'];                                         			//送货日期
                    record.origin_url = '';
                    record.order_commodity_code = data['commodity_id'];                        //分拣条码
                    record.goods_remark = data['remark'];                                                     //商品备注
                    record.summary = data['notice'];                                                     //商品说明
                    record.sort_code = data['sort_code'];
                    record.sort_name = data['sort_name'];
                    record.sort_time = data['sort_time'];
                    record.durability_period = 1;
                    record.mfg_date = '';
                    record.stopgap_code = '';
                    records.push(record);
                }
            }, error: function (response, textStatus) {
                alert('服务器错误 | 打印订单时!');
                console.log(response);
            }
        });

        console.log(records);
        return records;
    },
    getDataAndPrint(obj){

        //GLOB_TIMELY_PICK：（boolean）是实时获取还是性能优先，[ture：实时, false:性能优先]。
        if (GLOB_TIMELY_PICK == true) {
            GLOB_TPLS_PICK = pick_print.getTpl({});
        }
        if (!GLOB_TPLS_PICK) {
            alert('此单对应用户所设置模板不存在或没有设置默认模板');
            return;
        }

        obj.tpl = GLOB_TPLS_PICK.INCLU_ITEM;
        obj.records = pick_print.getDataFromThePage(obj);

        delete(obj.idArr);
        delete(obj.clickObj);

        console.log(obj);
        public_printer.main(obj);

    },
    getTpl(param=null){

        var re = '';
        var data = {type: 'PICK'};
        $.ajax({
            type: "get",
            url: '/admin/print/get-tpl',
            data: data,
            async: false,
            dataType: 'json',
            success: function (result) {
                if (result.status == 'error') {
                    alert(result.message);
                    return false;
                }
                var data = result.data;
                for (var i in data) {
                    data[i].tpl_data = JSON.parse(data[i].tpl_data);
                }
                re = data;
            },
            error: function (response, textStatus) {
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

/*  仅仅是兼容补丁 | 无需理会下面这些代码   */
var createPage = function () {
    P = getLodop(document.getElementById('LODOP_OB'), document.getElementById('LODOP_EM'));
    if (!(P.webskt && P.webskt.readyState == 1)) {
        setTimeout('pick_print.createPage()', 500);
        return;
    }
    P.SET_SHOW_MODE("LANGUAGE", 0);
}
if (!print) {
    var print = {
        createPage: createPage
    }
} else {
    print.createPage = createPage
}