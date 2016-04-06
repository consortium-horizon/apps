{*
 * This theme was ported from Vanilla Forums Inc's hosted theme "Bootstrap" it has been modified to merge with Bootstrap 3 ( from 2.3 ) and has been designed for use on the OS model.
 *
 * @copyright Vanilla Forums
 * @author Chris Ireland (Adapation)
*}
<!DOCTYPE html>
<html>
<head>
    {asset name="Head"}
</head>
<body id="{$BodyID}" class="{$BodyClass}">
<div id="Frame">
    <div class="NavBar">
        <div class="Row">
            <strong class="SiteTitle"><a href="{link path="/"}">{logo}</a></strong>

            <div class="MeWrap">
               {module name="MeModule" CssClass="Inline FlyoutRight"}
            </div>
            <ul class="SiteMenu">
                {discussions_link}
                {activity_link}
                {custom_menu}
            </ul>
        </div>
        <a id="Menu" href="#sidr" data-reveal-id="sidr"><span class="icon"></span><span class="icon"></span><span
                    class="icon"></span>
            <noscript> Mobile Menu Disabled with Javascript disabled</noscript>
        </a>

        <div id="sidr" class="sidr">
            <strong class="SiteTitle"><a href="{link path="/"}">{logo}</a></strong>
            {dashboard_link wrap="li class='dashboard'"}
            {discussions_link wrap="li class='discussions'"}
            {activity_link wrap="li class='activity'"}
            {inbox_link wrap="li class='inbox'"}
            {custom_menu}
            {profile_link wrap="li class='profile'"}
            {signinout_link wrap="li class='signout'"}
            </ul>
        </div>
    </div>
    <div id="Body">
        <div class="BreadcrumbsWrapper Row">
            <div class="SiteSearch">{searchbox}</div>
            {breadcrumbs}
        </div>
        <div class="Row">
            <div class="Column PanelColumn" id="Panel">
               {asset name="Panel"}
            </div>
            <div class="Column ContentColumn" id="Content">
		{asset name="Content"}
	    </div>
        </div>
    </div>
    <div id="Foot">
        <div class="Row">
            <a href="{vanillaurl}" class="PoweredByVanilla" title="Community Software by Vanilla Forums">Powered by
                Vanilla</a>
            {asset name="Foot"}
        </div>
    </div>
</div>
{event name="AfterBody"}
{literal}
    <script>
        // Theme Defintions
        jQuery("#Menu").sidr();
        $('.SignInPopup').click(function () {
            jQuery.sidr('close');
        });

        if ($(window).width() < 612) {
            $(".Options").addClass("FlyoutLeft");
            $("body.Discussion .Options").removeClass("FlyoutLeft");
        }

        $(window).resize(function () {
            if ($(window).width() > 612) {
                jQuery.sidr('close');
                $(".Options").removeClass("FlyoutLeft");
            }
            else if ($(window).width() < 612) {
                $(".Options").addClass("FlyoutLeft");
                $("body.Discussion .Options").removeClass("FlyoutLeft");
            }
        });
    </script>
{/literal}
</body>
</html>