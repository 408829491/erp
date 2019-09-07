/*auther:陈端生15111222285; createtime:2017-05-25*/

function getQueryString(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}

if(typeof w2utils=="undefined"){
	console.log('请将common.js放在所有main.js文件后面加载');
}
//w2utils.locale('../../../../../../zh-cn.json');

/*
function setGridHeight(){
	var toHeight = $(window).height()-405;
	$('iframe').height(toHeight);
	$('.w2ui-grid').height(toHeight);
	$('#grid').height(toHeight);
}
*/
/*打开新窗口*/
function openNewBrowserWindow(url){
    var tmp=window.open("about:blank","","scrollbars=yes,menubar=yes, resizable=yes, location=no, channelmode=yes");
    tmp.moveTo(0,0);
    tmp.resizeTo(screen.width,screen.height);
    tmp.focus();
    tmp.location=url;
    tmp.innerHTML=window.innerHTML;
}

/*拓展jq序列化*/
$.fn.serializeObject = function()
{
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};