<?php

$app_head_include = '';

// don't include it when the request was from inside app
$in_app = tt_getenv('HTTP_IN_APP');
$referer = tt_getenv('HTTP_REFERER');
if ($in_app || preg_match('#^https?://link.tapatalk.com#i', $referer))
    return;


if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']))
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
    $app_referer = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
else
    $app_referer = $board_url;


// for those forum system which can not add js in html body, please set $functionCallAfterWindowLoad as 1
$functionCallAfterWindowLoad = isset($functionCallAfterWindowLoad) && $functionCallAfterWindowLoad ? 1 : 0;

$app_ios_id_default = '307880732';      // Tapatalk 1, 585178888 for Tapatalk 2
$app_ios_hd_id_default = '307880732';   // Tapatalk 1, 481579541 for Tapatalk HD
$app_android_id_default = 'com.quoord.tapatalkpro.activity';

$app_location_url = isset($app_location_url) && preg_match('#^tapatalk://#i', $app_location_url) ? $app_location_url : 'tapatalk://';
$app_location_url_byo = str_replace('tapatalk://', 'tapatalk-byo://', $app_location_url);
$tapatalk_dir_url = isset($tapatalk_dir_url) && $tapatalk_dir_url ? $tapatalk_dir_url : './mobiquo';
$mobiquo_extension = isset($mobiquo_extension) && $mobiquo_extension ? $mobiquo_extension : 'php';
$app_forum_name = isset($app_forum_name) && $app_forum_name ? html_entity_decode($app_forum_name) : 'this forum';
$board_url = isset($board_url) ? preg_replace('#/$#', '', trim($board_url)) : '';

$app_ios_id = isset($app_ios_id) && intval($app_ios_id) ? intval($app_ios_id) : '';
$app_android_id = isset($app_android_id) && $app_android_id ? preg_replace('/^.*?details\?id=([^\s,&]+).*?$/si', '$1', $app_android_id) : '';
$app_kindle_url = isset($app_kindle_url) ? $app_kindle_url : '';
$app_banner_message = isset($app_banner_message) && $app_banner_message ? preg_replace('/\r\n|\n|\r/si', '<br />', $app_banner_message) : '';
$is_mobile_skin = isset($is_mobile_skin) && $is_mobile_skin ? 1 : 0;

// valid page_type: index, forum, topic, post, pm, search, profile, online, other
$page_type = isset($page_type) && $page_type ? $page_type : 'other';

$is_byo = $app_ios_id && $app_ios_id != -1 || $app_android_id && $app_android_id != -1 || $app_kindle_url && $app_kindle_url != -1 ? 1 : 0;

// add App Indexing for Google Search
$host_path = preg_replace('#tapatalk://#si', '', $app_location_url);
if (in_array($page_type, array('topic', 'post', 'home')) && $host_path)
{
    if ($app_android_id == $app_android_id_default || empty($app_android_id) || $app_android_id == -1)
    {
        $app_head_include .= '
        <!-- App Indexing for Google Search -->
        <link href="android-app://com.quoord.tapatalkpro.activity/tapatalk/'.$host_path.'" rel="alternate" />
        ';
    }
    
    if (!$is_byo)
    {
        $app_head_include .= '
        <meta property="al:android:package" content="'.$app_android_id_default.'" />
        <meta property="al:android:url" content="'.$app_location_url.'" />
        <meta property="al:android:app_name" content="Tapatalk" />
        <meta property="al:ios:url" content="'.$app_location_url.'" />
        <meta property="al:ios:app_store_id" content="'.$app_ios_id_default.'" />
        <meta property="al:ios:app_name" content="Tapatalk" />
        ';
    }
}

// display twitter card
$twitter_card_head = '';
if ($app_ios_id != -1 || $app_android_id != -1)
{
    $twitter_card_head .= '
        <!-- twitter app card start-->
        <!-- https://dev.twitter.com/docs/cards/types/app-card -->
        <meta name="twitter:card" content="app" />
    ';
    
    if ($app_ios_id != '-1')
    {
        $twitter_card_head .= '
        <meta name="twitter:app:id:iphone" content="'.($app_ios_id ? $app_ios_id : $app_ios_id_default).'" />
        <meta name="twitter:app:url:iphone" content="'.($app_ios_id ? $app_location_url_byo : $app_location_url).'" />
        <meta name="twitter:app:id:ipad" content="'.($app_ios_id ? $app_ios_id : $app_ios_hd_id_default).'" />
        <meta name="twitter:app:url:ipad" content="'.($app_ios_id ? $app_location_url_byo : $app_location_url).'" />
        ';
    };
        
    if ($app_android_id != '-1')
    {
        $twitter_card_head .= '
        <meta name="twitter:app:id:googleplay" content="'.($app_android_id ? $app_android_id : $app_android_id_default).'" />
        <meta name="twitter:app:url:googleplay" content="'.($app_android_id ? $app_location_url_byo : $app_location_url).'" />
        ';
    };
    
    $twitter_card_head .= '
    <!-- twitter app card -->
    ';
}

$app_head_include .= $twitter_card_head;

// don't include it when the request was not from mobile device
$useragent = tt_getenv('HTTP_USER_AGENT');
if (!preg_match('/iPhone|iPod|iPad|Silk|Android|IEMobile|Windows Phone|Windows RT.*?ARM/i', $useragent))
    return;

// don't show welcome page and banner for googlebot or twitterbot
if (preg_match('/googlebot|twitterbot/i', $useragent))
    return;

// display smart banner and welcome page
$app_banner_head = '';
if (file_exists(dirname(__FILE__) . '/appbanner.js') &&
    file_exists(dirname(__FILE__) . '/app.php') &&
    file_exists(dirname(__FILE__) . '/appbanner.css'))
{
    $app_banner_head = '
        <!-- Tapatalk Banner&Welcome head start -->
        <link href="'.$tapatalk_dir_url.'/smartbanner/appbanner.css" rel="stylesheet" type="text/css" media="screen" />
        <script type="text/javascript">
            var is_byo             = '.$is_byo.';
            var is_mobile_skin     = '.$is_mobile_skin.';
            var app_ios_id         = "'.$app_ios_id.'";
            var app_android_id     = "'.addslashes($app_android_id).'";
            var app_kindle_url     = "'.addslashes(urlencode($app_kindle_url)).'";
            var app_banner_message = "'.addslashes($app_banner_message).'";
            var app_forum_name     = "'.addslashes($app_forum_name).'";
            var app_location_url   = "'.addslashes($app_location_url).'";
            var app_board_url      = "'.addslashes($board_url).'";
            var mobiquo_extension  = "'.addslashes($mobiquo_extension).'";
            var functionCallAfterWindowLoad = '.$functionCallAfterWindowLoad.';
            
            var app_api_key        = "'.(trim($api_key) ? md5(trim($api_key)) : '').'";
            var app_referer        = "'.addslashes($app_referer).'";
            var tapatalk_dir_name  = "'.addslashes(basename(dirname(dirname(__FILE__)))).'";
            var app_welcome_enable = '.(!isset($app_ads_enable) || $app_ads_enable ? 1 : 0).';
            var app_banner_enable  = '.(!isset($app_banner_enable) || $app_banner_enable ? 1 : 0).';
        </script>
        <script src="'.$tapatalk_dir_url.'/smartbanner/appbanner.js" type="text/javascript"></script>
        <!-- Tapatalk Banner head end-->
    ';
}

$app_head_include .= $app_banner_head;

function tt_getenv($key)
{
    $return = '';

    if ( is_array( $_SERVER ) && isset( $_SERVER[$key] ) && $_SERVER[$key])
    {
        $return = $_SERVER[$key];
    }
    else
    {
        $return = getenv($key);
    }

    return $return;
}