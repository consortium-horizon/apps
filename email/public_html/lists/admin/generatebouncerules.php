<?php
require_once dirname(__FILE__).'/accesscheck.php';
print '<script language="Javascript" src="js/progressbar.js" type="text/javascript"></script>';
flush();

#$limit = '';
$numperrun = 10000;
ob_end_flush();
$count = 0;
$notmatched = 0;
$existmatch = 0;
$rules = array();

function output($message) {
  print $message . "<br/>\n";
  flush();
}

print '<script language="Javascript" type="text/javascript"> yposition = 10;document.write(progressmeter); start();</script>';

# lets not do this unless we do some locking first
$abort = ignore_user_abort(1);
$process_id = getPageLock();
if (empty($process_id)) {
  return;
}
$req = Sql_Fetch_Row_query(sprintf('select count(*) from %s ',$GLOBALS['tables']['bounce']));
$total = $req[0];
if (isset($_GET['s'])) {
  $s = sprintf('%d',$_GET['s']);
  $e = $s + $numperrun;
} else {
  $s = 0;
  $e = $numperrun;
}
$limit = ' limit '.$s.', '.$numperrun;

if ($total > $numperrun && $e < $total) {
  $next = '<p class="button">'.PageLink2('generatebouncerules&s='.$e,$GLOBALS['I18N']->get('Process Next Batch')).'</p>';
} else {
  $next = '';
}

$req = Sql_query(sprintf('select * from %s %s ',$GLOBALS['tables']['bounce'],$limit));
while ($row = sql_Fetch_array($req)) {
  $alive = checkLock($process_id);
  if ($alive)
    keepLock($process_id);
  else {
    print $GLOBALS['I18N']->get("Process Killed by other process");
    exit;
  }
  $count++;
  if ($count % 10 == 0) {
    print '. '."\n";flush();
    if ($count > 1000000) {
      return;
    }
  }
  $regexid = matchedBounceRule($row['data']);
  if ($regexid) {
    Sql_Query(sprintf('insert into %s (regex,bounce) values(%d,%d)',
      $GLOBALS['tables']['bounceregex_bounce'],$regexid,$row['id']),1);
    $bouncematched = 1;
    $existmatch++;
  } else {
    $lines = explode("\n",$row['data']);
  #  print '<br/>'.sizeof($lines).' lines';
    $bouncematched = 0;
    set_time_limit(100);
    foreach ($lines as $line) {
      if (preg_match('/ (55\d) (.*)/',$line,$regs)) {
        $bouncematched = 1;
        $code = $regs[1];
        $info = $regs[2];
        #if ($code != 550) {
        #  print "<br/>$line";
        #  print "<br/><b>$code</b>";
        #  print htmlspecialchars(" $info");
        $rule = preg_replace('/[^\s\<]+@[^\s\>]+/','.*',$info);
        $rule = preg_replace('/\{.*\}/U','.*',$rule);
        $rule = preg_replace('/\(.*\)/U','.*',$rule);
        $rule = preg_replace('/\<.*\>/U','.*',$rule);
        $rule = preg_replace('/\[.*\]/U','.*',$rule);
        $rule = preg_replace('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/','\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $rule); ## replace IPs with their regex
        $rule = str_replace('?','.',$rule);
        $rule = str_replace('/','.',$rule);
        $rule = str_replace('"','.',$rule);
        $rule = str_replace('(','.',$rule);
        $rule = str_replace(')','.',$rule);
        
        if (stripos($rule,'Unknown local user') !== false) {
          $rule = 'Unknown local user';
        } elseif (preg_match('/Unknown local part (.*) in/iU',$rule,$regs)) {
          $rule = preg_replace('/'.preg_quote($regs[1]).'/','.*',$rule);
        } elseif (preg_match('/mta(.*)\.mail\.yahoo\.com/iU',$rule)) {
          $rule = preg_replace('/mta[\d]+/i','mta[\\d]+',$rule);
        }
        
        $rule = trim($rule);
        if (!in_array($rule,$rules) && strlen($rule) > 25) {# && $code != 554 && $code != 552) {
          if (VERBOSE) {
            print "<br/>".htmlspecialchars($rule);
          }
          array_push($rules,$rule);
          
          #}
          switch ($code) {
            case 554:case 552:
              $action = 'unconfirmuseranddeletebounce';break;
            case 550:
              $action = 'blacklistuseranddeletebounce';break;
            default:
              $action = 'unconfirmuseranddeletebounce';break;
          }
          $query
          = ' insert into %s'
          . '    (regex, action, comment, status)'
          . ' values'
          . '    (?, ?, ?, ?)';
          $query = sprintf($query, $GLOBALS['tables']['bounceregex']);
          Sql_Query_Params($query, array( trim($rule), $action, 'Auto Created from bounce ' . $row['id'] . "\n line: " . $line, 'candidate'));
          $regexid = Sql_Insert_Id($GLOBALS['tables']['bounceregex'], 'id');
          if ($regexid) { # most likely duplicate entry if no value
            $query
            = ' insert into %s'
            . '    (regex, bounce)'
            . ' values'
            . '    (?, ?)';
            $query = sprintf($query, $GLOBALS['tables']['bounceregex_bounce']);
            Sql_Query_Params($query, array($regexid, $row['id']));
          } else {
#            print matchedBounceRule($row['data']);
            print $GLOBALS['I18N']->get('Hmm, duplicate entry, ').' '.$row['id']." $code $rule<br/>";
          }
        }
      }
    }
  }
  if (!$bouncematched) {
    $notmatched++;
  }
}

print '<ul>';
print '<li>'.sizeof($rules).' '.$GLOBALS['I18N']->get('new rules found').'</li>';
print '<li>'.$notmatched.' '.$GLOBALS['I18N']->get('bounces not matched').'</li>';
print '<li>'.$existmatch.' '.$GLOBALS['I18N']->get('bounces matched to existing rules').'</li>';
if ($next) {
  print '<li>'.$next.'</li>';
}
print '</ul>';

print '<script language="Javascript" type="text/javascript"> finish(); </script>';
releaseLock($process_id);

return;


?>
