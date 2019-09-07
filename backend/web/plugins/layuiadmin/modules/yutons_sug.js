/**
 * @Title：yutons_sug搜索框提示插件||输入框提示插件
 * @Version：1.0.1
 * @Auth：yutons
 * @Date: 2019/04/08
 * @Time: 10:00
 */
layui.define(['jquery', 'table'], function (exports) {
    "use strict";
    const $ = layui.jquery,
        table = layui.table;

    let yutons_sug = function () {
        this.v = '1.0.1';
    };
    /**
     * yutons_sug搜索框提示插件||输入框提示插件初始化
     */
    yutons_sug.prototype.render = function (opt) {
        opt.urlBak = opt.url;
        //设置默认初始化参数
        opt.type = opt.type || 'sug'; //默认sug，传入sug||sugTable
        opt.elem = "#yutons_sug_" + opt.id;
        opt.height = opt.height || '229';
        opt.cellMinWidth = opt.cellMinWidth || '80'; //最小列宽
        opt.page = opt.page || true;
        opt.limits = opt.limits || [20];
        opt.loading = opt.loading || true;
        opt.limit = opt.limit || 20;
        opt.size = opt.size || 'sm'; //小尺寸的表格

        //初始化输入框提示容器
        $("#" + opt.id).after("<div id='sugItem' style='background-color: #fff;display: none;z-index:1;position: absolute;width:100%;'></div>");

        //输入框提示容器移出事件：鼠标移出隐藏输入提示框
        $("#" + opt.id).parent().mouseleave(function () {
            $("#" + opt.id).next().hide().html("");
        });


        if (opt.type == "sugTable") {
            //如果type为sugTable，则初始化下拉表格
            /* 输入框鼠标松开事件 */
            $("#" + opt.id).mouseup(function (e) {
                opt.obj = this;
                getSugTable(opt);
            })
            /* 输入框键盘抬起事件 */
            $("#" + opt.id).keyup(function (e) {
                opt.obj = this;
                getSugTable(opt);
            })
        } else if (opt.type == "sug") {
            //如果type为sug，则初始化下拉框
            $("#" + opt.id).next().css("border", "solid #e6e6e6 0.5px");
            /* 输入框鼠标松开事件 */
            $("#" + opt.id).mouseup(function (e) {
                opt.obj = this;
                getSug(opt);
            })
            /* 输入框键盘抬起事件 */
            $("#" + opt.id).keyup(function (e) {
                opt.obj = this;
                getSug(opt);
            })
        }
    }

    //搜索框提示插件||输入框提示插件--sugTable-下拉表格
    function getSugTable(opt) {
        //如果输入信息为"",则隐藏输入提示框,不再执行下边代码
        let keyword = $.trim($(opt.obj).val());
        if (keyword == "") {
            $("#" + opt.id).next().hide().html("");
            return false;
        }
        //下拉表格初始化table容器
        let html = '<table id="yutons_sug_' + opt.obj.getAttribute("id") + '" lay-filter="yutons_sug_' + opt.obj.getAttribute(
            "id") +
            '"></table>';
        $("#" + opt.obj.getAttribute("id")).next().show().html(html);

        //下拉表格初始化
        opt.url = opt.urlBak + keyword;
        opt.toolbar = false;
        opt.page = false;
        table.render(opt);
        //设置下拉表格样式
        $(opt.elem).next().css("margin-top", "0").css("background-color", "#ffffff");
        //监听下拉表格行单击事件（单击||双击事件为：row||rowDouble）设置单击或双击选中对应的行
        // table.on('rowDouble(' + "yutons_sug_" + opt.id + ')', function (obj) {
        //     for (const param of opt.params) {
        //         //$("#" + param.name).val(obj.data[param.field])
        //         //此处修改---由原来的根据id设置value值变更为根据name设置value值
        //         $("*[name='"+param.name+"']").val(obj.data[param.field])
        //     }
        //     $("#" + opt.id).next().hide().html("");
        // });
    }

    //搜索框提示插件||输入框提示插件--sug-下拉框
    function getSug(opt) {
        sessionStorage.setItem("inputId", opt.id)
        if (opt.idField != undefined && opt.idField != null) {
            sessionStorage.setItem("idField", opt.idField);
        }
        let keyword = $.trim($(opt.obj).val());
        if (keyword == "") {
            $("#" + opt.id).next().hide().html("");
            return false;
        }
        //sug下拉框异步加载数据并渲染下拉框
        $.ajax({
            type: "get",
            url: opt.urlBak + keyword,
            success: function (data) {
                let html = "";
                layui.each(data.data, (index, item) => {
                    if (index >= opt.limit) {
                        return;
                    }
                    //if (item[sessionStorage.getItem("inputId")].indexOf(decodeURI(keyword)) >= 0) {
                    html +=
                        "<div class='item' style='padding: 3px 10px;cursor: pointer;' onmouseenter='getFocus(this)' onClick='getCon(this);' id='sug_" + sessionStorage.getItem("idField") + "' name='" + item[sessionStorage.getItem("idField")] + "'>" +
                        item[sessionStorage.getItem("inputId")] + "</div>";
                    //}
                });
                if (html != "") {
                    $("#" + sessionStorage.getItem("inputId")).next().show().html(html);
                } else {
                    $("#" + sessionStorage.getItem("inputId")).next().hide().html("");
                }
            }
        });
    }

    //搜索框提示插件||输入框提示插件--sug-下拉框上下键移动事件和回车事件
    $(document).keydown(function (e) {
        e = e || window.event;
        let keycode = e.which ? e.which : e.keyCode;
        if (keycode == 38) {
            //上键事件
            if ($.trim($("#" + sessionStorage.getItem("inputId")).next().html()) == "") {
                return;
            }
            movePrev(sessionStorage.getItem("inputId"));
        } else if (keycode == 40) {
            //下键事件
            if ($.trim($("#" + sessionStorage.getItem("inputId")).next().html()) == "") {
                return;
            }
            $("#" + sessionStorage.getItem("inputId")).blur();
            if ($(".item").hasClass("addbg")) {
                moveNext();
            } else {
                $(".item").removeClass('addbg').css("background", "").eq(0).addClass('addbg').css("background", "#e6e6e6");
            }
        } else if (keycode == 13) {
            //回车事件


            dojob();
        }
    });
    //上键事件
    let movePrev = function (id) {
        $("#" + id).blur();
        let index = $(".addbg").prevAll().length;
        if (index == 0) {
            $(".item").removeClass('addbg').css("background", "").eq($(".item").length - 1).addClass('addbg').css(
                "background", "#e6e6e6");
        } else {
            $(".item").removeClass('addbg').css("background", "").eq(index - 1).addClass('addbg').css("background", "#e6e6e6");
        }
    }
    //下键事件
    let moveNext = function () {
        let index = $(".addbg").prevAll().length;
        if (index == $(".item").length) {
            $(".item").removeClass('addbg').css("background", "").eq(0).addClass('addbg').css("background", "#e6e6e6");
        } else {
            $(".item").removeClass('addbg').css("background", "").eq(index + 1).addClass('addbg').css("background", "#e6e6e6");
        }
    }
    //回车事件
    let dojob = function () {

        //如果未定义idField，则不添加idField字段
        let idField = sessionStorage.getItem("idField");
        if (idField != undefined && idField != null) {
            $("#" + sessionStorage.getItem("idField")).val($(".addbg").attr("name"))
        }

        let value = $(".addbg").text();
        $("#" + sessionStorage.getItem("inputId")).blur();
        $("#" + sessionStorage.getItem("inputId")).val(value);
        $("#" + sessionStorage.getItem("inputId")).next().hide().html("");
    }

    //上下键选择和鼠标选择事件改变颜色
    window.getFocus = function (obj) {
        $(".item").css("background", "");
        $(obj).css("background", "#e6e6e6");
    }

    //点击选中事件，获取选中内容并回显到输入框
    window.getCon = function (obj) {
        let value = $(obj).text();
        //如果未定义idField，则不添加idField字段
        let idField = sessionStorage.getItem("idField");
        if (idField != undefined && idField != null) {
            $("#" + sessionStorage.getItem("idField")).val($(".item").attr("name"))
        }
        $("#" + $(".item").parent().prev().attr("id")).val(value);
        $("#" + $(".item").parent().prev().attr("id")).next().hide().html("");
    }

    //自动完成渲染
    yutons_sug = new yutons_sug();
    //暴露方法
    exports("yutons_sug", yutons_sug);
});
