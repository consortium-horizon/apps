<?php
require_once dirname(__FILE__).'/accesscheck.php';

$content = '';
if (isset($_POST["usercheck"])) {
  $lsexist = new WebblerListing(s("Existing subscribers"));
  $lsnonexist = new WebblerListing(s("Non existing subscribers "));
  $users = explode("\n",$_POST["usercheck"]);
  foreach ($users as $user) {
    $user = trim($user);
    if (isset($_POST['check']) && $_POST["check"] == "foreignkey") {
      $exists = Sql_Query(sprintf('select id,foreignkey,email from %s where foreignkey = "%s"',$tables["user"],sql_escape($user)));
    } else {
      $exists = Sql_Query(sprintf('select id,foreignkey,email from %s where email = "%s"',$tables["user"],sql_escape($user)));
    }
    if (Sql_Num_Rows($exists)) {
      $id = Sql_Fetch_Array($exists);
      $element = strip_tags($user);
      $lsexist->addElement($element,PageUrl2("user&amp;id=".$id["id"]));
      $lsexist->addColumn($element,$GLOBALS["I18N"]->get('email'),$id['email']);
      $lsexist->addColumn($element,$GLOBALS["I18N"]->get('key'),$id['foreignkey']);
    } else {
      $lsnonexist->addElement(strip_tags($user));
    }
  }
  print $lsexist->display();
  print $lsnonexist->display();
} else {
  $_POST['usercheck'] = '';
}

/*
print $GLOBALS["I18N"]->get("Page to check the existence of users in the database");
*/

$content .=  '<form method="post" action="">';
$content .=  '<table class="usercheckForm">';
$content .=  '<tr><td>'.s("What is the type of information you want to check").'</td></tr>';
$content .=  '<tr><td><label for="foreignkey">'.s("Foreign Key").'</label> <input type="radio" id="foreignkey" name="check" value="foreignkey"></td></tr>';
$content .=  '<tr><td><label for="email">'.s("Email").'</label> <input type="radio" id="email" name="check" value="email"></td></tr>';
$content .=  '<tr><td>'.s("Paste the values to check in this box, one per line").'</td></tr>';
$content .=  '<tr><td><input type="submit" name="continue" value="'.s("Continue").'" class="button"></td></tr>';
$content .=  '<tr><td><textarea name="usercheck" rows=30 cols=65>'.htmlspecialchars(stripslashes($_POST['usercheck'])).'</textarea></td></tr>';
$content .=  '<tr><td><input type="submit" name="continue" value="'.s("Continue").'" class="button"></td></tr>';
$content .=  '</table></form>';

$p = new UIPanel('',$content);
print $p->display();
