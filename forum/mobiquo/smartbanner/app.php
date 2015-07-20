<?php

defined('IN_MOBIQUO') or exit;
error_reporting(0);

$title = isset($_GET['name']) ? $_GET['name'] : 'Stay in touch with us via Tapatalk app';
$name = isset($_GET['name']) ? $_GET['name'] : 'online forums';
$board_url = isset($_GET['board_url']) ? $_GET['board_url'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';
$referer = isset($_GET['referer']) ? $_GET['referer'] : '';
$redirect_url = $referer ? $referer : ($board_url ? $board_url : dirname(dirname(dirname($_SERVER['REQUEST_URI']))));
$deeplink = isset($_GET['deeplink']) ? $_GET['deeplink'] : $board_url;
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!preg_match('#^https?://#si', $redirect_url)) $redirect_url = '/';

$banner_image_path = 'smartbanner/images/';
$image_list = array(
    'wrt-v-bg.jpg', 'wrt-h-bg.jpg',
    'wp-v-bg.jpg', 'wp-h-bg.jpg',
    'pad-v-bg.jpg', 'pad-h-bg.jpg',
    'iphone-v-bg.jpg', 'iphone-h-bg.jpg',
    'ipad-v-bg.jpg', 'ipad-h-bg.jpg',
    'android-v-bg.jpg', 'android-h-bg.jpg',
    'close.png', 'logo.png'
);

foreach($image_list as $image)
{
    if (!file_exists('smartbanner/images/'.$image))
    {
        $banner_image_path = 'https://s3.amazonaws.com/welcome-screen/images/';
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo htmlspecialchars($title); ?></title>
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/welcome-screen/welcome_screen.css"/>
<script type="text/javascript">
    var banner_image_path = '<?php echo addslashes($banner_image_path); ?>';
    var forum_name = '<?php echo addslashes(htmlspecialchars($name)); ?>';
    var app_api_key = '<?php echo addslashes(htmlspecialchars($code)); ?>';
    var app_deep_link = '<?php echo addslashes(htmlspecialchars($deeplink)); ?>';
    var banner_redirect_url = '<?php echo addslashes(htmlspecialchars($redirect_url))?>';
    
    // ---- tapstream track ----
    var _tsq = _tsq || [];
    _tsq.push(["setAccountName", "tapatalk"]);
    _tsq.push(["setPageUrl", document.location.protocol + "//" + document.location.hostname]);
    // "key" and "value" will appear as custom_parameters in the JSON that the app receives
    // from the getConversionData callback. Set it to something like "forum-id", "123456".
    _tsq.push(["addCustomParameter", "key", app_api_key]);
    _tsq.push(["addCustomParameter", "referer", app_deep_link]);
    // The logic below attaches a Tapstream session ID to your Tapstream campaign links.
    // This is critical for chaining the impression on the forum domain to the click
    // on your Tapstream custom domain.
    _tsq.push(["attachCallback", "init", function(cbType, sessionId){
    var links = document.getElementsByTagName('a');
    var tsidTemplate = '$TSID';
    for (var x = 0; x < links.length; x++){
        var link = links[x];
        if (link.href.indexOf(tsidTemplate) != -1 ){
            link.href = link.href.replace(tsidTemplate, sessionId);
        }
    }
    }]);
    _tsq.push(["trackPage"]);
    (function() {
        function z(){
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.async = "async";
            // Change the second string below (tapatalk.com/your-proxiy-URL.js) to a location you control
            // that proxies the Tapstream JavaScript URL. The Tapstream JavaScript is available at
            // http(s)://cdn.tapstream.com/static/js/tapstream.js
            s.src = window.location.protocol + "//s3.amazonaws.com/welcome-screen/tapstream.js";
            var x = document.getElementsByTagName("script")[0];
            x.parentNode.insertBefore(s, x);
        }
        if (window.attachEvent)
            window.attachEvent("onload", z);
        else
            window.addEventListener("load", z, false);
    })();
</script>
<script type="text/javascript" src="https://s3.amazonaws.com/welcome-screen/welcome2.js" charset="UTF-8"></script>
<script>
    $(document).ready(function()
    {
        $("body").html(body);
        check_device();
        // Detect whether device supports orientationchange event, otherwise fall back to
        // the resize event.
        var supportsOrientationChange = "onorientationchange" in window,
            orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
          
        window.addEventListener(orientationEvent, function() {
            check_device();
            $("#close_icon img").click(function() {
                localStorage.hide = true;
                window.location.href='<?php echo addslashes(htmlspecialchars($redirect_url))?>';
            });
        }, false);

        $("#web_bg img").css("max-height",$(window).height() + 'px');
        //$("body").height(($(window).height()*2- $(document).height() )+ 'px');
        $("#close_icon img").click(function() {
            localStorage.hide = true;
            window.location.href='<?php echo addslashes(htmlspecialchars($redirect_url))?>';
        });
        /*
        $("#button a").attr("href","https://tapatalk.com/m?id=23&referer=<?php echo urlencode($redirect_url)?>");
        */
        $("#button a").attr("href", 'http://tapstream.tapatalk.com/lzzq-1/?__tsid=$TSID&__tsid_override=1&referer='+encodeURIComponent(app_deep_link));
    })
</script>
</head>
<body scroll="no">
</body>
</html>