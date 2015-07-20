<?php
/**
 * {tplEchoUrl} function plugin
 * 对CF::echoUrl()方法的封装，实现显示对应的url
 * 调用方法与CF::echoUrl()类似：
 * {tplEchoUrl appName=$v1 mainName=$v2 cmd=$v3 getParams="array('v1' => 'test1')" vName='vName' vValue=$vValue vName1='vName1' vValue1=$vValue1 vName2='vName2' vValue2=$vValue2 ... getAbsoluteUrl=true}
 * 其中getParams中的参数只能是确定的数值或字符串，而不能是变量，如果要用变量需要赋到对应的vName/vValue对，目前支持最多101个vName/vValue对，即从vName/vValue到vName100/vValue100 getAbsoluteUrl参数标记是否返回完整地址
 * 
 * @since  2010-1-1
 * @author Wu ZeTao <578014287@qq.com>
 * @param array
 * @param Smarty
 */
function smarty_function_tplEchoUrl($params, &$smarty) {
    if ($params['getParams']) {
        $getParams = eval($params['getParams']);
        if (!is_array($getParams)) {
            $getParams = array();
        }
    } else {
        $getParams = array();
    }
    if ($params['vName']) {
        $getParams[$params['vName']] = $params['vValue'];
    }
    for ($i = 1;$i <= 100;$i ++) {
        if ($params['vName'.$i]) {
            $getParams[$params['vName'.$i]] = $params['vValue'.$i];
        }
    }
    if ($params['getAbsoluteUrl']) {
        $getAbsoluteUrl = true;
    } else {
        $getAbsoluteUrl = false;
    }
    MainApp::$oCf->echoUrl($params['appName'], $params['mainName'], $params['cmd'], $getParams, $getAbsoluteUrl);
}
?>
