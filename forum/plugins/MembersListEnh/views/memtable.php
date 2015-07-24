<?php if(!defined('APPLICATION')) die();


 $Session = Gdn::Session();
 if     (!((CheckPermission('Plugins.MembersListEnh.GenView')) ||
        (CheckPermission('Plugins.MembersListEnh.IPEmailView')))) die();
    

     $RequestArgs = Gdn::Request()->GetRequestArguments();
     
     $UserField = GetValue('ufield',$RequestArgs['get']);
  
    
    if (!$UserField)
         $UserField = 'UserID'; 
  
    
   $SortOrder = GetValue('sort',$RequestArgs['get']);
   
   
    if (!$SortOrder) 
         $SortOrder = 'asc';
    
    
    $Limit = Gdn::Config("Plugins.MembersListEnh.DCount");
    
   
   if (!is_numeric($Limit) || $Limit < 0)
       $Limit = 20;
       
     $MLUrl = $this->SelfUrl; 
     
     $Arg = $MLUrl;
     $MColUrl = 'members/p1';
     
     $Page = 1;
    
    if (preg_match("|p(\d+)|", $Arg,$matches)){
                  $Page = $matches[1];
    }
    
    $SortOrder = strtolower($SortOrder);
    if (($SortOrder == 'asc') ||(!$SortOrder)) {
            $NewSort = 'desc';
            $SortOrder = 'asc';
    } else {
            $NewSort = 'asc';
            $SortOrder = 'desc';
            }
   
  
     switch (strtolower($UserField))
       {
       case 'userid':
            $UserField  = 'UserId';
            break;
       case 'name':
            $UserField  = 'Name';
           break;     
      case 'userid':
            $UserField  = 'UserId';
           break;   
      case 'symboluser':
            $UserField  = 'SymbolUser';
           break;       
      case 'balance':
            $UserField  = 'Balance';
           break;  
      case 'receivedthankcount':
            $UserField  = 'ReceivedThankCount';
           break;  
     case 'reaction1':
            $UserField  = 'PeregrineReactOne';
           break;     
     case 'reaction2':
            $UserField  = 'PeregrineReactTwo';
           break;  
     case 'reaction3':
            $UserField  = 'PeregrineReactThree';
           break;  
     case 'reaction4':
            $UserField  = 'PeregrineReactFour';
           break;  
    case 'liked':
            $UserField  = 'Liked';
           break;  
     case 'qnacountacceptance':
            $UserField = 'QnACountAcceptance';
            break; 
      case 'email':
            $UserField  = 'Email';
           break;
      case 'countvisits':
            $UserField  = 'CountVisits';
           break;
      case 'datefirstvisit':
            $UserField  = 'DateFirstVisit';
           break;
      case 'datelastactive':
            $UserField  = 'DateLastActive';
           break;
      case 'lastipaddress':
            $UserField  = 'LastIPAddress';
           break;
      case 'countdiscussions':
            $UserField  = 'CountDiscussions';
           break;
      case 'countcomments':
            $UserField  = 'CountComments';
           break;
      default:
          $UserField  = 'Name';
      }
    



$ShowPhoto   =  Gdn::Config("Plugins.MembersListEnh.ShowPhoto");
$ShowID      =  Gdn::Config("Plugins.MembersListEnh.ShowID");
$ShowRoles   =  Gdn::Config("Plugins.MembersListEnh.ShowRoles");
$ShowFVisit  =  Gdn::Config("Plugins.MembersListEnh.ShowFVisit");
$ShowLVisit  =  Gdn::Config("Plugins.MembersListEnh.ShowLVisit");
$ShowEmail   =  Gdn::Config("Plugins.MembersListEnh.ShowEmail");
$ShowIP      =  Gdn::Config("Plugins.MembersListEnh.ShowIP");
$ShowVisits  =  Gdn::Config("Plugins.MembersListEnh.ShowVisits");
$ShowDiCount =  Gdn::Config("Plugins.MembersListEnh.ShowDiCount");
$ShowCoCount =  Gdn::Config("Plugins.MembersListEnh.ShowCoCount");
$ShowLike =  Gdn::Config("Plugins.MembersListEnh.ShowLike");
$ShowThank =  Gdn::Config("Plugins.MembersListEnh.ShowThank");
$ShowAnswers = Gdn::Config("Plugins.MembersListEnh.ShowAnswers");
$ShowPeregrineReactions = Gdn::Config("Plugins.MembersListEnh.ShowPeregrineReactions");
$ShowSymbol= Gdn::Config("Plugins.MembersListEnh.ShowSymbol");
$ShowKarma = "";
 if  (Gdn::Config('EnabledPlugins.KarmaBank') == TRUE) {
    $ShowKarma =  Gdn::Config("Plugins.MembersListEnh.ShowKarma");
     }

$this->AddCssFile('ml.css', 'plugins/MembersListEnh');?>


<h1><?php echo T('Members List Enhanced'); ?></h1>
<table id="Users" class="AltColumns" style="width: 100%;">
   <thead>
      <tr class="Info">
       <?php 
       if ($ShowPhoto)
       echo  '<th>' . Anchor(T('Photo'),$MColUrl . '&ufield=' . 'Name' . '&sort=' . $NewSort ) .'</th>';
       echo  '<th>' . Anchor(T('Username'),$MColUrl . '&ufield=' . 'Name' . '&sort=' . $NewSort ) .'</th>';
      if ($ShowSymbol)
            echo  '<th>' . Anchor(T('SymbolUser'),$MColUrl . '&ufield=' . 'SymbolUser' . '&sort=' . $NewSort ) .'</th>';
       if ($ShowThank)
       echo  '<th>' . Anchor(T('Thanks'),$MColUrl . '&ufield=' . 'ReceivedThankCount' . '&sort=' . $NewSort )  . '</th>';
       if ($ShowKarma)
       echo  '<th>' .Anchor(T('Karma'),$MColUrl . '&ufield=' . 'Balance' . '&sort=' . $NewSort ) . '</th>';
        if ($ShowLike)
       echo  '<th>' .Anchor(T('Likes'),$MColUrl . '&ufield=' . 'Liked' . '&sort=' . $NewSort ) . '</th>';
        if ($ShowPeregrineReactions) {
       echo  '<th>' .Anchor(T('Reaction1mt'),$MColUrl . '&ufield=' . 'Reaction1' . '&sort=' . $NewSort ) . '</th>';
       echo  '<th>' .Anchor(T('Reaction2mt'),$MColUrl . '&ufield=' . 'Reaction2' . '&sort=' . $NewSort ) . '</th>';
       echo  '<th>' .Anchor(T('Reaction3mt'),$MColUrl . '&ufield=' . 'Reaction3' . '&sort=' . $NewSort ) . '</th>';
       echo  '<th>' .Anchor(T('Reaction4mt'),$MColUrl . '&ufield=' . 'Reaction4' . '&sort=' . $NewSort ) . '</th>';
       }
       if ($ShowAnswers)
        echo '<th>' .Anchor(T('Answers'),$MColUrl . '&ufield=' . 'QnACountAcceptance' . '&sort=' . $NewSort ) . '</th>';      
        if ($ShowID)
       echo  '<th>' . Anchor(T('User ID'),$MColUrl. '&ufield=' . 'UserID' . '&sort=' . $NewSort ).'</th>';
       if  ($ShowRoles)
       echo  '<th>' . T('Roles').'</th>';
       if  ($ShowFVisit)
       echo  '<th>' . Anchor(T('First Visit'),$MColUrl. '&ufield=' . 'DateFirstVisit' . '&sort=' . $NewSort ).'</th>';
       if  ($ShowLVisit)
       echo  '<th>' . Anchor(T('Last Visit'),$MColUrl. '&ufield=' . 'DateLastActive' . '&sort=' . $NewSort ).'</th>';
        // if  (($ShowIP) && ($Session->CheckPermission('Plugins.MembersListEnh.IPEmailView')))
        if  (($ShowEmail) && ($Session->CheckPermission('Garden.Users.Add')))
       echo  '<th>' . Anchor(T('Email'),$MColUrl. '&ufield=' . 'Email' . '&sort=' . $NewSort ).'</th>';
       if  (($ShowIP) && ($Session->CheckPermission('Garden.Users.Add')))
       echo  '<th>' . Anchor(T('Last IP Address'),$MColUrl. '&ufield=' . 'LastIPAddress' . '&sort=' . $NewSort ).'</th>';
       if  ($ShowVisits)
       echo  '<th>' . Anchor(T('Visits'),$MColUrl. '&ufield=' . 'CountVisits' . '&sort=' . $NewSort ).'</th>';
       if  ($ShowDiCount)
       echo '<th>' . Anchor(T('Discussion Count'),$MColUrl. '&ufield=' . 'CountDiscussions' . '&sort=' . $NewSort ).'</th>';
          if  ($ShowCoCount)
       echo '<th>' . Anchor(T('Comment Count'),$MColUrl. '&ufield=' . 'CountComments' . '&sort=' . $NewSort ).'</th>';
       echo   '</tr></thead><tbody>';
    $Alt = FALSE;
 

    $Sender->Offset = ($Page * $Limit) - $Limit ;
    $Offset = $Sender->Offset;
  

   $this->MembersListEnh = MembersListEnhModel::GetMembersListEnh($Limit, $Offset,$SortOrder,$UserField);
   $MemNumRows = MembersListEnhModel::GetMembersCount();
   
  
   
   $mydata = $this->MembersListEnh;
    

        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $this);
        $Sender->Pager->MoreCode = '>';
        $Sender->Pager->LessCode = '<';
        $Sender->Pager->ClientID = 'Pager';
     
        $Sender->Pager->Configure(
        $Sender->Offset,
        $Limit,
        $MemNumRows,
         'members' . '/{Page}' . '&sort=' . $SortOrder . '&ufield=' .$UserField
        );
       
 
        
        $mypager = $Sender->Pager;

      
    echo $Sender->Pager->ToString('more');
 
   $symboldir = C('Plugins.SymbolEdit.SymbolDir', 'CZodiac');
  
   foreach ($mydata as $User) {
     $Alt = $Alt ? FALSE : TRUE;
     ?>
     <tr<?php echo $Alt ? ' class="Alt"' : ''; ?>>
      <?php  
         if ($ShowPhoto)
         echo '<td>' .UserPhoto($User,array('LinkClass'=>'ProfilePhotoCategory','ImageClass'=>'ProfilePhotoMedium')) . '</td>';
        
        
         echo '<td>' . UserAnchor($User). '</td>';  
 
         if ($ShowSymbol) {
           $symbolimage = "plugins/SymbolEdit/design/images/" . $symboldir . "/" . $User->SymbolUser . ".png";
                if (file_exists($symbolimage)) {
                  echo '<td>' . Img($symbolimage, array('alt' => 'Symbol', 'title' => '', 'class' => "ProfilePhotoMedium")) .'</td>'; 
                } else {
                echo '<td></td>';   
                }
            }
            
            
        if ($ShowThank) { 
           $Anchor = '/profile/receivedthanks/'. $User->UserID .'/'. $User->Name; 
           echo '<td>' . Anchor($User->ReceivedThankCount,$Anchor) . '</td>'; } 
      
        if ($ShowKarma) { 
           $Anchor = '/profile/karmabank/'. $User->UserID .'/'. $User->Name; 
           echo '<td>' . Anchor($User->Balance,$Anchor) . '</td>'; }  
         
         if ($ShowLike)
         echo '<td>' . $User->Liked . '</td>';
        
         if ($ShowPeregrineReactions) {
            echo '<td>' . $User->PeregrineReactOne . '</td>'; 
            echo '<td>' . $User->PeregrineReactTwo . '</td>'; 
            echo '<td>' . $User->PeregrineReactThree . '</td>'; 
            echo '<td>' . $User->PeregrineReactFour . '</td>'; 
            }
         if ($ShowAnswers)
         echo '<td>' . $User->QnACountAcceptance . '</td>';    
        
         if ($ShowID)
         echo '<td>' . $User->UserID . '</td>';
         if  ($ShowRoles) {
           $Roles = GetValue('Roles', $User, array());
           $RolesString = '';

           if ($User->Banned && !in_array('Banned', $Roles)) {
              $RolesString = T('Banned');
           }
          echo '<td>';
           foreach ($Roles as $RoleID => $RoleName) {
              $RolesString = ConcatSep(', ', $RolesString, htmlspecialchars($RoleName));
           }
          echo $RolesString . '</td>';
         }
         
           if  ($ShowFVisit)
         echo  '<td>' . Gdn_Format::Date($User->DateFirstVisit, 'html') . '</td>';
         
          if  ($ShowLVisit)
         echo  '<td>' . Gdn_Format::Date($User->DateLastActive, 'html') . '</td>';
         
         if  (($ShowEmail) && ($Session->CheckPermission('Garden.Users.Add')))
         echo  '<td>' . $User->Email  . '</td>'; 
        
         if  (($ShowIP) && ($Session->CheckPermission('Garden.Users.Add')))
         echo  '<td>' . $User->LastIPAddress . '</td>'; 
        
         if  ($ShowVisits)
         echo  '<td>' . $User->CountVisits . '</td>'; 
         
         if  ($ShowDiCount){
         $Anchor = '/profile/discussions/'. $User->UserID .'/'. $User->Name;
         echo  '<td>' . Anchor($User->CountDiscussions,$Anchor) . '</td>'; 
         }
          if  ($ShowCoCount){
         $Anchor = '/profile/comments/'. $User->UserID .'/'.$User->Name;
         echo  '<td>' . Anchor($User->CountComments,$Anchor) . '</td>'; 
         }
     
         echo '</tr>';
     }
  
        echo  '</tbody></table>'; 
        echo "</br>";
        echo $Sender->Pager->ToString('more');

