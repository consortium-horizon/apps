<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="manifest" href="manifest.json">
  {asset name="Head"}
</head>
<body id="{$BodyID}" class="{$BodyClass}">
<div id="Frame">
  <div class="Top">
    <div class="Row">
      <div class="TopMenu">
        <!--
        You can add more of your top-level navigation links like this:

        <a href="#">Store</a>
        <a href="#">Blog</a>
        <a href="#">Contact Us</a>
        -->
      </div>
   </div>
  </div>
  <div class="Banner">
    <div class="Row">
      <strong class="SiteTitle">
        <a href="{link path="/"}">
          {logo}
        </a>
      </strong>
      <!--
      We've placed this optional advertising space below. Just comment out the line and replace "Advertising Space" with your 728x90 ad banner.
      -->
      <!-- <div class="AdSpace">Advertising Space</div> -->
    </div>
  </div>
  <div id="Head">
    <div class="Row">
      <div class="SiteSearch">{searchbox}</div>
      <ul class="SiteMenu">
      	<li>
      		<a href="http://www.consortium-horizon.com/">
        		Accueil
        	</a>
      	</li>
        <li>
        	<a href="{link path="/"}">
        		Forum
        	</a>
        </li>
        {dashboard_link}
        <li class="dropdown">
          La guilde
          <ul>
            <li>
            	<a href="http://www.consortium-horizon.com/forum/organigramme">
    				Organigramme
            	</a>
            </li>
            <li>
            	<a href="http://www.consortium-horizon.com/forum/page/presentation-de-la-guilde">
    				La Charte
            	</a>
            </li>
            <li>
	            <a href="https://www.youtube.com/channel/UCEk_pNq59GlK2PNW6zNAZqQ">
	            	Sur YouTube
	            </a>
            </li>
            <li>
	            <a href="https://www.consortium-horizon.com/wiki/Accueil">
	            	Notre Wiki
	            </a>
            </li>
            <li>
            <a href="#" onClick="MyWindow=window.open('http://www.consortium-horizon.com/chat/','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=1000,height=600,left=300,top=300'); return false;">Chat</a>
            </li>
          </ul>
        </li>
        {discussions_link}
        {custom_menu}
        {profile_link}
        {signinout_link}
      </ul>
    </div>
  </div>
  <div class="BreadcrumbsWrapper">
    <div class="Row">
     {breadcrumbs}
    </div>
  </div>

  <div id="Body">
    <div class="Row">
      <div class="Column PanelColumn" id="Panel">
         {module name="MeModule"}
         {asset name="Panel"}
      </div>
      <div class="Column ContentColumn" id="Content">
        <!-- Planetside 2 module -->
        {if $Path=='categories/planetside-2'}
            {planetside_online}
        {/if}
        {asset name="Content"}
      </div>
      <!-- Fix for container height -->
      <div style="clear: both"></div>
    </div>
  </div>
  <div id="Foot">
    <div class="Row">
      <div class="Content">
        Le Consortium Horizon - LCH
      </div>
      {asset name="Foot"}
    </div>
  </div>
</div>
{event name="AfterBody"}
</body>
</html>
