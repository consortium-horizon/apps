<?php

# click stats listing users
require_once dirname(__FILE__).'/accesscheck.php';

if (isset($_GET['msgid'])) {
  $msgid = sprintf('%d',$_GET['msgid']);
} else {
  $msgid = 0;
}
if (isset($_GET['fwdid'])) {
  $fwdid = sprintf('%d',$_GET['fwdid']);
} else {
  $fwdid = 0;
}
if (isset($_GET['userid'])) {
  $userid = sprintf('%d',$_GET['userid']);
} else {
  $userid = 0;
}
if (isset($_GET['start'])) {
  $start = sprintf('%d',$_GET['start']);
} else {
  $start = 0;
}

if (!$msgid && !$fwdid && !$userid) {
  print $GLOBALS['I18N']->get('Invalid Request');
  return;
}

$access = accessLevel('userclicks');
switch ($access) {
  case 'owner':
  case 'all':
    $subselect = '';
    break;
  case 'none':
  default:
    print $GLOBALS['I18N']->get('You do not have access to this page');
    return;
    break;
}

#$limit = ' limit 100';

$ls = new WebblerListing($GLOBALS['I18N']->get('User Click Statistics'));

if ($fwdid) {
  $urldata = Sql_Fetch_Array_Query(sprintf('select url from %s where id = %d',
    $GLOBALS['tables']['linktrack_forward'],$fwdid));
}
if ($msgid) {
  $messagedata = Sql_Fetch_Array_query("SELECT * FROM {$tables['message']} where id = $msgid $subselect");
}
if ($userid) {
  $userdata = Sql_Fetch_Array_query("SELECT * FROM {$tables['user']} where id = $userid $subselect");
}

if ($fwdid && $msgid) {
  print '<h3>'.$GLOBALS['I18N']->get('User Click Details for a URL in a message');
  print ' ' .strtolower(PageLink2('uclicks&amp;id='.$fwdid,$urldata['url']));
  print '</h3>';
  print '<table class="userclicksDetails">
  <tr><td>'.$GLOBALS['I18N']->get('Subject').'<td><td>'.PageLink2('mclicks&amp;id='.$msgid,$messagedata['subject']).'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Entered').'<td><td>'.$messagedata['entered'].'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Sent').'<td><td>'.$messagedata['sent'].'</td></tr>
  </table><hr/>';
  $query = sprintf('select htmlclicked, textclicked, user.email,user.id as userid,firstclick,date_format(latestclick,
    "%%e %%b %%Y %%H:%%i") as latestclick,clicked from %s as uml_click, %s as user where uml_click.userid = user.id 
    and uml_click.forwardid = %d and uml_click.messageid = %d
    and uml_click.clicked',$GLOBALS['tables']['linktrack_uml_click'],$GLOBALS['tables']['user'],$fwdid,$msgid);
} elseif ($userid && $msgid) {
  print '<h3>'.$GLOBALS['I18N']->get('User Click Details for a message').'</h3>';
  print $GLOBALS['I18N']->get('User').' '.PageLink2('user&amp;id='.$userid,$userdata['email']);
  print '</h3>';
  print '<table class="userclickDetails">
  <tr><td>'.$GLOBALS['I18N']->get('Subject').'<td><td>'.PageLink2('mclicks&amp;id='.$msgid,$messagedata['subject']).'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Entered').'<td><td>'.$messagedata['entered'].'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Sent').'<td><td>'.$messagedata['sent'].'</td></tr>
  </table><hr/>';
  $query = sprintf('select htmlclicked, textclicked,user.email,user.id as userid,firstclick,date_format(latestclick,
    "%%e %%b %%Y %%H:%%i") as latestclick,clicked,messageid,forwardid,url from %s as uml_click, %s as user, %s as forward where uml_click.userid = user.id 
    and uml_click.userid = %d and uml_click.messageid = %d and forward.id = uml_click.forwardid',$GLOBALS['tables']['linktrack_uml_click'],$GLOBALS['tables']['user'],$GLOBALS['tables']['linktrack_forward'], $userid,$msgid);
} elseif ($fwdid) {
  print '<h3>'.$GLOBALS['I18N']->get('User Click Details for a URL').' <b>'.$urldata['url'].'</b></h3>';
  $query = sprintf('select user.email, user.id as userid,firstclick,date_format(latestclick,
    "%%e %%b %%Y %%H:%%i") as latestclick,clicked from %s as uml_click, %s as user where uml_click.userid = user.id 
    and uml_click.forwardid = %d group by uml_click.userid',$GLOBALS['tables']['linktrack_uml_click'],$GLOBALS['tables']['user'],
    $fwdid);
} elseif ($msgid) {
  print '<h3>'.$GLOBALS['I18N']->get('User Click Details for a Message').'</h3>';
  print '<table class="userclickDetails">
  <tr><td>'.$GLOBALS['I18N']->get('Subject').'<td><td>'.$messagedata['subject'].'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Entered').'<td><td>'.$messagedata['entered'].'</td></tr>
  <tr><td>'.$GLOBALS['I18N']->get('Sent').'<td><td>'.$messagedata['sent'].'</td></tr>
  </table><hr/>';
  $query = sprintf('select user.email,user.id as userid,firstclick,date_format(latestclick,
    "%%e %%b %%Y %%H:%%i") as latestclick,clicked from %s as uml_click, %s as user where uml_click.userid = user.id 
    and uml_click.messageid = %d',$GLOBALS['tables']['linktrack_uml_click'],$GLOBALS['tables']['user'],
    $msgid);
} elseif ($userid) {
  print '<h3>'.$GLOBALS['I18N']->get('User Click Details').'</h3>';
  $query = sprintf('select sum(htmlclicked) as htmlclicked,sum(textclicked) as textclicked,user.email,user.id as userid,min(firstclick) as firstclick,date_format(max(latestclick),
    "%%e %%b %%Y %%H:%%i") as latestclick,sum(clicked) as clicked,messageid,forwardid,url from %s as uml_click, %s as user, %s as forward where uml_click.userid = user.id 
    and uml_click.userid = %d and forward.id = uml_click.forwardid group by url',$GLOBALS['tables']['linktrack_uml_click'],$GLOBALS['tables']['user'],$GLOBALS['tables']['linktrack_forward'],
    $userid);
}

#ob_end_flush();
#flush();

$req = Sql_Query($query);
$total = Sql_Num_Rows($req);
if ($total > 100) {
  print simplePaging('userclicks&msgid='.$msgid.'&fwdid='.$fwdid.'&userid='.$userid,$start,$total,100, s('Subscribers'));

  $limit = ' limit '.$start.', 100';
  $req = Sql_Query($query.' '.$limit);
}
  
$summary = array();
$summary['totalclicks'] = 0;
while ($row = Sql_Fetch_Array($req)) {
#  print $row['email'] . "<br/>";
  if (!$userid) {
    $element = shortenTextDisplay($row['email']);
    $ls->addElement($element,PageUrl2('userhistory&amp;id='.$row['userid']));
    $ls->setClass($element,'row1');
  } else {
#    $link = substr($row['url'],0,50);
#    $element = PageLink2($link,$link,PageUrl2('uclicks&amp;id='.$row['forwardid']),"",true,$row['url']);
    $element = shortenTextDisplay($row['url']);
    $ls->addElement($element,PageUrl2('uclicks&amp;id='.$row['forwardid']));
    $ls->setClass($element,'row1');
    $ls->addColumn($element,$GLOBALS['I18N']->get('message'),PageLink2('mclicks&amp;id='.$row['messageid'],' '.$row['messageid']));
  }
#  $element = sprintf('<a href="%s" target="_blank" class="url" title="%s">%s</a>',$row['url'],$row['url'],substr(str_replace('http://','',$row['url']),0,50));
#  $total = Sql_Verbose_Query(sprintf('select count(*) as total from %s where messageid = %d and url = "%s"',
#    $GLOBALS['tables']['linktrack'],$id,$row['url']));
#  $totalsent = Sql_Fetch_Array_Query(sprintf('select count(*) as total from %s where url = "%s"',
#    $GLOBALS['tables']['linktrack'],$urldata['url']));
  $ls_userid = "";
  if (!$userid) {
    $ls_userid='<span class="viewusers"><a class="button" href="'.PageUrl2('userclicks&amp;userid='.$row['userid']).'" title="'.$GLOBALS['I18N']->get('view user').'"></a></span>';
  }
  $ls->addColumn($element,$GLOBALS['I18N']->get('firstclick'),formatDateTime($row['firstclick'],1));
  $ls->addColumn($element,$GLOBALS['I18N']->get('latestclick'),$row['latestclick']);
  $ls->addColumn($element,$GLOBALS['I18N']->get('clicks'),$row['clicked'].$ls_userid);
  $ls->addRow($element,'<div class="content listingsmall fright gray">'.$GLOBALS['I18N']->get('HTML').': '.$row['htmlclicked'].'</div>'.
                       '<div class="content listingsmall fright gray">'.$GLOBALS['I18N']->get('text').': '.$row['textclicked'].'</div>','');
#  $ls->addColumn($element,$GLOBALS['I18N']->get('sent'),$total['total']);
#  $perc = sprintf('%0.2f',($row['numclicks'] / $totalsent['total'] * 100));
#  $ls->addColumn($element,$GLOBALS['I18N']->get('clickrate'),$perc.'%');
  $summary['totalclicks'] += $row['clicked'];
}

## adding a total doesn't make sense if we're not listing everything, it'll only do the total of the page
//$ls->addElement($GLOBALS['I18N']->get('total'));
//$ls->setClass($GLOBALS['I18N']->get('total'),'rowtotal');
//$ls->addColumn($GLOBALS['I18N']->get('total'),$GLOBALS['I18N']->get('clicks'),$summary['totalclicks']);
print $ls->display();
