<?php
require_once dirname(__FILE__).'/accesscheck.php';

if (isset($_GET['id'])) {
  $hash = '#'.sprintf('%d',$_GET['id']);
  $id = sprintf('%d',$_GET['id']);
} else {
  $hash = '';
  $id = 0;
}

if (isset($_POST['save']) && $_POST['save']) {
  Sql_Query(sprintf('update %s set regex = "%s",action="%s", comment="%s",status = "%s" where id= %d',
    $GLOBALS['tables']['bounceregex'],trim($_POST['regex']),sql_escape($_POST['action']),sql_escape($_POST['comment']),sql_escape($_POST['status']),$_GET['id']),1);
  $num = Sql_Affected_Rows();
  if ($num < 0) {
    print $GLOBALS['I18N']->get('Updating the regular expression of this rule caused an Sql conflict<br/>This is probably because there is already a rule like that. Do you want to delete this rule instead?');
    print '<p>'.PageLink2('bouncerules&del='.$id,$GLOBALS['I18N']->get('Yes')).'&nbsp;';
    print PageLink2('bouncerules',$GLOBALS['I18N']->get('No')).'</p>';
    return;
  }
  Redirect('bouncerules'.$hash);
}

print '<p>'.PageLink2('bouncerules'.$hash,$GLOBALS['I18N']->get('back to list of bounce rules')).'</p>';
$data = Sql_Fetch_Array_Query(sprintf('select * from %s where id = %d',
  $GLOBALS['tables']['bounceregex'],$id));

print formStart();
print '<table>';
printf('<tr><td>%s</td><td><input type=text name="regex" size=30 value="%s"></td></tr>',
  $GLOBALS['I18N']->get('Regular Expression'),htmlspecialchars($data['regex']));
printf('<tr><td>%s</td><td>%s</td></tr>',
  $GLOBALS['I18N']->get('Created By'),adminName($data['admin']));
printf('<tr><td>%s</td><td><select name="action">',$GLOBALS['I18N']->get('Action'));
foreach ($GLOBALS['bounceruleactions'] as $action => $desc) {
  printf('<option value="%s" %s>%s</option>',$action,$data['action'] == $action ? 'selected':'',$desc);
}
print '</select></td></tr>';
printf('<tr><td>%s</td><td><select name="status">',$GLOBALS['I18N']->get('Status'));
printf('<option value="none">[%s]</option>',$GLOBALS['I18N']->get('Select Status'));
foreach (array('active','candidate') as $type) {
  printf('<option value="%s" %s>%s</option>',$type,$data['status'] == $type ? 'selected':'',$GLOBALS['I18N']->get($type));
}
print '</select></td></tr>';
printf('<tr><td colspan=2>%s</td></tr><tr><td colspan=2>
  <textarea name="comment" rows=10 cols=65>%s</textarea></td></tr>',
  $GLOBALS['I18N']->get('Memo for this rule'),htmlspecialchars($data['comment']));
print '<tr><td colspan=2><input type=submit name="save" value="'.$GLOBALS['I18N']->get('Save Changes').'"></td></tr>';
print '</table></form>';

$req = Sql_Query(sprintf('select * from %s where regex = %d',$GLOBALS['tables']['bounceregex_bounce'],$_GET['id']));
$num = Sql_affected_Rows();
if ($num) {
  print '<p>'.$GLOBALS['I18N']->get('related bounces').'</p><p>';
} else {
  print '<p>'.$GLOBALS['I18N']->get('no related bounces found').'</p>';
}  
$c = 0;
while ($row = Sql_Fetch_Array($req)) {
  print PageLink2('bounce&id='.$row['bounce'],$row['bounce']).' ';
  $c++;
  if ($c > 100) break;
}
if ($c < $num) {
  printf(' '.$GLOBALS['I18N']->get('and more, %d in total'),$num);
}

?>
