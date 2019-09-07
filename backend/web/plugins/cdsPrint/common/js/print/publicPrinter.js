/* 实际打印底层支持 [打印框架采用分层设计] 。可以将多行数据(数组)依据模板生成打印效果 并传递给打印控件 */

var public_printer = pr = {}, group = null;

/* [生成打印任务:流程控制] - [打印|打印预览] 
 * @param machine_index: (int or string) 打印机
 * @param isPrev: (boolean) 是否预览
 * @param tpl: (json) 模板数据
 * @param records: (json) 需要打印的数据
 * @param after_func: (json) 回调函数-打印后需要执行的方法
 * 
 * FOR EXAMPLE:
 * {machine_index: "Microsoft XPS Document Writer", isPrev: true, tpl: Object, records: Array(1), after_func: function}
 * */
pr.main = function (obj) {
    var records = obj.records, tpl = obj.tpl, machine_index = obj.machine_index, isPrev = obj.isPrev;
    if (!machine_index) {
        if (isPrev == true) w2alert('请选择打印机！');
        return;
    }
    if (!records || !tpl || !machine_index) {
        console.log('流程参数错误：records, tpl, machine_index', records, tpl, machine_index);
        alert('打印流程参数错误');
        return;
    }
    if (!isPrev == true && obj.after_func) obj.after_func(tpl.type);
	var hasHtml = false;
    var one_batch =30;
    var l = Math.floor(records.length / one_batch);
    var i = 0;
    var j = 0;

       pr.createPage();
       P.PRINT_INIT("模板打印"+(Math.random()*10000));
       P.PRINT_INITA(tpl.page_top + 'mm', tpl.page_left + 'mm', tpl.page_width + 'mm', tpl.page_height + 'mm', "cds");
       P.SET_PRINTER_INDEX(machine_index);

       var page_width = tpl.page_width;
       var page_height = tpl.page_height;
       if (tpl.print_direct == 2) {
           page_width = tpl.page_height;
           page_height = tpl.page_width;
       }
    if(tpl.print_direct==3){
        P.SET_PRINT_PAGESIZE(tpl.print_direct, page_width + "mm", "45mm", "");
    }else{
        P.SET_PRINT_PAGESIZE(tpl.print_direct, page_width + "mm", page_height + "mm", "");
    }
       //P.SET_PRINT_MODE("POS_BASEON_PAPER",true);//设置输出位置以纸张边缘为基点
       P.SET_PRINT_MODE("NP_NO_RESULT", true);

       for (var key in records) {
           i = i + 1;
           var record = records[key];
           if (!record.item) {
               public_printer.processNoItem(record, tpl);
           } else if (record.item) {
               if(tpl.tpl_style==0&&tpl.type=='ORDER'&&tpl.row_length>0){
                   var new_record=pr.chunk(record.item,tpl.row_length);
                   for(var nr in new_record ){
                       record.item=new_record[nr];
                       public_printer.processIncluItem(record, tpl, i,(parseInt(nr)+1),new_record.length);
                       P.NEWPAGEA();
                   }
               }else{
                   var nr=1;
                   public_printer.processIncluItem(record, tpl, i,nr,0);
               }

           }
           if(tpl.tpl_style>0||tpl.row_length==0){
               P.NEWPAGEA();
           }
           hasHtml = true;
			P.SET_PRINT_MODE("RESELECT_PRINTER",isPrev);
			P.SET_PRINT_MODE("RESELECT_ORIENT",isPrev);
			P.SET_PRINT_MODE("RESELECT_PAGESIZE",isPrev);
			P.SET_PRINT_MODE("RESELECT_COPIES",isPrev);
           if (i % one_batch == 0 && j < l) {
               j = j + 1;

               P.SET_PRINT_MODE("CUSTOM_TASK_NAME", i);

               pr.end();

               P.PRINT_INIT("模板打印"+(Math.random()*10000));
               P.PRINT_INITA(tpl.page_top + 'mm', tpl.page_left + 'mm', tpl.page_width + 'mm', tpl.page_height + 'mm', "cds");
               P.SET_PRINTER_INDEX(machine_index);
               if(tpl.print_direct==3){
                   P.SET_PRINT_PAGESIZE(tpl.print_direct, page_width + "mm", "45mm", "");
               }else{
                   P.SET_PRINT_PAGESIZE(tpl.print_direct, page_width + "mm", page_height + "mm", "");
               }

               hasHtml = false;

               if (P.blOneByone == true) {
                   $.delay(1);
               }
           }

       }

       if (hasHtml) {
           pr.end();
       }
}
pr.createPage=function(){
    P=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    if (!(P.webskt && P.webskt.readyState==1)){
        setTimeout('pr.createPage()', 500);
        return;
    }
    P.SET_SHOW_MODE("LANGUAGE",0);
}

pr.end = function () {
    isPrev ? P.PREVIEW() : P.PRINT();
}
pr.repeat=function(str,n){
    return new Array(n+1).join(str);
}
pr.groupBy=function groupBy( array , f ) {
    let groups = {};
    array.forEach( function( o ) {
        let group = JSON.stringify( f(o) );
        groups[group] = groups[group] || [];
        groups[group].push( o );
    });
    return groups;
   /* return Object.keys(groups).map( function( group ) {
        return groups[group];
    });*/
}
pr.chunk=function array_chunk (input, size, preserveKeys) {
//  函数出处: http://locutus.io/php/array_chunk/
// original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
// improved by: Brett Zamir (http://brett-zamir.me)
//      note 1: Important note: Per the ECMAScript specification,
//      note 1: objects may not always iterate in a predictable order
//   example 1: array_chunk(['Kevin', 'van', 'Zonneveld'], 2)
//   returns 1: [['Kevin', 'van'], ['Zonneveld']]
//   example 2: array_chunk(['Kevin', 'van', 'Zonneveld'], 2, true)
//   returns 2: [{0:'Kevin', 1:'van'}, {2: 'Zonneveld'}]
//   example 3: array_chunk({1:'Kevin', 2:'van', 3:'Zonneveld'}, 2)
//   returns 3: [['Kevin', 'van'], ['Zonneveld']]
//   example 4: array_chunk({1:'Kevin', 2:'van', 3:'Zonneveld'}, 2, true)
//   returns 4: [{1: 'Kevin', 2: 'van'}, {3: 'Zonneveld'}]

    var x;
    var p = '';
    var i = 0;
    var c = -1;
    var l = input.length || 0;
    var n = [];

    if (size < 1) {
        return null;
    }

    if (Object.prototype.toString.call(input) === '[object Array]') {
        if (preserveKeys) {
            while (i < l) {
                (x = i % size)
                    ? n[c][i] = input[i]
                    : n[++c] = {}; n[c][i] = input[i]
                i++;
            }
        } else {
            while (i < l) {
                (x = i % size)
                    ? n[c][x] = input[i]
                    : n[++c] = [input[i]]
                i++;
            }
        }
    } else {
        if (preserveKeys) {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size)
                        ? n[c][p] = input[p]
                        : n[++c] = {}; n[c][p] = input[p]
                    i++;
                }
            }
        } else {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size)
                        ? n[c][x] = input[p]
                        : n[++c] = [input[p]]
                    i++;
                }
            }
        }
    }

    return n;
}

pr.processIncluItem = function (record, tpl, i,pnum,ptotal) {

    //if (!group) {
        group = pr.cutTpl(JSON.parse(JSON.stringify(tpl)));
    //}
    var show_header =tpl.is_show_header;
    if (show_header > 0) {
        pr.shape(tpl, group.up);
        pr.table2(record, group.table, tpl,pnum,ptotal);
        pr.underTable(record, tpl, group.down);
        pr.html(record, tpl, group.up);
        if(i==1){
            P.SET_PRINT_STYLEA(0, "ItemType", 1);
            P.SET_PRINT_STYLEA(0, "LinkedItem", 1);
        }
    } else {
        //第二页不展示表头
        pr.html(record, tpl, group.up);
        pr.shape(tpl, group.up);
        pr.table(record, group.table, tpl,pnum,ptotal);
        pr.underTable(record, tpl, group.down);
    }
}

pr.processNoItem = function (record, tpl) {

    pr.html(record, tpl);

    pr.shape(tpl);
}

pr.html = function (record, tpl, tpl_data) {
    var tpl_data = tpl_data ? tpl_data : tpl.tpl_data;
    var HTML = $("<div></div>");
      HTML.prop("id", "tpl_head");
    var is_show_header=tpl.is_show_header;
    var BARCODE = [];
    var QRCODE = [];
    var IMAGES = [];
    for (var i in tpl_data) {
        var field = tpl_data[i].field;
        var style = tpl_data[i].style;
        if ($.inArray(field, ['line_h', 'line_s', 'rect']) == -1) {
            var type = tpl_data[i].type;
            if (type == 'tx') {
                var s = printCommon.json2css({style: style, field: field});
                var item =
                    "<div style='" + s + "'>" +
                    tpl_data[i].title +
                    "</div>";
                $(item).appendTo(HTML);
            }else if(type=='image'){
                var img_src=tpl_data[i].src;
                IMAGES.push({src:img_src,image_tpl: tpl_data[i]});
            }else {
                for (var j in record) {
                    if (tpl_data[i].field == j) {
                        if (tpl_data[i].type == 'barcode') {
                            BARCODE.push({
                                barcode_data: record[j],
                                barcode_tpl: tpl_data[i]
                            });
                        }else if(tpl_data[i].type=='qrcode'){
                            if(record[j]){
                                QRCODE.push({
                                    qrcode_data: record[j],
                                    qrcode_tpl: tpl_data[i]
                                });
                            }
                        }else {
                            var s = printCommon.json2css({style: style, field: field});
                            var item =
                                "<div style='" + s + "'>" +
                                record[j] +
                                "</div>";

                            $(item).appendTo(HTML);
                        }
                    }
                }
            }
        }
    }
    P.ADD_PRINT_HTM(0 + "mm", 0 + "mm", "100%", "100%", HTML.html());
    if (BARCODE.length) {
        for (var i in BARCODE) {
            var s = BARCODE[i].barcode_tpl.style;
            P.ADD_PRINT_BARCODE(s.top, s.left, s.width, s.height, "128Auto", BARCODE[i].barcode_data);
        }
    }
    if (QRCODE.length) {
        for (var i in  QRCODE) {
            var s = QRCODE[i].qrcode_tpl.style;
            P.ADD_PRINT_BARCODE(s.top, s.left, s.width, s.height, "QRCode", QRCODE[i].qrcode_data);
        }
    }
    if (IMAGES.length) {
        for (var i in  IMAGES) {
            var s = IMAGES[i].image_tpl.style;

            if(is_show_header>0){
                P.SET_PRINT_STYLEA(0, "ItemType", 1);
                P.SET_PRINT_STYLEA(0, "LinkedItem", 1);
            }
            var item = "<img src='"+IMAGES[i].src+"' style='width:"+s.width+";height:"+s.height+"' >";
            P.ADD_PRINT_IMAGE(s.top, s.left, s.width, s.height,item);
            //P.SET_PRINT_STYLEA(0,"TransColor","#FFFFFF");
            P.SET_PRINT_STYLEA(0,"Stretch",2);
        }
    }

}

/*解析几何图形*/
pr.shape = function (tpl, tpl_data) {
    var d = tpl_data ? tpl_data : tpl.tpl_data;
    var show_header =tpl.is_show_header;
    for (var i in d) {

        var field = d[i].field;
        var s = d[i].style;
        if ($.inArray(field, ['line_h', 'line_s', 'rect', 'barcode_user_defined']) != -1) {
            if (field == 'line_h') {
                P.ADD_PRINT_LINE(s.top, s.left, s.top, parseInt(s.left) + parseInt(s.width), s.border_style, s.border_width);
            } else if (field == 'line_s') {
                P.ADD_PRINT_LINE(s.top, s.left, parseInt(s.top) + parseInt(s.height), s.left, s.border_style, s.border_width);
            } else if (field == 'rect') {
                P.ADD_PRINT_SHAPE(2, s.top, s.left, s.width, s.height, s.border_style, s.border_width, s.border_color);
            } else if (field == 'barcode_user_defined') {
                P.ADD_PRINT_BARCODE(s.top, s.left, s.width, s.height, "128Auto", d[i].title);
            }
            if(show_header){
                P.SET_PRINT_STYLEA(0, "ItemType", 1);
                P.SET_PRINT_STYLEA(0, "LinkedItem", 1);
            }
        }
    }
}
//首页才显示表头
pr.table = function (record, table, tpl,pnum,ptotal) {
    var is_show_sign=tpl.is_show_sign;
    var is_show_border=tpl.is_show_border;
    var is_show_page_num=tpl.is_show_page_num;
    var is_fill_height=tpl.is_fill_height;
    var is_show_goods_header=tpl.is_show_goods_header;
    var data = record.item;
    var col=1;
	var has_price_column = 0;
    if(tpl.tpl_style==1){
        col=2;
    }
    var s = table.style;
    var items = table.items;
    var tpl_type = tpl.type;
    var HTML = '<table  border="'+is_show_border+'" bordercolor="#000" style="border-collapse:collapse;"  cellspacing="0" style="' + printCommon.json2css({
            style: s,
            is_item: true,
            field: 'table'
        }) + '">';
    if(is_show_goods_header>0){
        HTML += '<tr style="background:#f7f7f7;">';
        var tds="";
        for (var j in items) {
            var rstyle=items[j].style;
            rstyle.width*=(1/col);
            tds+= '<td style="' + printCommon.json2css({
                    style: rstyle,
                    is_item: true
                }) + ';font-weight:700 !important;">' + table.items[j].title + '</td>';

        }
        HTML+=pr.repeat(tds,col);
        HTML += '</tr>';
    }

    if(tpl.tpl_style==2){
        //分组样式
        var no = 0;
        var data3=pr.groupBy(data,function(item){ return item.category_name;});
        var cate_price=0;
        for(var b in data3){
            HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
            HTML+='<td colspan='+(table.items.length)+'>'+b.replace(/'|"/g,'')+"【"+(data3[b].length)+"】"+'</td></tr>';

            var data2=pr.chunk(data3[b],col);
            var items2=pr.chunk(items,col);
            for(var m in data2){
                HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
                for (var i in data2[m]) {
                    no += 1;
                    for(var n in items2){
                        for (var j in items2[n]) {
                            if ($.inArray(tpl_type, ["ORDER"]) != -1) {
                                if ($.inArray(items2[n][j].field, ["row_money"]) != -1) {
                                    if(data2[m][j].row_money!=""){
                                        data2[m][j].row_money=Number(data2[m][j].row_money);
                                        if(isNaN(data2[m][j].row_money)==false){
                                            cate_price+=parseFloat(data2[m][j].row_money,2);
                                        }
                                    }

                                }
                            }
                            HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                            if (items2[n][j].field == 'no') {
                                HTML += no;
                            } else {
                                for (var k in data2[m][i]) {
                                    if($.isArray(data2[m][i][k])){
                                        var h=data2[m][i][k];
                                        HTML+="<table>";
                                        for(var t in h ){
                                            for(var tt in h[t]){
                                                if(tt==items2[n][j].field){
                                                    HTML+='<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true})+';border:0px;">';
                                                    if(tt==h[t].length-1){
                                                        var extra_td_style=';border:0px;';
                                                    }else{
                                                        var extra_td_style=';border:0px;border-bottom:1px solid #000;';
                                                    }
                                                    HTML+='<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) +extra_td_style+ '">';
                                                    HTML+=h[t][tt]+'</td></tr>';
                                                    continue;
                                                }
                                            }
                                        }
                                        HTML+="</table>";

                                    }else{
                                        if (items2[n][j].field == k) {
                                            HTML += '<span>' + data2[m][i][k] + '</span>';
                                        }
                                    }
                                }
                            }
                            HTML += '</td>';
                        }
                    }
                }
                HTML += '</tr>';
            }
            if(isNaN(cate_price)==false&&cate_price>0){
                cate_price=Number(parseFloat(cate_price,2)).toFixed(2);
                HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
                HTML+='<td colspan='+(table.items.length)+' style="font-size:14px;"> 小计：'+cate_price+'</td></tr>';
            }
            cate_price=0;

        }
    }else if(tpl.tpl_style<2){
        //多列样式
        var diff=0;
        var no = (pnum-1)*parseInt(tpl.row_length);
        if(tpl.tpl_style>0&&col!=1&&data.length%col!=0&&data.length%col<col){
            diff=col-data.length%col;
            var diff_arrs=[];
            var diff_arr={};
            diff_arr=$.extend(true,diff_arr,data[0]);
            for(var d in diff_arr){
                diff_arr[d]="";
            }
            for(var i=0;i<diff;i++){
                diff_arrs.push(diff_arr);
            }
        }
        if(diff>0){
            data=data.concat(diff_arrs);
        }else{
            //是否开启自动填充剩余条数
            if(is_fill_height>0){
                var pre_row_length=parseInt(tpl.row_length);
                if(pre_row_length>0&&data.length<pre_row_length){
                    //如果设置了每页显示行数且当前页小于固定行数
                    var diff_len=pre_row_length-data.length;
                    var diff_arr={};
                    var diff_arrs=[];
                    diff_arr=$.extend(true,diff_arr,data[0]);
                    for(var d in diff_arr){
                        diff_arr[d]="";
                    }
                    for(var i=0;i<diff_len;i++){
                        diff_arrs.push(diff_arr);
                    }
                    data=data.concat(diff_arrs);
                }
            }
        }
        var a=[];
        for(i=1;i<=data.length;i++){
            a.push(i);
        }
        var k=a.length;var b=[];var d=k/col;
        //双列序号显示调整用
        for(var j in a){
            if(j==d){
                break;
            }
            b.push(a[j]);
            if(col==2){ b.push(a[parseInt(j)+d]);}
        }

        var data2=pr.chunk(data,col);
        var items2=pr.chunk(items,col);
        for(var m in data2){
            HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
            for (var i in data2[m]) {
                no += 1;
                for(var n in items2){
                    for (var j in items2[n]) {
                       if ($.inArray(tpl_type, ["ORDER"]) != -1) {
                            if ($.inArray(items2[n][j].field, ["row_money"]) != -1) {
                                has_price_column = 1;
                                column_index = n;
                                HTML += '<td  tindex="' + j + '" style="' + printCommon.json2css({
                                        style: items2[n][j].style,
                                        is_item: true
                                    }) + '">';
                            } else {
                                HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                            }

                        } else {
                            HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                        }
                        if (items2[n][j].field == 'no') {
                            if(col==1){
                                HTML+=no;
                            }else{
                                HTML += b[no-1];
                            }
                        } else {
                            for (var k in data2[m][i]) {
                                if($.isArray(data2[m][i][k])){
                                    var h=data2[m][i][k];
                                    HTML+="<table>";
                                    for(var t in h ){
                                        for(var tt in h[t]){
                                            if(tt==items2[n][j].field){
                                                HTML+='<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true})+';border:0px;">';
                                                if(t==h.length-1){
                                                    var extra_td_style=';border:0px;';
                                                }else{
                                                    var extra_td_style=';border:0px;border-bottom:1px solid #000;';
                                                }
                                                HTML+='<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) +extra_td_style+ '">';
                                                HTML+=h[t][tt]+'</td></tr>';
                                                continue;
                                            }
                                        }
                                    }
                                    HTML+="</table>";

                                }else{
                                    if (items2[n][j].field == k) {
                                        HTML += '<span>' + data2[m][i][k] + '</span>';
                                    }
                                }

                            }
                        }
                        HTML += '</td>';
                    }
                }
            }
            HTML += '</tr>';

        }
    }

	 if (has_price_column) {
        HTML += "<tfoot>";
         if(is_show_page_num>0){
             if(col==2){
                 HTML+="<tr><td align='center' colspan='" + (table.items.length*col) + "'>";
                 if (record.order_no)HTML += record.order_no + " -- ";
                 if(ptotal>0){
                     HTML += "<span >第"+pnum+"页</span>/<span >共"+ptotal+"页</span></td>";
                 }else{
                     HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td></tr>";
                 }
             }else{
                 HTML+="<tr><td align='center' colspan='" + (column_index) + "'>";
                 if (record.order_no)HTML += record.order_no + " -- ";
                 if(ptotal>0){
                     HTML += "<span >第"+pnum+"页</span>/<span >共"+ptotal+"页</span></td>";
                 }else{
                     HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td>";
                 }
                 HTML+=" <td colspan='" + ((table.items.length - column_index)) + "' tdata='SubSum'";
                 HTML += " format='#,##0.00' align='left' style='font-size:12px;'>本页合计###</td></tr>";
             }
         }
        if(is_show_sign>0){
            HTML+="<tr><td align='center;' style='text-align: center' colspan=" + (table.items.length*col)+"'>收货人签名：_________________</td></tr></tfoot></table>";
        }else{
            HTML+="</tfoot></table>";
        }
    } else {
        HTML += "<tfoot style='display: table-footer-group;'>";
         if(is_show_page_num>0) {
             HTML +="<tr><td align='center' colspan='" + (table.items.length * col) + "'>";
             if (record.order_no)HTML += record.order_no + " -- ";

             if (ptotal > 0) {
                 HTML += "<span >第" + pnum + "页</span>/<span >共" + ptotal + "页</span></td></tr>";
             } else {
                 HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td></tr>";
             }
         }
        if(is_show_sign>0){
            HTML+="<tr><td align='center;' style='text-align: center' colspan=" + (table.items.length*col)+"'>收货人签名：_________________</td></tr></tfoot></table>";
        }else{
            HTML+="</tfoot></table>";
        }

    }
    P.ADD_PRINT_TABLE(s.top, s.left, s.width, (tpl.page_height - (s.top + s.height * 1.15) / cds.WIDTH_SCALE) + 'mm', HTML);
    P.SET_PRINT_STYLEA(0, "Offset2Top", -s.top);
}

pr.table2 = function (record, table, tpl,pnum,ptotal) {
    var has_price_column = 0;
    var column_index = 0;
    var is_show_sign=tpl.is_show_sign;
    var is_show_border=tpl.is_show_border;
    var is_show_page_num=tpl.is_show_page_num;
    var is_fill_height=tpl.is_fill_height;
    var is_show_goods_header=tpl.is_show_goods_header;
    var row_length=tpl.row_length;
    var data = record.item;
    var col=1;
    if(tpl.tpl_style==1){
        col=2;
    }
    var s = table.style;
    var items = table.items;
    var tpl_type = tpl.type;

    //这些类型 才打印表头
    var HTML = '<table  id="tpl_body" border="'+is_show_border+'" bordercolor="#000" style="border-collapse:collapse;"  cellspacing="0" style="' + printCommon.json2css({
            style: s,
            is_item: true,
            field: 'table'
        }) + '">';
    if(is_show_goods_header>0) {
        if ($.inArray(tpl_type, ["ORDER", "ORDER_RETURN", "PUR", "PUR_TAKE", "PUR_RETURN", "IN_STORAGE", "CHECK", "SUMMARY", "SOA", "GET_METERIA", "PROCESS", "COMPLETE", "RETURN_METERIA"]) != -1) {
            HTML += "<thead>";
        }
        HTML += '<tr style="background:#f7f7f7;' + '">';
        var tds = "";
        for (var j in items) {
            var rstyle = items[j].style;
            rstyle.width *= (1 / col);
            tds += '<td style="' + printCommon.json2css({
                    style: rstyle,
                    is_item: true
                }) + ';font-weight:700 !important;">' + table.items[j].title + '</td>';

        }
        HTML += pr.repeat(tds, col);
        HTML += '</tr>';
        if ($.inArray(tpl_type, ["ORDER", "ORDER_RETURN", "PUR", "PUR_TAKE", "PUR_RETURN", "IN_STORAGE", "CHECK", "SUMMARY", "SOA", "GET_METERIA", "PROCESS", "COMPLETE", "RETURN_METERIA"]) != -1) {
            HTML += "</thead>";
        }
    }

    if(tpl.tpl_style==2){
        //分类汇总
        var no = 0;
        var cate_price=0;
        var data3=pr.groupBy(data,function(item){ return item.category_name;});
        for(var b in data3){
            HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
            HTML+='<td colspan='+(table.items.length)+'>'+b.replace(/'|"/g,'')+"【"+(data3[b].length)+"】"+'</td></tr>';
            var data2=pr.chunk(data3[b],col);
            var items2=pr.chunk(items,col);
            for(var m in data2){
                HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
                for (var i in data2[m]) {
                    no += 1;
                    for(var n in items2){
                        for (var j in items2[n]) {
                            if ($.inArray(tpl_type, ["ORDER"]) != -1) {
                                if ($.inArray(items2[n][j].field, ["row_money"]) != -1) {
                                    if(data2[m][j].row_money!=""){
                                        data2[m][j].row_money=Number(data2[m][j].row_money);
                                        if(isNaN(data2[m][j].row_money)==false){
                                            cate_price+=parseFloat(data2[m][j].row_money,2);
                                        }
                                    }
                                }
                            }
                            HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                            if (items2[n][j].field == 'no') {
                                HTML += no;
                            } else {
                                for (var k in data2[m][i]) {
                                    if($.isArray(data2[m][i][k])){
                                        var h=data2[m][i][k];
                                        HTML+="<table>";
                                        for(var t in h ){
                                            for(var tt in h[t]){
                                                if(tt==items2[n][j].field){
                                                    HTML+='<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true})+';border:0px;">';
                                                    if(tt==h[t].length-1){
                                                        var extra_td_style=';border:0px;';
                                                    }else{
                                                        var extra_td_style=';border:0px;border-bottom:1px solid #000;';
                                                    }
                                                    HTML+='<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) +extra_td_style+ '">';
                                                    HTML+=h[t][tt]+'</td></tr>';
                                                    continue;
                                                }
                                            }
                                        }
                                        HTML+="</table>";

                                    }else{
                                        if (items2[n][j].field == k) {
                                            HTML += '<span>' + data2[m][i][k] + '</span>';
                                        }
                                    }
                                }
                            }
                            HTML += '</td>';
                        }
                    }
                }
                HTML += '</tr>';
            }
            if(isNaN(cate_price)==false&&cate_price>0){
                cate_price=Number(parseFloat(cate_price,2)).toFixed(2);
                HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
                HTML+='<td colspan='+(table.items.length)+' style="font-size:14px;"> 小计：'+cate_price+'</td></tr>';
            }
            cate_price=0;
        }
    }else if(tpl.tpl_style<2){
        //单双列
        var diff=0;
        //多列尾部不足 补充空列
        var no = (pnum-1)*parseInt(tpl.row_length);
        if(tpl.tpl_style>0&&col!=1&&data.length%col!=0&&data.length%col<col){
            diff=col-data.length%col;
            var diff_arrs=[];
            var diff_arr={};
            diff_arr=$.extend(true,diff_arr,data[0]);
            for(var d in diff_arr){
                diff_arr[d]="";
            }
            for(var i=0;i<diff;i++){
                diff_arrs.push(diff_arr);
            }
        }
        if(diff>=1){
            data=data.concat(diff_arrs);
        }else{
            //是否开启自动填充剩余条数
            if(is_fill_height>0){
                var pre_row_length=parseInt(tpl.row_length);
                if(pre_row_length>0&&data.length<pre_row_length){
                    //如果设置了每页显示行数且当前页小于固定行数
                    var diff_len=pre_row_length-data.length;
                    var diff_arr={};
                    var diff_arrs=[];
                    diff_arr=$.extend(true,diff_arr,data[0]);
                    for(var d in diff_arr){
                        diff_arr[d]="";
                    }
                    for(var i=0;i<diff_len;i++){
                        diff_arrs.push(diff_arr);
                    }
                    data=data.concat(diff_arrs);
                }
            }
        }
        var a=[];
        if(data&&data.length){
            for(i=1;i<=data.length;i++){
                a.push(i);
            }
        }

        var k=a.length;var b=[];var d=k/col;
        for(var j in a){
            if(j==d){
                break;
            }
            b.push(a[j]);
            if(col==2){ b.push(a[parseInt(j)+d]);}
        }
        var data2=pr.chunk(data,col);
        var items2=pr.chunk(items,col);
        for(var m in data2){
            HTML += '<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true}) + '">';
            for (var i in data2[m]) {
                no += 1;
                for(var n in items2){
                    for (var j in items2[n]) {
                        if ($.inArray(tpl_type, ["ORDER"]) != -1) {
                            if ($.inArray(items2[n][j].field, ["row_money"]) != -1) {
                                has_price_column = 1;
                                column_index = n;
                                HTML += '<td  tindex="' + j + '" style="' + printCommon.json2css({
                                        style: items2[n][j].style,
                                        is_item: true
                                    }) + '">';
                            } else {
                                HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                            }

                        } else {
                            HTML += '<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) + '">';
                        }
                        if (items2[n][j].field == 'no') {
                            if(col==1){
                                HTML+=no;
                            }else{
                                HTML += b[no-1];
                            }
                        } else {
                            for (var k in data2[m][i]) {
                                if($.isArray(data2[m][i][k])){
                                    var h=data2[m][i][k];
                                    HTML+="<table>";
                                    for(var t in h ){
                                        for(var tt in h[t]){
                                            if(tt==items2[n][j].field){
                                                HTML+='<tr style="' + printCommon.json2css({style: s, filed: 'tr', is_item: true})+';border:0px;">';
                                                if(tt==h[t].length-1){
                                                    var extra_td_style=';border:0px;';
                                                }else{
                                                    var extra_td_style=';border:0px;border-bottom:1px solid #000;';
                                                }
                                                HTML+='<td style="' + printCommon.json2css({style: items2[n][j].style, is_item: true}) +extra_td_style+ '">';
                                                HTML+=h[t][tt]+'</td></tr>';
                                                continue;
                                            }
                                        }
                                    }
                                    HTML+="</table>";

                                }else{
                                    if (items2[n][j].field == k) {
                                        HTML += '<span>' + data2[m][i][k] + '</span>';
                                    }
                                }
                            }
                        }
                        HTML += '</td>';
                    }
                }
            }
            HTML += '</tr>';
        }

    }
    if (has_price_column) {
        HTML += "<tfoot>";
        if(is_show_page_num>0){
            if(col==2){
                HTML+="<tr><td align='center' colspan='" + (table.items.length*col) + "'>";
                if (record.order_no)HTML += record.order_no + " -- ";
                if(ptotal>0){
                    HTML += "<span >第"+pnum+"页</span>/<span >共"+ptotal+"页</span></td>";
                }else{
                    HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td></tr>";
                }
            }else{
                HTML+="<tr><td align='center' colspan='" + (column_index) + "'>";
                if (record.order_no)HTML += record.order_no + " -- ";
                if(ptotal>0){
                    HTML += "<span >第"+pnum+"页</span>/<span >共"+ptotal+"页</span></td>";
                }else{
                    HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td>";
                }
                HTML+=" <td colspan='" + ((table.items.length - column_index)) + "' tdata='SubSum'";
                HTML += " format='#,##0.00' align='left' style='font-size:12px;'>本页合计###</td></tr>";
            }
        }
        if(is_show_sign>0){
            HTML+="<tr><td align='center;' style='text-align: center' colspan=" + (table.items.length*col)+"'>收货人签名：_________________</td></tr></tfoot></table>";
        }else{
            HTML+="</tfoot></table>";
        }
    } else {
        HTML += "<tfoot style='display: table-footer-group;'>";
        if(is_show_page_num>0) {
            HTML+="<tr><td align='center' colspan='" + (table.items.length * col) + "'>";
            if (record.order_no)HTML += record.order_no + " -- ";

            if (ptotal > 0) {
                HTML += "<span >第" + pnum + "页</span>/<span >共" + ptotal + "页</span></td></tr>";
            } else {
                HTML += "<span tdata='pageNO'>第#页</span>/<span tdata='pageCount'>共#页</span></td></tr>";
            }
        }
        if(is_show_sign>0){
            HTML+="<tr><td align='center;' style='text-align: center' colspan=" + (table.items.length*col)+"'>收货人签名：_________________</td></tr></tfoot></table>";
        }else{
            HTML+="</tfoot></table>";
        }

    }
    P.ADD_PRINT_TABLE(s.top, s.left, s.width, (tpl.page_height - (s.top + s.height * 1.15) / cds.WIDTH_SCALE) + 'mm', HTML);
    P.SET_PRINT_STYLEA(0, "Offset2Top", 0);

}

pr.underTable = function (record, tpl, tpl_data) {
    var t = tpl_data;
    var n = 0;
    for (var i in t) {
        var s = t[i].style;
        var f = t[i].field;
        if (!t[i].type) {
            for (var j in record) {
                if (f == j) {
                    //有时间需完善|陈端生
                    pr.add_print_text(t[i], record[j]);
                    P.SET_PRINT_STYLEA(0, "ItemType", n);
                    n = n - 1;
                    P.SET_PRINT_STYLEA(0, "LinkedItem", n);
                }
            }
        }else if (t[i].type == 'tx') {
            pr.add_print_text(t[i], t[i].title);
            P.SET_PRINT_STYLEA(0, "ItemType", n);
            n = n - 1;
            P.SET_PRINT_STYLEA(0, "LinkedItem", n);
        }else if (t[i].type == 'barcode') {
            if (f == 'barcode_user_defined') {
                P.ADD_PRINT_BARCODE(s.top, s.left, s.width, s.height, "128Auto", t[i].title);
                n = n - 1;
                P.SET_PRINT_STYLEA(0, "LinkedItem", n);
            } else {
                for (var j in record) {
                    if (f == j) {

                        //有时间需完善|陈端生
                        var s = t[i].style;
                        P.ADD_PRINT_BARCODE(s.top, s.left, s.width, s.height, "128Auto", record[j]);
                        n = n - 1;
                        P.SET_PRINT_STYLEA(0, "LinkedItem", n);
                    }
                }
            }
        }else if (f == 'line_h') {
            P.ADD_PRINT_LINE(s.top, s.left, s.top, parseInt(s.left) + parseInt(s.width), s.border_style, s.border_width);
            P.SET_PRINT_STYLEA(0, "ItemType", n);
            n = n - 1;
            P.SET_PRINT_STYLEA(0, "LinkedItem", n);
        }else if (f == 'line_s') {
            P.ADD_PRINT_LINE(s.top, s.left, parseInt(s.top) + parseInt(s.height), s.left, s.border_style, s.border_width);
            P.SET_PRINT_STYLEA(0, "ItemType", n);
            n = n - 1;
            P.SET_PRINT_STYLEA(0, "LinkedItem", n);
        }
        else if (f == 'rect') {
            P.ADD_PRINT_SHAPE(2, s.top, s.left, s.width, s.height, s.border_style, s.border_width, s.border_color);
            P.SET_PRINT_STYLEA(0, "ItemType", n);
            n = n - 1;
            P.SET_PRINT_STYLEA(0, "LinkedItem", n);
        }else if(f=='image'){
            var img_src=t[i].src;
            var item ="<img  src='"+img_src+"'  style='width:"+s.width+";height: "+s.height+"'/>";
            P.ADD_PRINT_IMAGE(s.top, s.left, s.width, s.height,item);
            //P.SET_PRINT_STYLEA(0,"TransColor","#FFFFFF");
            P.SET_PRINT_STYLEA(0,"Stretch",2);
            P.SET_PRINT_STYLEA(0, "ItemType", n);
            n = n - 1;
            P.SET_PRINT_STYLEA(0, "LinkedItem", n);
        }
    }

}

/*公共函数开始-----*/

pr.cutTpl = function (tpl) {
    var d = tpl.tpl_data;
    var style = d[d.length - 1].style;

    var tab_top = style.top;
    var tab_bottom = style.top + style.height;

    var up = [], down = [];
    for (var i in d) {
        if (d[i].style.top < tab_top) {
            up.push(d[i]);
        }
        if (d[i].style.top > tab_bottom) {
            d[i].style.top = d[i].style.top - tab_bottom;
            down.push(d[i]);
        }
    }
    var d={
        up: up,
        down: down,
        table: d[d.length - 1]
    }
    console.log(d);
    return d;
}

pr.add_print_text = function (el, tx) {
    var s = el.style;

    P.ADD_PRINT_TEXT(s.top, s.left, s.width, s.height, tx);

    if (s.font_style == 'italic') {
        P.SET_PRINT_STYLEA(0, 'Italic', 1);
    }

    if (s.text_decoration == 'underline') {
        P.SET_PRINT_STYLEA(0, 'Underline', 1);
    }

    if (s.font_weight == 'bolder' || s.font_weight == 'bold') {
        P.SET_PRINT_STYLEA(0, 'Bold', 1);
    }

    P.SET_PRINT_STYLEA(0, "FontName", s.font_family || '微软雅黑');

    P.SET_PRINT_STYLEA(0, 'FontSize', parseFloat(s.font_size) * 0.75 || 9);

    P.SET_PRINT_STYLEA(0, 'FontColor', s.color || '#00000');

    if (s.text_align == 'left') {
        P.SET_PRINT_STYLEA(0, 'Alignment', 1);
    } else if (s.text_align == 'center') {
        P.SET_PRINT_STYLEA(0, 'Alignment', 2);
    } else if (s.text_align == 'right') {
        P.SET_PRINT_STYLEA(0, 'Alignment', 3);
    }

    P.SET_PRINT_STYLEA(0, "LetterSpacing", s.letter_spacing);

    P.SET_PRINT_STYLEA(0, "LineSpacing", (parseFloat(s.line_height) - parseFloat(s.font_size)) * 0.75);
}