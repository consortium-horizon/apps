<!--  /!\   NO JAVASCRIPT IN THIS PAGE   /!\  -->

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="manifest" href="manifest.json">

  <!-- jQuery : link for lastest version here https://code.jquery.com/
  <script src="/forum/themes/LCH - Le consortium Horizon/js/jquery-1.12.4.min.js"></script> -->

  <!-- Ajaxify activation : https://github.com/arvgta/ajaxify -->
  <!-- <script src="/forum/themes/LCH - Le consortium Horizon/js/ajaxify.min.js"></script> -->
  <!-- <script src="/forum/themes/LCH - Le consortium Horizon/js/ajaxify-config.js"></script> -->

  <!-- Ajaxify activation -->
  <!--
    <link rel="stylesheet" type="text/css" href="/forum/themes/LCH - Le consortium Horizon/design/nprogress.css">
    <script src="//balupton.github.io/jquery-scrollto/lib/jquery-scrollto.js"></script>
    <script src="/forum/themes/LCH - Le consortium Horizon/js/nprogress.js"></script>
    <script src="//browserstate.github.io/history.js/scripts/bundled/html4+html5/jquery.history.js"></script>
    <script src="/forum/themes/LCH - Le consortium Horizon/js/ajaxify-html5.js?v=2.2"></script>
  -->
  <!-- / Ajaxify activation -->


  <!-- Candy activation -->
  <!--
    <link rel="stylesheet" type="text/css" href="/chat/res/embedded.css" />
    <link rel="stylesheet" type="text/css" href="/chat/roomPanel/default.css" />
    <script type="text/javascript" src="/chat/libs.min.js"></script>
    <script type="text/javascript" src="/chat/candy.min.js"></script>
    <script type="text/javascript" src="/chat/roomPanel/roomPanel.js"></script>
  -->
  <!-- Candy activation -->

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
      <div class="SiteSearch">{searchbox}</div> <!-- DON'T active searchbox_advanced -->
      {module name="MeModule"}
      {module name="NewDiscussionModule"}
      <ul class="SiteMenu">
        <li class="dropdown">
          Forum
          <ul>
            <li><a href="{link path="/"}">Accueil du forum</a></li>
            <li><a href="{link path="/discussions"}">Sujets récents</a></li>
            <li><a href="{link path="/discussions/unread"}">Sujets non lus</a></li>
            <li><a href="{link path="/discussions/unanswered"}">Sujets sans réponse</a></li>
            {activity_link}
            <li><a href="{link path="/best"}">Meilleur contenu</a></li>
            <li><hr></li>
            <li><a href="{link path="/discussions/mine"}">Mes sujets</a></li>
            <li><a href="{link path="/drafts"}">Mes brouillons</a></li>
            <li><hr></li>
            {custom_menu}
            {dashboard_link}
          </ul>
        </li>
        <li><a href="https://www.consortium-horizon.com/wiki/Accueil" target="_blank">Wiki</a></li>
        <li class="dropdown">
          Guilde
          <ul>
            <li><a href="{link path="/page/presentation-de-la-guilde"}">Présentation &amp; Charte</a></li>
            <li><a href="{link path="/organigramme"}">Organigramme</a></li>
            <li><a href="{link path="/page/outils-et-tutos"}">Outils &amp; Tutos</a></li>
          </ul>
        </li>
        <li class="dropdown">
          Réseaux sociaux
          <ul>
            <li><a href="https://steamcommunity.com/groups/consortium-horizon" target="_blank">Steam</a></li>
            <li><a href="https://www.youtube.com/channel/UCEk_pNq59GlK2PNW6zNAZqQ" target="_blank">YouTube</a></li>
            <li><a href="https://www.twitch.tv/lchorizon" target="_blank">Twitch</a></li>
            <li><a href="https://www.facebook.com/LeConsortiumHorizon" target="_blank">Facebook</a></li>
            <li><a href="https://twitter.com/LCHorizon" target="_blank">Twitter</a></li>
          </ul>
        </li>
        <li>
          <a href="#" onClick="MyWindow=window.open('http://www.consortium-horizon.com/chat/','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=1000,height=600,left=300,top=300'); return false;">Chat</a>
        </li>
        <!--
          <li class="dropdown">
            Mon profil
            <ul>
              {profile_link}
              {inbox_link}
              {signinout_link}
            </ul>
          </li>
        -->
      </ul>
    </div>
  </div>
  
  <div id="Breadcrumbs" class="BreadcrumbsWrapper">
    <div class="Row">
     {breadcrumbs}
    </div>
  </div>
  

  <div id="Body">
    <div class="Row">
      {module name="DiscussionEventModule" Limit=4}
      <!-- liste des sections (appelées par Gdn_Theme::section
        ActivityList : 
        ArticleList : 
        CategoryList : page forum
        CategoryArticleList : 
        CategoryDiscussionList : 
        Comments : 
        Conversation : 
        ConversationList : messagerie
        Dashboard :
        Dicussion : 
        DiscussionList : dans un forum particulier
        Entry : 
        Error : 
        EditProfile : 
        PostConversation : 
        PostDiscussion : 
        Profile : 
        SearchResults : 

        syntax : if InSection("CategoryList")
      -->
      <div class="Column PanelColumn" id="Panel">
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
