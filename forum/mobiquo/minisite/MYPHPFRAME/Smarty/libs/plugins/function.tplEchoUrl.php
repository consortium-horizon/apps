<?php
/**
 * {tplEchoUrl} function plugin
 * ��CF::echoUrl()�����ķ�װ��ʵ����ʾ��Ӧ��url
 * ���÷�����CF::echoUrl()���ƣ�
 * {tplEchoUrl appName=$v1 mainName=$v2 cmd=$v3 getParams="array('v1' => 'test1')" vName='vName' vValue=$vValue vName1='vName1' vValue1=$vValue1 vName2='vName2' vValue2=$vValue2 ... getAbsoluteUrl=true}
 * ����getParams�еĲ���ֻ����ȷ������ֵ���ַ������������Ǳ��������Ҫ�ñ�����Ҫ������Ӧ��vName/vValue�ԣ�Ŀǰ֧�����101��vName/vValue�ԣ�����vName/vValue��vName100/vValue100 getAbsoluteUrl��������Ƿ񷵻�������ַ
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
