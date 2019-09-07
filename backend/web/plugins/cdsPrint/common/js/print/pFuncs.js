var CreatedOKLodop7766 = null;

function setLicense() {
    var domain = location.host;
    if(!LODOP){
        return false;
    }
   $.ajax({url:"/admin/key.json",async:false,dateType:"json",success:function(res){
       var jsonData=[
           {"Domain":"sdongpo.com","Licence":"332D3C455C6A2E47D6E0C067886DDEF9","LicenceA":"C94CEE276DB2187AE6B65D56B3FC2848","LicenceB":""},
           {"Domain":"qudengshan.cn","Licence":"9C341FA88D1CD9B451D94C6A07705B64","LicenceA":"C94CEE276DB2187AE6B65D56B3FC2848","LicenceB":""},
           {"Domain":"lianlvpo.com","Licence":"79EFA0B7321732101E5776CD58667E87","LicenceA":"C94CEE276DB2187AE6B65D56B3FC2848","LicenceB":""},
           {"Domain":"dengshanjia.cn","Licence":"D4941AC17E63F2EB02D8AE20CA380254","LicenceA":"C94CEE276DB2187AE6B65D56B3FC2848","LicenceB":""}];
       var jsonData2=res;
       if(jsonData2){
           jsonData=jsonData.concat(jsonData2);
       }
        $.each(jsonData,function(i,t){
            if(domain.indexOf(t.Domain) != -1){
                console.log(i);
                LODOP.SET_LICENSES("",t.Licence,t.LicenceA,t.LicenceB);
            }
        });
         }
   ,error:function () {
        console.log(2);
        if(domain.indexOf('sdongpo.com') != -1)
            LODOP.SET_LICENSES("","332D3C455C6A2E47D6E0C067886DDEF9","C94CEE276DB2187AE6B65D56B3FC2848","");
        if(domain.indexOf('qudengshan.cn') != -1)
            LODOP.SET_LICENSES("","9C341FA88D1CD9B451D94C6A07705B64","C94CEE276DB2187AE6B65D56B3FC2848","");
        if(domain.indexOf('lianlvpo.com') != -1)
            LODOP.SET_LICENSES("","79EFA0B7321732101E5776CD58667E87","C94CEE276DB2187AE6B65D56B3FC2848","");
        if(domain.indexOf('dengshanjia.cn') != -1)
            LODOP.SET_LICENSES("","D4941AC17E63F2EB02D8AE20CA380254","C94CEE276DB2187AE6B65D56B3FC2848","");
    }
});


  /* if(domain.indexOf('sdongpo.com') != -1)
	    LODOP.SET_LICENSES("","332D3C455C6A2E47D6E0C067886DDEF9","C94CEE276DB2187AE6B65D56B3FC2848","");
    if(domain.indexOf('qudengshan.cn') != -1)
	    LODOP.SET_LICENSES("","9C341FA88D1CD9B451D94C6A07705B64","C94CEE276DB2187AE6B65D56B3FC2848","");
    if(domain.indexOf('lianlvpo.com') != -1)
        LODOP.SET_LICENSES("","79EFA0B7321732101E5776CD58667E87","C94CEE276DB2187AE6B65D56B3FC2848","");
	if(domain.indexOf('dengshanjia.cn') != -1)
        LODOP.SET_LICENSES("","D4941AC17E63F2EB02D8AE20CA380254","C94CEE276DB2187AE6B65D56B3FC2848","");*/
}

function needCLodop() {
    try {
        var scheme=location.protocol.toLowerCase().replace(":","");
        if(scheme=="https"){
            console.log(location);
            return true;
        }
        var ua = navigator.userAgent;
        if (ua.match(/Windows\sPhone/i) != null) return true;
        if (ua.match(/iPhone|iPod/i) != null) return true;
        if (ua.match(/Android/i) != null) return true;
        if (ua.match(/Edge\D?\d+/i) != null) return true;
        if (ua.match(/QQBrowser/i) != null) return false;
        var verTrident = ua.match(/Trident\D?\d+/i);
        var verIE = ua.match(/MSIE\D?\d+/i);
        var verOPR = ua.match(/OPR\D?\d+/i);
        var verFF = ua.match(/Firefox\D?\d+/i);
        var x64 = ua.match(/x64/i);
        if ((verTrident == null) && (verIE == null) && (x64 !== null)) return true;
        else if (verFF !== null) {
            verFF = verFF[0].match(/\d+/);
            if (verFF[0] >= 42) return true
        } else if (verOPR !== null) {
            verOPR = verOPR[0].match(/\d+/);
            if (verOPR[0] >= 32) return true
        } else if ((verTrident == null) && (verIE == null)) {
            var verChrome = ua.match(/Chrome\D?\d+/i);
            if (verChrome !== null) {
                verChrome = verChrome[0].match(/\d+/);
                if (verChrome[0] >= 42) return true
            }
        };
        return false
    } catch (err) {
        return true
    }
};
if (needCLodop()) {
    var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
    var oscript = document.createElement("script");
    oscript = document.createElement("script");
    var scheme=location.protocol.toLowerCase().replace(":","");
    if(scheme=="https"){
        oscript.src ="https://localhost:8443/CLodopfuncs.js?priority=1";
    }else{
        oscript.src ="http://localhost:8000/CLodopfuncs.js?priority=1";
    }
    head.insertBefore(oscript, head.firstChild);
};

function getLodop(oOBJECT, oEMBED) {
    var strHtmInstall = "<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='http://113.10.155.131/install_lodop32.zip' target='_0'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
    var strHtmUpdate = "<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='http://113.10.155.131/install_lodop32.zip' target='_0'>执行升级</a>,升级后请重新进入。</font>";
    var strHtm64_Install = "<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='http://113.10.155.131/install_lodop64.zip' target='_0'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
    var strHtm64_Update = "<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='http://113.10.155.131/install_lodop64.zip' target='_0'>执行升级</a>,升级后请重新进入。</font>";
    var strHtmFireFox = "<br><br><font color='#FF00FF'>（注意：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它）</font>";
    var strHtmChrome = "<br><br><font color='#FF00FF'>(如果此前正常，仅因浏览器升级或重安装而出问题，需重新执行以上安装）</font>";
    var scheme=location.protocol.toLowerCase().replace(":","");
    if(scheme=="http"){
        var strCLodopInstall="<br><font color='#FF00FF'>CLodop云打印服务(localhost本地)未安装启动!点击这里<a href='http://demo.c-lodop.com:8000/Lodop6.221_CLodop3.029.zip' target='_self'>执行安装</a>,安装后请刷新页面。</font>";
        var strCLodopUpdate="<br><font color='#FF00FF'>CLodop云打印服务需升级!点击这里<a href='http://demo.c-lodop.com:8000/Lodop6.221_CLodop3.029.zip' target='_self'>执行升级</a>,升级后请刷新页面。</font>";
    }else{
        var strCLodopInstall="<br><font color='#FF00FF'>CLodop云打印服务(localhost本地)未安装启动!点击这里<a href='http://demo.c-lodop.com:8000/CLodop_Setup_for_Win32NT_https_3.029Extend.zip' target='_self'>执行安装</a>,安装后请刷新页面。</font>";
        var strCLodopUpdate="<br><font color='#FF00FF'>CLodop云打印服务需升级!点击这里<a href='http://demo.c-lodop.com:8000/CLodop_Setup_for_Win32NT_https_3.029Extend.zip' target='_self'>执行升级</a>,升级后请刷新页面。</font>";
    }
    var LODOP;
    try {
        var isIE = (navigator.userAgent.indexOf('MSIE') >= 0) || (navigator.userAgent.indexOf('Trident') >= 0);
        if (needCLodop()) {
            try {
                LODOP = getCLodop()
            } catch (err) {};
            if (!LODOP && document.readyState !== "complete") {
                alert("C-Lodop没准备好，请重启Clodop服务后再试！");
                return
            };
            if (!LODOP) {
                alert("您暂未安装Clodop打印控件，请先安装！");
                window.open("http://www.mtsoftware.cn/download.html","_blank");
                return false;
                /*if (isIE) document.write(strCLodopInstall);
                else document.documentElement.innerHTML = strCLodopInstall + document.documentElement.innerHTML;
                return*/
            } else {
                if (CLODOP.CVERSION < "2.0.6.2") {
                    alert("您的Clodop打印控件版本过低，请下载最新版本");
                    window.open("http://www.mtsoftware.cn/download.html","_blank");
                    return false;
                   /* if (isIE) document.write(strCLodopUpdate);
                    else document.documentElement.innerHTML = strCLodopUpdate + document.documentElement.innerHTML*/
                };
                if (oEMBED && oEMBED.parentNode) oEMBED.parentNode.removeChild(oEMBED);
                if (oOBJECT && oOBJECT.parentNode) oOBJECT.parentNode.removeChild(oOBJECT)
            }
        } else {
            var is64IE = isIE && (navigator.userAgent.indexOf('x64') >= 0);
            if (oOBJECT != undefined || oEMBED != undefined) {
                if (isIE) LODOP = oOBJECT;
                else LODOP = oEMBED
            } else if (CreatedOKLodop7766 == null) {
                LODOP = document.createElement("object");
                LODOP.setAttribute("width", 0);
                LODOP.setAttribute("height", 0);
                LODOP.setAttribute("style", "position:absolute;left:0px;top:-100px;width:0px;height:0px;");
                if (isIE) LODOP.setAttribute("classid", "clsid:2105C259-1E0C-4534-8141-A753534CB4CA");
                else LODOP.setAttribute("type", "application/x-print-lodop");
                document.documentElement.appendChild(LODOP);
                CreatedOKLodop7766 = LODOP
            } else LODOP = CreatedOKLodop7766; if ((LODOP == null) || (typeof(LODOP.VERSION) == "undefined")) {
                alert("您暂未安装Clodop打印控件，请先安装！");
                window.open("http://www.mtsoftware.cn/download.html","_blank");
                return false;
                if (navigator.userAgent.indexOf('Chrome') >= 0) document.documentElement.innerHTML = strHtmChrome + document.documentElement.innerHTML;
                if (navigator.userAgent.indexOf('Firefox') >= 0) document.documentElement.innerHTML = strHtmFireFox + document.documentElement.innerHTML;
                if (is64IE) document.write(strHtm64_Install);
                else if (isIE) document.write(strHtmInstall);
                else document.documentElement.innerHTML = strHtmInstall + document.documentElement.innerHTML;
				if(!!LODOP){
				    setLicense();
				}
                return LODOP
            }
        }; if (LODOP.VERSION < "6.2.0.3") {
            alert("您的Clodop打印控件版本过低，请下载最新版本");
            window.open("http://www.mtsoftware.cn/download.html","_blank");
            return false;
            if (needCLodop()) document.documentElement.innerHTML = strCLodopUpdate + document.documentElement.innerHTML;
            else if (is64IE) document.write(strHtm64_Update);
            else if (isIE) document.write(strHtmUpdate);
            else document.documentElement.innerHTML = strHtmUpdate + document.documentElement.innerHTML;
			if(!!LODOP){
				setLicense();
			}
            return LODOP
        };
        //LODOP.SET_LICENSES("","332D3C455C6A2E47D6E0C067886DDEF9","C94CEE276DB2187AE6B65D56B3FC2848","");
		if(!!LODOP){
		    setLicense();
		}
        return LODOP
    } catch (err) {
        console.log("getLodop出错:" + err);
        alert("getLodop出错:" + err)
    }
};