<?php

defined('MBQ_IN_IT') or exit;

/**
 * user custom config,to replace some config of MbqMain::$oMbqConfig->cfg.
 * you can change any config if you need,please refer to MbqConfig.php for more details.
 * this file used for json api now
 * 
 * @since  2013-5-7
 * @author Wu ZeTao <578014287@qq.com>
 */
MbqMain::$customConfig['base']['version'] = 'vn20_1.5.1';
MbqMain::$customConfig['forum']['offline'] = MbqBaseFdt::getFdt('MbqFdtConfig.forum.offline.range.no');
MbqMain::$customConfig['forum']['private'] = MbqBaseFdt::getFdt('MbqFdtConfig.forum.private.range.no');

?>