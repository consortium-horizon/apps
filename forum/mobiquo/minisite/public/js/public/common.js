/**
 * 公共变量类
 *
 * CV是common variable的缩写
 */
function CV() {
    this.cmd = null;   /* 页面当前的操作字符串 */
    this.ua = navigator.userAgent;  /* 浏览器版本信息 */
    this.isIE = this.ua.match(/msie/i) ? true : false;  /* 判断是否是ie浏览器 */
    this.isIE6 = this.isIE && (navigator.appVersion.split(";")[1].replace(/[ ]/g,"") =="MSIE6.0") ? true:false;  /* 判断是否是ie6 */
    this.isIE7 = this.isIE && (navigator.appVersion.split(";")[1].replace(/[ ]/g,"") =="MSIE7.0") ? true:false;  /* 判断是否是ie7 */
    this.isIE8 = this.isIE && (navigator.appVersion.split(";")[1].replace(/[ ]/g,"") =="MSIE8.0") ? true:false;  /* 判断是否是ie8 */
    this.is360 = this.ua.toLowerCase().indexOf('360se')? true : false;  /* 判断是否是360浏览器 */
    this.isFireFox = this.ua.match(/firefox/i) ? true : false;  /* 判断是否是firefox浏览器 */
    /* 错误信息常量，页面载入后初始化 */
    this.ERR_TOP = null;
    this.ERR_HIGH = null;
    this.ERR_APP = null;
    this.ERR_INFO = null;
    this.info = new Array(); /* 默认公共信息 */
    
    this.jsLibUrl = null;       /* 公共js目录 */
    this.cssLibUrl = null;       /* 公共css目录 */
    this.imgLibUrl = null;       /* 公共img目录 */
    this.flashLibUrl = null;       /* 公共flash目录 */
    
    this.appJsLibUrl = null;       /* 模块js目录 */
    this.appCssLibUrl = null;       /* 模块css目录 */
    this.appImgLibUrl = null;       /* 模块img目录 */
    this.appFlashLibUrl = null;       /* 模块flash目录 */
    
    this.rewriteMethod = null;  /* rewrite方式 */
    this.mainHomeDomain = null;   /* 主站域名 */
    this.homeDomain = null;   /* 模块域名 */
    this.theme = null;  /* 项目皮肤名 */
}




/**
 * 以跨浏览器的形式创建XMLHttpClient的函数
 */
function initXMLHttpClient() {
    var xmlhttp;
    try {
        // Mozilla / Safari / IE7+
        xmlhttp = new XMLHttpRequest();
    } catch (e) {
        // IE 
        var XMLHTTP_IDS = new Array('MSXML2.XMLHTTP.6.0',

'MSXML2.XMLHTTP.5.0',

'MSXML2.XMLHTTP.4.0',

'MSXML2.XMLHTTP.3.0',

'MSXML2.XMLHTTP',

'Microsoft.XMLHTTP' );
        var success = false;
        for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
            try {
                xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
                success = true;
            } catch (e) {}
        }
        if (!success) {
            //throw new Error('Unable to create XMLHttpRequest.');
            return false;
        }
    }
    return xmlhttp;
}




/**
 * 公共函数类
 *
 * CF是common function的缩写
 */
function CF() {
    this.xHttp = null;  /* ajax对象 */
    this.ajaxRsp = null;    /* ajax返回的信息（json） */
    this.ajaxOverTime = null; /*ajax超时时间*/
}

/**
 * 初始化ajax对象
 */
CF.prototype.initXHttp = function() {
    this.xHttp = initXMLHttpClient();
}

/**
 * 弹出对话框函数
 *
 * @param Objcect params 弹出对话框函数可选参数(
   {
        String message 对话框提示消息
        Integer displayTime 对话框显示时间（单位为毫秒）
   }
   )
 */
CF.prototype.dlg = function(params) {
    alert(params.message);
    if (this.ajaxRsp && this.ajaxRsp.redirectUrl) {
        if (params.displayTime) {
            this.redirectByAjaxRsp(params.displayTime);
        } else {
            this.redirectByAjaxRsp();
        }
    }
}

/**
 * 弹出确认对话框
 *
 * @param Objcect params 弹出对话框函数可选参数(
   {
        String message 对话框提示消息
   }
   )
 */
CF.prototype.dlgConfirm = function(params) {
    return confirm(params.message);
}

/**
 * 添加url
 *
 * @param  String  url  提交的url路径
 * @param  String  varName  变量名
 * @param  String  v  变量值
 */
CF.prototype.addUrl = function(url,varName,v){
    if (jCv && jCv.rewriteMethod) {
        if (jCv.rewriteMethod == 'php') {
            if (url.substr(url.length-1) == '/') {
                url +=  varName+'/'+v;
            } else {
                url +=  '/'+varName+'/'+v;
            }
        } else if (jCv.rewriteMethod == 'normal') {
            if (url.substr(url.length-1) == '/') {
                url +=  varName+'/'+v;
            } else {
                url +=  '/'+varName+'/'+v;
            }
        } else if (jCv.rewriteMethod == 'none') {
            url += "&" + varName+'='+v;
        }
    }
    return url;
}

/**
 * 异步提交表单
 *
 * @param  String  url  提交的url路径
 * @param  String  postData  提交的表单数据
 * @param  String  funcName  对应的处理函数名
 * @param  Integer  overTime  请求超时时间
 * @param  String  overFuncName  请求超时时间函数名
 */
CF.prototype.asyncSubmit = function(url, postData, funcName, overTime, overFuncName) {
    if (jCv && jCv.rewriteMethod) {
        if (jCv.rewriteMethod == 'php') {
            if (url.substr(url.length-1) == '/') {
                url += 'ajaxSec/' + Date.parse(new Date());
            } else {
                url += '/ajaxSec/' + Date.parse(new Date());
            }
        } else if (jCv.rewriteMethod == 'normal') {
            if (url.substr(url.length-1) == '/') {
                url += 'ajaxSec/' + Date.parse(new Date());
            } else {
                url += '/ajaxSec/' + Date.parse(new Date());
            }
        } else if (jCv.rewriteMethod == 'none') {
            url += "&ajaxSec=" + Date.parse(new Date());
        }
    }
    this.xHttp.open('post', url, true);
    this.xHttp.setRequestHeader("content-length",postData.length);
    this.xHttp.setRequestHeader("content-type","application/x-www-form-urlencoded");
    eval('this.xHttp.onreadystatechange = '+funcName);
    this.xHttp.send(postData);
    /*设置超时时间监控*/
    if(overTime){
        var oThis = this;
        this.ajaxOverTime = setTimeout(function(){
            if (oThis.xHttp) {
                oThis.xHttp.abort();
            }
            if(overFuncName){
                eval(overFuncName);
            }
        },overTime);
    }
}

/**
 * 判断异步提交表单是否成功完成
 *
 * @return  Boolean
 */
CF.prototype.asyncSubmitOk = function() {
    if (this.xHttp.readyState == 4 && (this.xHttp.status == 200 || this.xHttp.status == 0)) {
        /* 清除超时时间监控 */
        if(this.ajaxOverTime != null) {
            clearTimeout(this.ajaxOverTime);
        }
        return true;
    }
    return false;
}

/**
 * 判断异步提交表单是否失败
 *
 * @return  Boolean
 */
CF.prototype.asyncSubmitFail = function() {
    if (this.xHttp.readyState == 4 && (this.xHttp.status != 0 && this.xHttp.status != 1 && this.xHttp.status != 2 && this.xHttp.status != 200)) {
        return true;
    }
    return false;
}

/**
 * 根据ajax返回的信息（json）构造this.ajaxRsp
 */
CF.prototype.makeAjaxRsp = function() {
    if (this.xHttp.responseText.indexOf('{') == 0) {    /* 返回的可能是正常的json数据 */
        var tempRsp = eval('['+this.xHttp.responseText+']');
        this.ajaxRsp = tempRsp[0];
    }
}

/**
 * 根据this.ajaxRsp跳转页面
 *
 * @param Integer delayTime 延迟调转时间(单位毫秒)
 */
CF.prototype.redirectByAjaxRsp = function(delayTime) {
    if(!delayTime){
        delayTime = 0;
    }
    var oThis = this;
    window.setTimeout(function(){
        if (oThis.ajaxRsp.redirectUrl) {
            if (oThis.ajaxRsp.redirectTarget == '_self') {
                self.location.href = oThis.ajaxRsp.redirectUrl;
            } else if (oThis.ajaxRsp.redirectTarget == '_parent') {
                parent.location.href = oThis.ajaxRsp.redirectUrl;
            } else if (oThis.ajaxRsp.redirectTarget == '_top') {
                top.location.href = oThis.ajaxRsp.redirectUrl;
            }
        }
    },delayTime);
}

/**
 * 构造对应表单数据，用于异步提交
 *
 * @param  Object  oForm  表单对象
 * @return  String  返回对应的数据串
 */
CF.prototype.makeFormPostData = function(oForm) {
    var postData = '';
    for (var i = 0;i < oForm.elements.length;i ++) {
        //alert(oForm.elements[i].name + '=' +oForm.elements[i].type);
        if (oForm.elements[i].type == 'text' || oForm.elements[i].type == 'select' || oForm.elements[i].type == 'select-one' || oForm.elements[i].type == 'password' || oForm.elements[i].type == 'hidden' || oForm.elements[i].type == 'textarea') {
            postData += "&"+oForm.elements[i].name+"="+encodeURIComponent(oForm.elements[i].value);
        } else if (oForm.elements[i].type == 'checkbox' || oForm.elements[i].type == 'radio') {
            if (oForm.elements[i].checked) {
                postData += "&"+oForm.elements[i].name+"="+encodeURIComponent(oForm.elements[i].value);
            }
        }
    }
    return postData;
}

/**
 * 引用页面html元素
 *
 * @param  String  id  页面元素的id
 * @return Object
 */
CF.prototype.$ = function(id) {
    return document.getElementById(id);
}

/**
 * 全选/清除指定form中id以某种前缀开头的一组checkbox,并切换控制全选/清除的checkbox的勾选状态
 *
 * @param  Object  oCheckbox  控制全选/清除的checkbox对象
 * @param  Object  oForm  表单对象
 * @param  Mixed  prefix  一组checkbox的id前缀（或id前缀数组）（例如：ck_123的前缀就是ck_）
 */
CF.prototype.switchMark = function(oCheckBox, oForm, prefix) {
    if (typeof(prefix) == 'string') {   /* prefix是id前缀字符串 */
        var arr = new Array();  /* checkbox的id前缀数组 */
        arr[0] = prefix;
    } else if (typeof(prefix) == 'object') {    /* prefix是id前缀字符串数组 */
        arr = prefix;
    }
    var hasEmpty = false;   /* 为true表示有清空的checkbox标记，为false表示所有对应的checkbox都被选择 */
    var e = oForm.elements;
    f1 : for(var i=0;i<e.length;i++) {
        if (e[i] && e[i].type == "checkbox" && e[i].id != '') {
            for (var j in arr) {
                if (e[i].id.indexOf(arr[j]) == 0 && e[i].checked == false) {
                    hasEmpty = true;   /* 有清空的checkbox标记 */
                    break f1;
                }
            }
        }
    }
    if (hasEmpty) {
        for(var i=0;i<e.length;i++) {
            if (e[i] && e[i].type == "checkbox" && e[i].id != '') {
                for (var j in arr) {
                    if (e[i].id.indexOf(arr[j]) == 0) {
                        e[i].checked = true;
                        break;
                    }
                }
            }
        }
        oCheckBox.checked = true;
    } else {
        for(var i=0;i<e.length;i++) {
            if (e[i] && e[i].type == "checkbox" && e[i].id != '') {
                for (var j in arr) {
                    if (e[i].id.indexOf(arr[j]) == 0) {
                        e[i].checked = false;
                        break;
                    }
                }
            }
        }
        oCheckBox.checked = false;
    }    
}

/**
 * 全选/清除指定form中id以某种前缀开头的一组checkbox
 *
 * @param  Object  oForm  表单对象
 * @param  String  prefix  一组checkbox的id前缀（例如：ck_123的前缀就是ck_）
 */
CF.prototype.markAll = function(oForm, prefix) {
    var hasEmpty = false;   /* 为true表示有清空的checkbox标记，为false表示所有对应的checkbox都被选择 */
    var e = oForm.elements;
    for(var i=0;i<e.length;i++) {
        if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
            if (e[i].checked == false) {
                hasEmpty = true;   /* 有清空的checkbox标记 */
                break;
            }
        }
    }
    if (hasEmpty) {
        for(var i=0;i<e.length;i++) {
            if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
                e[i].checked = true;
            }
        }
    } else {
        for(var i=0;i<e.length;i++) {
            if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
                e[i].checked = false;
            }
        }
    }    
}

/**
 * 检测指定form中id以某种前缀开头的一组checkbox是否有至少一个被选择
 *
 * @param  Object  oForm  表单对象
 * @param  String  prefix  一组checkbox的id前缀
 * @return  Boolean  有至少一个被选择则返回true，否则返回false
 */
CF.prototype.hasAnyMark = function(oForm, prefix) {
    var e = oForm.elements;
    for(var i=0;i<e.length;i++) {
        if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
            if (e[i].checked == true) {
                return true;
                break;
            }
        }
    }
    return false;
}

/**
 * 将form中id以某种前缀开头的一组checkbox全部清空选择
 *
 * @param  Object  oForm  表单对象
 * @param  String  prefix  一组checkbox的id前缀
 */
CF.prototype.clearMark = function(oForm, prefix) {
    var e = oForm.elements;
    for(var i=0;i<e.length;i++) {
        if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
            e[i].checked = false;
        }
    }
}

/**
 * 返回指定form中id以某种前缀开头的已被勾选的一组checkbox的id（去掉前缀的）组成的数组
 *
 * @param  Object  oForm  表单对象
 * @param  String  prefix  一组checkbox的id前缀
 * @return  array  已被勾选的id数组
 */
CF.prototype.getMarkId = function(oForm, prefix) {
    var e = oForm.elements;
    var arr = new Array();
    for(var i=0;i<e.length;i++) {
        if (e[i] && e[i].type == "checkbox" && e[i].id != '' && e[i].id.indexOf(prefix) == 0) {
            if (e[i].checked == true) {
                var pos = prefix.length;
                var checkedId = e[i].id.substr(pos);
                arr[checkedId] = checkedId;
            }
        }
    }
    return arr;
}

/**
 * 使页面select元素中的option根据参数v联动
 *
 * @param  Object  oSelect  页面select元素对象
 * @param  Mixed  v  要联动的值
 * @return  Boolean  找到相应的值则返回true，否则返回false
 */
CF.prototype.switchSelect = function(oSelect, v) {
    for (var i = 0; i < oSelect.length; i ++) {
        if (oSelect.options[i].value == v) {
            oSelect.options[i].selected = true;
            return true;
        }
    }
    return false;
}

/**
 * 根据iframe的name获取iframe窗口对象
 *
 * @param  String  iframeName  iframe的name
 */
CF.prototype.getIframe = function(iframeName) {
    return window.frames[iframeName];
}

/**
 * 将html实体转换为对应的字符
 *
 * @param  String  str  要被转换的含html实体的字符串
 */
CF.prototype.htmlspecialcharsDecode = function(str) {
    str=str.replace(/&quot;/g, '"');
    str=str.replace(/&#039;/g, "'");
    str=str.replace(/&lt;/g, '<');
    str=str.replace(/&gt;/g, '>');
    str=str.replace(/&amp;/g, '&');
    return str;
}

/**
 * 将字符转换为对应的html实体
 *
 * @param  String  str  要被转换的字符
 */
CF.prototype.htmlspecialcharsEncode = function(str) {
    str=str.replace(/\&/g, '&amp;');
    str=str.replace(/\"/g, '&quot;');
    str=str.replace(/\'/g, "&#039;");
    str=str.replace(/\</g, '&lt;');
    str=str.replace(/\>/g, '&gt;');
    return str;
}

/**
 * 获取文件全名中不带后缀的文件名
 *
 * @param  String  fileFullName  文件全名（例如：f1.txt，运行这个函数后将返回f1）
 */
CF.prototype.getFileName = function(fileFullName) {
    if (fileFullName.lastIndexOf('.') != -1) {
        var fileName = fileFullName.substr(0, fileFullName.lastIndexOf('.'));
    } else {
        var fileName = fileFullName;
    }
    return fileName;
}

/**
 * 创建一个dom元素
 *
 * @param  String  elmName  元素名
 */
CF.prototype.crElm = function(elmName) {
    return document.createElement(elmName);
}

/**
 * 阻止事件传播
 *
 * @param  Object  e  事件对象
 */
CF.prototype.stopEventPropagation = function(e) {
    var e = event ? event : window.event;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation();
}

/**
 * 设置对象样式
 *
 * @param  Object  o  对象
 * @param  String  n  样式名
 * @param  String  v  样式值
 */
CF.prototype.setStyle = function(o, n, v) {
    o.style[n] = v;
}

/**
 * 设置对象的样式class
 *
 * @param  Object  o  对象
 * @param  String  n  class名
 */
CF.prototype.setClass = function(o, n) {
    o.className = n;
}

/**
 * 取消对象的样式class
 *
 * @param  Object  o  对象
 */
CF.prototype.delClass = function(o) {
    o.className = '';
}

/**
 * 添加事件处理给对象
 *
 * @param  Object  o  对象
 * @param  String  eventName  小写的事件名（例如：onclick,onload）
 * @param  String  funcName  处理函数名
 * @param  Boolean  flag  事件处理方式标记，false(bubbling，默认的)，true(capturing)
 */
CF.prototype.addEventHandle = function(o, eventName, funcName, flag) {
    if (!window.addEventListener) { /* ie浏览器 */
        o.attachEvent(eventName.toLowerCase(), funcName);
    } else {
        eventName = eventName.toLowerCase();
        o.addEventListener(eventName.substr(2), funcName, flag);
    }
}

/**
 * 删除对象的事件处理
 *
 * @param  Object  o  对象
 * @param  String  eventName  小写的事件名（例如：onclick,onload）
 * @param  String  funcName  处理函数名
 * @param  Boolean  flag  事件处理方式标记，false(bubbling，默认的)，true(capturing)
 */
CF.prototype.delEventHandle = function(o, eventName, funcName, flag) {
    if (!window.addEventListener) { /* ie浏览器 */
        o.detachEvent(eventName.toLowerCase(), funcName);
    } else {
        eventName = eventName.toLowerCase();
        o.removeEventListener(eventName.substr(2), funcName, flag);
    }
}

/** 
 * 获取元素的纵坐标
 *
 * @param Object o 需要获取的对象
 */    
CF.prototype.getTop = function(o){
    var offset=o.offsetTop;   
    if(o.offsetParent!=null) offset+=this.getTop(o.offsetParent);   
    return offset;   
}   

/** 
 * 获取元素的横坐标
 *
 * @param Object o 需要获取的对象
 */   
CF.prototype.getLeft = function(o){
    var offset=o.offsetLeft;   
    if(o.offsetParent!=null) offset+=this.getLeft(o.offsetParent);   
    return offset;   
}  

/** 
 * 获取元素的css样式 
 *
 * @param Object o 需要获取的对象
 * @param String stlName 需要获取的样式名
 */   
CF.prototype.getCssStyle = function(o,stlName){
    if(document.all){
        return o.currentStyle[stlName];
    } else {
        return window.getComputedStyle(o,null).getPropertyValue(stlName);
    }
}

var jCv = new CV();      /* 公共变量对象 */
var jCf = new CF();      /* 公共函数对象 */