<?php
require_once dirname(__FILE__).'/accesscheck.php';

print "Sorry, stresstest is out of date";
return;

function my_shutdown () {
  global $tables;
  print "Script status: ".connection_status(); # unfortunately buggy in 4.2.1
  $res = Sql_query("select count(*) from $tables[user]");
  $row = Sql_fetch_row($res);

  print '<script language="Javascript" type="text/javascript"> finish(); </script>';
  print '<script language="Javascript" type="text/javascript"> document.forms[0].output.value="Done. Now there are '.$row[0].' users in the database";</script>'."\n";
 # register_shutdown_function("");
  exit;
}

register_shutdown_function("my_shutdown");

print '<script language="Javascript" src="js/progressbar.js" type="text/javascript"></script>';
ignore_user_abort(1);
?>

<h3>Stresstest</h3>
Filling database with stress test information, please wait
<br/><b>Warning</b> this is quite demanding for your browser!
<?php

function fill($prefix,$listid) {
  global $server_name,$tables,$table_prefix;
  # check for not too many
  $domain = getConfig("domain");
  $res = Sql_query("select count(*) from $tables[user]");
  $row = Sql_fetch_row($res);
  if ($row[0] > 50000) {
    error("Hmm, I think 50 thousand users is quite enough for a test<br/>This machine does need to do other things you know.");
    print '<script language="Javascript" type="text/javascript">finish();</script>';
    print '<script language="Javascript" type="text/javascript"> document.forms[0].output.value="Done. Now there are '.$row[0].' users in the database";</script>'."\n";
    return 0;
  }

  # fill the database with "users" who have any combination of attribute values
  $attributes = array();
  $res = Sql_query("select * from $tables[attribute] where type = \"select\" or type = \"checkbox\" or type=\"radio\"");
  $num_attributes = Sql_Affected_rows();
  $total_attr = 0;
  $total_val = 0;

  while ($row = Sql_fetch_array($res)) {
    array_push($attributes,$row["id"]);
    $total_attr++;
    $values[$row["id"]] = array();
    $res2 = Sql_query("select * from $table_prefix"."listattr_".$row["tablename"]);
    while ($row2 = Sql_fetch_array($res2)) {
      array_push($values[$row["id"]],$row2["id"]);
      $total_val++;
    }
  }
  $total = $total_attr * $total_val;
  if (!$total) {
    print '<script language="Javascript" type="text/javascript"> finish(); </script>';
    Fatal_Error("Can only do stress test when some attributes exist");
    return 0;
  }

  for ($i = 0;$i< $total;$i++) {
    $data = array();
    reset($attributes);
    while (list($key,$val) = each ($attributes)) {
      $data[$val] = pos($values[$val]);
      if (!$data[$val]) {
        reset($values[$val]);
        $data[$val] = pos($values[$val]);
      }
      next($values[$val]);
    }

    $query = sprintf('insert into %s (email,entered,confirmed) values("testuser%s",current_timestamp,1)',
      $tables["user"], $prefix . '-' . $i . '@' . $domain);
    $result = Sql_query($query,0);

    $userid = Sql_Insert_Id($tables['user'], 'id');
    if ($userid) {
      $result = Sql_query("replace into $tables[listuser] (userid,listid,entered) values($userid,$listid,current_timestamp)");
      reset($data);
      while (list($key,$val) = each ($data))
        if ($key && $val)
          Sql_query("replace into $tables[user_attribute] (attributeid,userid,value) values(".$key.",$userid,".$val.")");
    }
  }
  return 1;
}

print formStart(' class="testOutput" ').'<input type="text" name="output" size=45></form>';
print '<p class="button">'.PageLink2("stresstest","Erase Test information","eraseall=yes").' (may take a while)';
print '<script language="Javascript" type="text/javascript"> document.write(progressmeter); start();</script>';

ob_end_flush();
flush();

$testlists = array();
$res = Sql_Query("select id from $tables[list] where name like \"%test%\"");
while ($row = Sql_Fetch_Row($res)) {
  array_push($testlists,$row[0]);
}

if (!ini_get("safe_mode")) {
  if (!sizeof($testlists)) {
    print '<script language="Javascript" type="text/javascript"> document.forms[0].output.value="Error: cannot find any test lists to use";</script>'."\n";
  } elseif (!isset($eraseall)) {
    print '<script language="Javascript" type="text/javascript"> document.forms[0].output.value="Filling ";</script>'."\n";
    for ($i=0;$i<=100;$i++) {
      set_time_limit(60);
      flush();
      reset($testlists);
      while (list($key,$val) = each ($testlists))
        if (!fill(getmypid().$i,$val))
          return;
    }
  } else {
    $req = Sql_Query("select id from $tables[user] where email like \"testuser%\"");
    $i = 1;
    set_time_limit(60);
    print '<script language="Javascript" type="text/javascript"> document.forms[0].output.value="Erasing ";</script>'."\n";flush();
    while ($row = Sql_Fetch_row($req)) {
      Sql_Query("delete quick from $tables[user_attribute] where userid = $row[0]");
      Sql_Query("delete quick from $tables[listuser] where userid = $row[0]");
      Sql_Query("delete quick from $tables[usermessage] where userid = $row[0]");
      Sql_Query("delete quick from $tables[user] where id = $row[0]");
      $i++;
    }
  }
} else {
  print Error("Cannot do stresstest in safe mode");
}

?>
