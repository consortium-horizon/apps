<?php

require_once dirname(__FILE__).'/accesscheck.php';
$access = accessLevel("listbounces");

$listid = empty($_GET['id']) ? 0 : sprintf('%d',$_GET['id']);
$download = isset($_GET['type']) && $_GET['type'] == 'dl';
$isowner_and = '';
$isowner_where = '';

switch ($access) {
  case "owner":
    $subselect = " where owner = ".$_SESSION["logindetails"]["id"];
    if ($listid) {
      $query = "select id from " . $tables['list'] . $subselect . " and id = ?";
      $rs = Sql_Query_Params($query, array($listid));
      if (!Sql_Num_Rows($rs)) {
        Fatal_Error($GLOBALS['I18N']->get("You do not have enough privileges to view this page"));
        return;
      }
    }
    $isowner_and = sprintf(" list.owner = %d and ", $_SESSION["logindetails"]["id"]);
    $isowner_where = sprintf(" where list.owner = %d ", $_SESSION["logindetails"]["id"]);
    break;
  case "all":
  case "view":
    break;
  case "none":
  default:
    if ($listid) {
      Fatal_Error($GLOBALS['I18N']->get("You do not have enough privileges to view this page"));
      $isowner_and = sprintf(" list.owner = 0 and ");
      $isowner_where = sprintf(" where list.owner = 0 ");
      return;
    }
    break;
}
if (!$listid) {
  $req = Sql_Query(sprintf('select listuser.listid,count(distinct userid) as numusers from %s list, %s listuser,
    %s umb, %s lm where %s list.id = listuser.listid and listuser.listid = lm.listid and listuser.userid = umb.user group by listuser.listid
    order by listuser.listid limit 150',$GLOBALS['tables']['list'],$GLOBALS['tables']['listuser'],$GLOBALS['tables']['user_message_bounce'],$GLOBALS['tables']['listmessage'], $isowner_and));
  $ls = new WebblerListing($GLOBALS['I18N']->get('Choose a list'));
  $some = 0;
  while ($row = Sql_Fetch_Array($req)) {
    $some = 1;
    $element = '<!--'.$GLOBALS['I18N']->get('list').' '.$row['listid'].'-->'.listName($row['listid']);
    $ls->addElement($element,PageUrl2('listbounces&amp;id='.$row['listid']));
  #  $ls->addColumn($element,$GLOBALS['I18N']->get('name'),listName($row['listid']),PageUrl2('editlist&amp;id='.$row['listid']));
    $ls->addColumn($element,$GLOBALS['I18N']->get('# bounced'),$row['numusers']);
  }
  if ($some) {
    print $ls->display();
  } else {
    print '<p>'.$GLOBALS['I18N']->get('None found').'</p>';
  }
  return;
}

$query
= ' select lu.userid, count(umb.bounce) as numbounces'
. ' from %s lu'
. '    join %s umb'
. '       on lu.userid = umb.user'
. ' where '
#. ' current_timestamp < date_add(umb.time,interval 6 month) '
#. ' and ' 
. ' lu.listid = ? '
. ' group by lu.userid '
;
$query = sprintf($query, $GLOBALS['tables']['listuser'], $GLOBALS['tables']['user_message_bounce']);
#print $query;
$req = Sql_Query_Params($query, array($listid));
$total = Sql_Num_Rows($req);
$limit = '';
$numpp = 150;

$selectOtherlist = new buttonGroup(new Button(PageUrl2('listbounces'),$GLOBALS['I18N']->get('Select another list')));
$lists = Sql_Query(sprintf('select id,name from %s list %s order by listorder',$tables['list'], $isowner_where));
while ($list = Sql_Fetch_Assoc($lists)) {
  $selectOtherlist->addButton(new Button(PageUrl2('listbounces').'&amp;id='.$list['id'],htmlspecialchars($list['name'])));
}

print $selectOtherlist->show();
if ($total) {
  print PageLinkButton('listbounces&amp;type=dl&amp;id='.$listid,'Download emails');
}


print '<p>'.s('%d bounces to list %s',$total,listName($listid))."</p>";

$start = empty($_GET['start']) ? 0 : sprintf('%d',$_GET['start']);
if ($total > $numpp && !$download) {
#  print Paging2('listbounces&amp;id='.$listid,$total,$numpp,'Page');
 # $listing = sprintf($GLOBALS['I18N']->get("Listing %s to %s"),$s,$s+$numpp);
  $limit = "limit $start,".$numpp;
  print simplePaging('listbounces&amp;id='.$listid,$start,$total,$numpp);

  $query .= $limit;
  $req = Sql_Query_Params($query, array($listid));
}

if ($download) {
  ob_end_clean();
  Header("Content-type: text/plain");
  $filename = 'Bounces on '.listName($listid);
  header("Content-disposition:  attachment; filename=\"$filename\"");
}

$ls = new WebblerListing($GLOBALS['I18N']->get('Bounces on').' '.listName($listid));
$ls->noShader();
while ($row = Sql_Fetch_Array($req)) {
  $userdata = Sql_Fetch_Array_Query(sprintf('select * from %s where id = %d',
    $GLOBALS['tables']['user'],$row['userid']));
  if (!empty($userdata['email'])) {
    if ($download) {
      print $userdata['email']."\n";
    } else {
      $ls->addElement($row['userid'],PageUrl2('user&amp;id='.$row['userid']));
      $ls->addColumn($row['userid'],s('address'),$userdata['email']);
      $ls->addColumn($row['userid'],s('# bounces'),PageLink2('userhistory&id='.$row['userid'],$row['numbounces']));
    }
  }
}
if (!$download) {
  print $ls->display();
} else {
  exit;
}
