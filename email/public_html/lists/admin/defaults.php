<?php
require_once dirname(__FILE__).'/accesscheck.php';


$attributes = array();
@ob_end_flush();

function readentry($file) {
  if (preg_match('/\.\./',$file)) return;
  if (!preg_match('/data/',$file)) return;
  $fp = fopen($file,"r");
  $found = "";
  while (!feof ($fp)) {
    $buffer = fgets($fp, 4096);
    if (strpos($buffer,"#") === false) {
      $found = $buffer;
      fclose($fp);
      return $found;
    }
  }
  fclose ($fp);
  return "";
}

$dir = opendir("data");
while ($file = readdir($dir)) {
  if (is_file("data/$file")) {
    $entry = readentry("data/$file");
    $attributes[$entry] = $file;
  }
}
closedir($dir);

if (!empty($_POST['selected']) && is_array($_POST['selected'])) {
  $selected = $_POST['selected'];
  while(list($key,$val) = each($selected)) {
    $entry = readentry("data/$val");
    list($name,$desc) = explode(":",$entry);
    print "<br/><br/>".$GLOBALS['I18N']->get('Loading')." $desc<br/>\n";
    $lc_name = str_replace(" ","", strtolower(str_replace(".txt","",$val)));
    $lc_name = preg_replace("/[\W]/","",$lc_name);

    if ($lc_name == "") Fatal_Error($GLOBALS['I18N']->get('Name cannot be empty:')." $lc_name");
    Sql_Query("select * from {$tables['attribute']} where tablename = \"$lc_name\"");
    if (Sql_Affected_Rows()) Fatal_Error($GLOBALS['I18N']->get('Name is not unique enough'));

    $query = sprintf('insert into %s (name,type,required,tablename) values("%s","%s",%d,"%s")',
    $tables["attribute"],addslashes($name),"select",1,$lc_name);
    Sql_Query($query);
    $insertid = Sql_Insert_Id($tables['attribute'], 'id');

    $query = "create table $table_prefix"."listattr_$lc_name (id integer not null primary key auto_increment, name varchar(255) unique,listorder integer default 0)";
    Sql_Query($query);
    $fp = fopen("data/$val","r");
    $header = "";
    while (!feof ($fp)) {
      $buffer = fgets($fp, 4096);
      if (strpos($buffer,"#") === false) {
        if (!$header)
          $header = $buffer;
        else if (trim($buffer) != "")
          Sql_Query(sprintf('insert into %slistattr_%s (name) values("%s")',$table_prefix,$lc_name,trim($buffer)));
      }
    }
    fclose ($fp);
  }
  print $GLOBALS['I18N']->get('done')."<br/><br/>";
  
  print PageLinkButton("attributes",$GLOBALS['I18N']->get('return to editing attributes'));
  
#@@@@ not sure about this one:  print '<p class="button">'.PageLink2("attributes",$GLOBALS['I18N']->get('continue')).'</p>';

} else {

?>


<?php echo formStart(' class="defaultsAdd"')?>
<?php
reset($attributes);
while (list($key,$attribute) = each ($attributes)) {
  if (strstr($key,':')) {
    list($name,$desc) = explode(":",$key);
    if ($name && $desc) {
      printf('<input type="checkbox" name="selected[]" value="%s" />%s<br/>', $attribute,$desc);
    }
  }
}
print '<input class="submit" type="submit" value="'.$GLOBALS['I18N']->get('Add').'" /></form>';

}
?>
