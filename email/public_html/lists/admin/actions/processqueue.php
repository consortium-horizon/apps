<?php
## temporarily remove this check, to make sure processing the queue with a remote call continues to work
## https://mantis.phplist.com/view.php?id=17316
#verifyCsrfGetToken();

if (isset($_GET['login']) || isset($_GET['password'])) {
  print Error(s('Remote processing of the queue is now handled with a processing secret'));
  return;
}
$inRemoteCall = false;

if (isset($_GET['secret'])) {
  $ourSecret = getConfig('remote_processing_secret');
  if ($ourSecret != $_GET['secret']) {
    print Error(s('Incorrect processing secret'));
    return;
  } else {
    $inRemoteCall = true;
  }
} else {
  ## we're in a normal session, so the csrf token should work
  verifyCsrfGetToken();
}

require_once dirname(__FILE__).'/../accesscheck.php';
require_once dirname(__FILE__) .'/../sendemaillib.php';

$status = 'OK';
$processqueue_timer = new timer();
$domainthrottle = array();
# check for other processes running

if ((!empty($GLOBALS['commandline']) && isset($cline['f'])) || $inRemoteCall) {
  # force set, so kill other processes
  cl_output('Force set, killing other send processes');
  $send_process_id = getPageLock(1);
} else {
  $send_process_id = getPageLock();
}
if (empty($send_process_id)) {
  output(s('Unable get lock for processing'));
  $status = s('Error processing');
  return;
}

#cl_output('page locked on '.$send_process_id);

if (empty($GLOBALS['commandline']) && isset($_GET['reload'])) {
  $reload = sprintf('%d',$_GET['reload']);
} else {
  $reload = 0;
}

## this one sends a notification to plugins that processing has started
foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
  $plugin->processQueueStart();
}

## let's make sure all subscribers have a uniqid
## only when on CL
if ($GLOBALS['commandline']) {
  $req = Sql_Query(sprintf('select id from %s where uniqid is NULL or uniqid = ""',$GLOBALS["tables"]["user"]));
  $num = Sql_Affected_Rows();
  if ($num) {
    cl_output(s('Giving a Unique ID to %d subscribers, this may take a while', $num));
    while ($row = Sql_Fetch_Row($req)) {
      Sql_query(sprintf('update %s set uniqid = "%s" where id = %d',$GLOBALS["tables"]["user"],getUniqID(),$row[0]));
    }
  }
}

$num_per_batch = 0;
$batch_period = 0;
$script_stage = 0; # start
$someusers = $skipped = 0;

$maxbatch = -1;
$minbatchperiod = -1;
# check for batch limits
$ISPrestrictions = '';
$ISPlockfile = '';
//$rssitems = array(); //Obsolete by rssmanager plugin 
$user_attribute_query = '';
$lastsent = !empty($_GET['lastsent']) ? sprintf('%d',$_GET['lastsent']):0;
$lastskipped = !empty($_GET['lastskipped']) ? sprintf('%d',$_GET['lastskipped']):0;

if ($fp = @fopen("/etc/phplist.conf","r")) {
  $contents = fread($fp, filesize("/etc/phplist.conf"));
  fclose($fp);
  $lines = explode("\n",$contents);
  $ISPrestrictions = $GLOBALS['I18N']->get('The following restrictions have been set by your ISP:')."\n";
  foreach ($lines as $line) {
    list($key,$val) = explode("=",$line);

    switch ($key) {
      case "maxbatch": $maxbatch = sprintf('%d',$val);$ISPrestrictions .= "$key = $val\n";break;
      case "minbatchperiod": $minbatchperiod = sprintf('%d',$val);$ISPrestrictions .= "$key = $val\n";break;
      case "lockfile": $ISPlockfile = $val;
    }
  }
}
if (MAILQUEUE_BATCH_SIZE) {
  if ($maxbatch > 0) {
    $num_per_batch = min(MAILQUEUE_BATCH_SIZE,$maxbatch);
  } else {
    $num_per_batch = sprintf('%d',MAILQUEUE_BATCH_SIZE);
  }
} else {
  if ($maxbatch > 0) {
    $num_per_batch = $maxbatch;
  }
}

if (MAILQUEUE_BATCH_PERIOD) {
  if ($minbatchperiod > 0) {
    $batch_period = max(MAILQUEUE_BATCH_PERIOD,$minbatchperiod);
  } else {
    $batch_period = MAILQUEUE_BATCH_PERIOD;
  }
}

## force batch processing in small batches when called from the web interface
/*
 * bad idea, we shouldn't touch the batch settings, in case they are very specific for
 * ISP restrictions, instead limit webpage processing by time (below)
 * 
if (empty($GLOBALS['commandline'])) {
  $num_per_batch = min($num_per_batch,100);
  $batch_period = max($batch_period,1);
} elseif (isset($cline['m'])) {
  $cl_num_per_batch = sprintf('%d',$cline['m']);
  ## don't block when the param is not a number
  if (!empty($cl_num_per_batch)) {
    $num_per_batch = $cl_num_per_batch;
  }
  cl_output("Batch set with commandline to $num_per_batch");
}
*/
$maxProcessQueueTime = 0;
if (defined('MAX_PROCESSQUEUE_TIME') && MAX_PROCESSQUEUE_TIME > 0) {
  $maxProcessQueueTime = (int)MAX_PROCESSQUEUE_TIME;
}
# in-page processing force to a minute max, and make sure there's a batch size
if (empty($GLOBALS['commandline'])) {
  $maxProcessQueueTime = min($maxProcessQueueTime,60);
  if ($num_per_batch <= 0) {
    $num_per_batch = 10000;
  }
}

if (VERBOSE && $maxProcessQueueTime) {
  output(s('Maximum time for queue processing').': '.$maxProcessQueueTime,'progress');
}

if (isset($cline['m'])) {
  cl_output('Max to send is '.$cline['m'].' num per batch is '.$num_per_batch);
  $clinemax = (int)$cline['m'];
  ## slow down just before max
  if ($clinemax < 20) {
    $num_per_batch = min(2,$clinemax,$num_per_batch);
  } elseif ($clinemax < 200) {
    $num_per_batch = min(20,$clinemax,$num_per_batch);
  } else {
    $num_per_batch = min($clinemax,$num_per_batch);
  }
  cl_output('Max to send is '.$cline['m'].' setting num per batch to '.$num_per_batch);
}

$safemode = 0;
if (ini_get("safe_mode")) {
  # keep an eye on timeouts
  $safemode = 1;
  $num_per_batch = min(100,$num_per_batch);
  print $GLOBALS['I18N']->get('Running in safe mode').'<br/>';
}

$original_num_per_batch = $num_per_batch;
if ($num_per_batch && $batch_period) {
  # check how many were sent in the last batch period and take off that
  # amount from this batch
/*
  output(sprintf('select count(*) from %s where entered > date_sub(current_timestamp,interval %d second) and status = "sent"',
    $tables["usermessage"],$batch_period));
*/
  $recently_sent = Sql_Fetch_Row_Query(sprintf('select count(*) from %s where entered > date_sub(current_timestamp,interval %d second) and status = "sent"',
    $tables["usermessage"],$batch_period));
  cl_output('Recently sent : '.$recently_sent[0]);
  $num_per_batch -= $recently_sent[0];

  # if this ends up being 0 or less, don't send anything at all
  if ($num_per_batch == 0) {
    $num_per_batch = -1;
  }
}
# output some stuff to make sure it's not buffered in the browser
for ($i=0;$i<10000; $i++) {
  print '  ';
  if ($i%100 == 0) print "\n";
}
print '<style type="text/css" src="css/app.css"></style>';
print '<style type="text/css" src="ui/'.$GLOBALS['ui'].'/css/style.css"></style>';
print '<script type="text/javascript" src="js/'.$GLOBALS['jQuery'].'"></script>';
## not sure this works, but would be nice
print '<script type="text/javascript">$("#favicon").attr("href","images/busy.gif");</script>';

flush();
# report keeps track of what is going on
$report = "";
$nothingtodo = 0;
$cached = array(); # cache the message from the database to avoid reloading it every time

function my_shutdown () {
  global $script_stage,$reload;
#  output( "Script status: ".connection_status(),0); # with PHP 4.2.1 buggy. http://bugs.php.net/bug.php?id=17774
  output( s('Script stage').': '.$script_stage,0,'progress');
  global $counters,$report,$send_process_id,$tables,$nothingtodo,$processed,$notsent,$unconfirmed,$num_per_batch,$batch_period;
  $some = $processed; 
  $delaytime = 0;
  if (!$some) {
    output($GLOBALS['I18N']->get('Finished, Nothing to do'),0,'progress');
    $nothingtodo = 1;
  }

  $totaltime = $GLOBALS['processqueue_timer']->elapsed(1);
  if ($totaltime > 0) {
    $msgperhour = (3600/$totaltime) * $counters['sent'];
  } else {
    $msgperhour = s('Calculating');
  }
  if ($counters['sent']) {
    output(sprintf('%d %s %01.2f %s (%d %s)',$counters['sent'],$GLOBALS['I18N']->get('messages sent in'),
      $totaltime,$GLOBALS['I18N']->get('seconds'),$msgperhour,$GLOBALS['I18N']->get('msgs/hr')),$counters['sent'],'progress');
  }
  if ($counters['invalid']) {
    output(s('%d invalid email addresses',$counters['invalid']),1,'progress');
  }
  if ($counters['failed_sent']) {
    output(s('%d failed (will retry later)',$counters['failed_sent']),1,'progress');
    foreach ($counters as $label => $value) {
    #  output(sprintf('%d %s',$value,$GLOBALS['I18N']->get($label)),1,'progress');
      cl_output(sprintf('%d %s',$value,$GLOBALS['I18N']->get($label)));
    }
  }
  if ($unconfirmed) {
    output(sprintf($GLOBALS['I18N']->get('%d emails unconfirmed (not sent)'),$unconfirmed),1,'progress');
  }

  foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
    $plugin->processSendStats($counters['sent'],$counters['invalid'],$counters['failed_sent'],$unconfirmed,$counters);
  }

  flushClickTrackCache();
  releaseLock($send_process_id);

  finish("info",$report,$script_stage);
  if ($script_stage < 5 && !$nothingtodo) {
    output ($GLOBALS['I18N']->get('Warning: script never reached stage 5')."\n".$GLOBALS['I18N']->get('This may be caused by a too slow or too busy server')." \n");
  } elseif ($script_stage == 5 && (!$nothingtodo || isset($GLOBALS["wait"])))  {
    # if the script timed out in stage 5, reload the page to continue with the rest
    $reload++;
    if (!$GLOBALS["commandline"] && $num_per_batch && $batch_period) {
      if ($counters['sent'] + 10 < $GLOBALS["original_num_per_batch"]) {
        output($GLOBALS['I18N']->get('Less than batch size were sent, so reloading imminently'),1,'progress');
        $counters['delaysend'] = 10;
      } else {
        $counters['delaysend'] = (int)($batch_period - $totaltime); 
        $delaytime = 30; ## actually with the iframe we can reload fairly quickly
        output(s('Waiting for %d seconds before reloading',$delaytime),1,'progress');
      }
    }
    $counters['delaysend'] = (int)($batch_period - $totaltime); 
    if (empty($GLOBALS['inRemoteCall']) && empty($GLOBALS['commandline'])) {
      sleep($delaytime);
      printf( '<script type="text/javascript">
          document.location = "./?page=pageaction&action=processqueue&ajaxed=true&reload=%d&lastsent=%d&lastskipped=%d%s";
        </script></body></html>',$reload,$counters['sent'],$notsent,addCsrfGetToken());
    }
  } elseif ($script_stage == 6 || $nothingtodo) {
    foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
      $plugin->messageQueueFinished();
    }
    output($GLOBALS['I18N']->get('Finished, All done'),0);
    print '<script type="text/javascript">
      var parentJQuery = window.parent.jQuery;
      window.parent.allDone("'.s('All done').'");
      </script>';
  } else {
    output(s('Script finished, but not all messages have been sent yet.'));
  }
  if (!empty($GLOBALS['inRemoteCall'])) {
    ob_end_clean();
    print outputCounters();
    @ob_start();
  }
  
  if (empty($GLOBALS['inRemoteCall']) && empty($GLOBALS['commandline']) && empty($_GET['ajaxed'])) {
    return;
  } elseif (!empty($GLOBALS['inRemoteCall']) || !empty($GLOBALS['commandline'])) {
    @ob_end_clean();
  }
  exit;
}

register_shutdown_function("my_shutdown");

## some general functions
function finish ($flag,$message,$script_stage) {
  global $nothingtodo,$counters,$messageid;
  if ($flag == "error") {
    $subject = s("Maillist errors");
  } elseif ($flag == "info") {
    $subject = s("Maillist Processing info");
  }
  if (!$nothingtodo && !$GLOBALS['inRemoteCall']) {
    output(s('Finished this run'),1,'progress');
      print '<script type="text/javascript">
      var parentJQuery = window.parent.jQuery;
      parentJQuery("#progressmeter").updateSendProgress("'.$counters['sent'].','.$counters['total_users_for_message '.$messageid].'");
      </script>';
  } 
  if (!$GLOBALS['inRemoteCall'] && !TEST && !$nothingtodo && SEND_QUEUE_PROCESSING_REPORT) {
    $reportSent = false;
    
    ## @@TODO work out a way to deal with the order of processing the plugins
    ## as that can make a difference here.
    foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
      if (!$reportSent) {
        $reportSent = $plugin->sendReport($subject,$message);
      }
    }
    if (!$reportSent) {
      ## fall back to the central one
      $message .= "\n\n".s('To stop receiving these reports read:').' https://resources.phplist.com/system/config/send_queue_processing_report'. "\n\n";
      sendReport($subject,$message);
    }
      
  }
}

function ProcessError ($message) {
  global $report;
  $report .= $message;
  output( "Error: $message");
  exit;
}

function output ($message,$logit = 1,$target = 'summary') {
  global $report,$shadecount,$counters,$messageid;
  if (isset($counters['total_users_for_message '.$messageid])) {
    $total = $counters['total_users_for_message '.$messageid];
  } else {
    $total = 0;
  }
  if (!isset($shadecount)) $shadecount = 0;
  if (is_array($message)) {
    $tmp = '';
    foreach ($message as $key => $val) {
      $tmp .= $key .'='.$val.'; ';
    }
    $message = $tmp;
  }
  if (!empty($GLOBALS["commandline"])) {
    cl_output(strip_tags($message).' ['.$GLOBALS['processqueue_timer']->interval(1).'] ('.$GLOBALS["pagestats"]["number_of_queries"].')');
    $infostring = "[". date("D j M Y H:i",time()) . "] [CL]";
  } elseif ($GLOBALS['inRemoteCall']) {
    ## with a remote call we suppress output
    @ob_end_clean();
    $infostring = '';
    $message = '';
    @ob_start();
    return;
  } else {
    $infostring = "[". date("D j M Y H:i",time()) . "] [" . $_SERVER["REMOTE_ADDR"] ."]";
    #print "$infostring $message<br/>\n";
    $lines = explode("\n",$message);
    foreach ($lines as $line) {
      $line = preg_replace('/"/','\"',$line);

      ## contribution in forums, http://forums.phplist.com/viewtopic.php?p=14648
      //Replace the "&rsquo;" which is not replaced by html_decode
      $line = preg_replace("/&rsquo;/","'",$line);
      //Decode HTML chars
      $line = html_entity_decode($line,ENT_QUOTES,'UTF-8');
      
      print "\n".'<div class="output shade'.$shadecount.'">'.$line.'</div>';
      $line = str_replace("'", "\'", $line); // #16880 - avoid JS error
      print '<script type="text/javascript">
      var parentJQuery = window.parent.jQuery;
      parentJQuery("#processqueue'.$target.'").append(\'<div class="output shade'.$shadecount.'">'.$line.'</div>\');
      parentJQuery("#processqueue'.$target.'").animate({scrollTop:100000}, "slow");
      </script>';
      $shadecount = !$shadecount;
      for ($i=0;$i<10000; $i++) {
        print '  ';
        if ($i % 100 == 0) print "\n";
      }
    }
    flush();
  }

  $report .= "\n$infostring $message";
  if ($logit) {
    logEvent($message);
  }
  flush();
}

function outputCounters() {
  global $counters;
  $result = '';
  if (function_exists('json_encode')) { // only PHP5.2.0 and up
    return json_encode($counters);
  } else {
    ## keep track of which php versions we need to continue to support 
    $counters['PHPVERSION'] = phpversion();
    foreach ($counters as $key => $val) {
      $result .= $key.'='.$val.';';
    }
    return $result;
  }
}

function sendEmailTest ($messageid,$email) {
  global $report;
  if (VERBOSE)
    output($GLOBALS['I18N']->get('(test)').' '.$GLOBALS['I18N']->get('Would have sent').' '. $messageid .$GLOBALS['I18N']->get('to').' '. $email);
  else
    $report .= "\n".$GLOBALS['I18N']->get('(test)').' '.$GLOBALS['I18N']->get('Would have sent').' '. $messageid.$GLOBALS['I18N']->get('to').' '. $email;
  // fake a bit of a delay, 
  usleep(0.75 * 1000000);
  // and say it was fine.
  return true;  
}

# we don not want to timeout or abort
$abort = ignore_user_abort(1);
set_time_limit(600);
flush();

if (empty($reload)) { ## only show on first load
  output($GLOBALS['I18N']->get('Started'),0);
  if (defined('SYSTEM_TIMEZONE')) {
    output($GLOBALS['I18N']->get('Time now ').date('Y-m-d H:i'));
  }
}
#output('Will process for a maximum of '.$maxProcessQueueTime.' seconds '.MAX_PROCESSQUEUE_TIME);

## ask plugins if processing is allowed at all
foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
#  cl_output('Asking '.$pluginname);
  if (!$plugin->allowProcessQueue()) {
    output(s('Processing blocked by plugin %s',$pluginname));
    finish('info',s('Processing blocked by plugin %s',$pluginname));
    exit;
  }
}

if (empty($reload)) { ## only show on first load
  if (!empty($ISPrestrictions)) {
    output($ISPrestrictions);
  }
  if (is_file($ISPlockfile)) {
    ProcessError(s('Processing has been suspended by your ISP, please try again later'),1);
  }
}

if ($num_per_batch > 0) {
  if ($safemode) {
    output(s('In safe mode, batches are set to a maximum of 100'));
  }
  if ($original_num_per_batch != $num_per_batch) {
    if (empty($reload)) {
      output(s('Sending in batches of %d messages',$original_num_per_batch),0);
    }
    $diff = $original_num_per_batch - $num_per_batch;
    if ($diff < 0) $diff = 0;
    output(s('This batch will be %d emails, because in the last %d seconds %d emails were sent',$num_per_batch,$batch_period, $diff),0,'progress');
  } else {
    output(s('Sending in batches of %d emails',$num_per_batch),0,'progress');
  }
} elseif ($num_per_batch < 0) {
  output(s('In the last %d seconds more emails were sent (%d) than is currently allowed per batch (%d)', $batch_period,$recently_sent[0],$original_num_per_batch),0,'progress');
  $processed = 0;
  $script_stage = 5;
  $GLOBALS["wait"] = $batch_period;
  return;
}
$counters['batch_total'] = $num_per_batch;
$counters['failed_sent'] = 0;
$counters['invalid'] = 0;
$counters['sent'] = 0;

if (0 && $reload) {
  output(s('Sent in last run').": $lastsent",0,'progress');
  output(s('Skipped in last run').": $lastskipped",0,'progress');
}

$script_stage = 1; # we are active
$notsent = $unconfirmed = $cannotsend = 0;

## check for messages that need requeuing
$req = Sql_Query(sprintf('select id,requeueinterval,embargo < now() as inthepast from %s where requeueinterval > 0 and requeueuntil > now() and status = "sent"',$tables['message']));
while ($msg = Sql_Fetch_Assoc($req)) {
  if ($msg['inthepast']) {
    Sql_query(sprintf('update %s set status = "submitted",sendstart = null, embargo = date_add(now(),interval %d minute) where id = %d',$GLOBALS['tables']['message'],$msg['requeueinterval'],$msg['id']));
  } else {
    Sql_query(sprintf('update %s set status = "submitted",sendstart = null, embargo = date_add(embargo,interval %d minute) where id = %d',$GLOBALS['tables']['message'],$msg['requeueinterval'],$msg['id']));
  }
  ## @@@ need to update message data as well
}

$messagelimit = '';
## limit the number of campaigns to work on 
if (defined('MAX_PROCESS_MESSAGE')) {
  $messagelimit = sprintf(' limit %d ',MAX_PROCESS_MESSAGE);
}

$query
= " select id"
. " from ${tables['message']}"
. " where status not in ('draft', 'sent', 'prepared', 'suspended')"
. "   and embargo <= current_timestamp"
. " order by entered ".$messagelimit;
if (VERBOSE) {
  output($query);
}
$messages = Sql_query($query);
$num_messages = Sql_Num_Rows($messages);
if (Sql_Has_Error($database_connection)) {  ProcessError(Sql_Error($database_connection)); }
if ($num_messages) {
  $counters['status'] = $num_messages;
  if (empty($reload)) {
    output($GLOBALS['I18N']->get('Processing has started,'));
    if ($num_messages == 1) {
      output(s('One campaign to process.'));
    } else {
      output(s('%d campaigns to process.',$num_messages));
    }
  }
  clearPageCache();
  if (!$GLOBALS["commandline"] && empty($reload)) {
    output(s('Please leave this window open.').' '.s('phpList will process your queue until all messages have been sent.').' '.s('This may take a while'));
    if (SEND_QUEUE_PROCESSING_REPORT) {
      output(s('Report of processing will be sent by email'));
    }
  }
} else {
  ## check for a future embargo, to be able to report when it expires.
  $future = Sql_Fetch_Assoc_Query('select unix_timestamp(embargo) - unix_timestamp(current_timestamp) as waittime '
    . " from ${tables['message']}"
    . " where status not in ('draft', 'sent', 'prepared', 'suspended')"
    . " and embargo > current_timestamp"
    . " order by embargo asc limit 1");
  $counters['status'] = 'embargo';
  $counters['delaysend'] = $future['waittime'];
}

$script_stage = 2; # we know the messages to process
#include_once "footer.inc";
if (!isset($num_per_batch)) {
  $num_per_batch = 1000000;
}

while ($message = Sql_fetch_array($messages)) {
  $counters['campaign']++;
  $throttlecount = 0;

  $messageid = $message["id"];
  $counters['total_users_for_message '.$messageid] = 0;
  $counters['processed_users_for_message '.$messageid] = 0;
  $counters['failed_sent_for_message '.$messageid] = 0;
  
  if (!empty($getspeedstats)) output('start send '.$messageid);
  
  $msgdata = loadMessageData($messageid);
  foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
    $plugin->campaignStarted($msgdata);
  }
  
  if (!empty($msgdata['resetstats'])) {
    resetMessageStatistics($msgdata['id']);
    ## make sure to reset the resetstats flag, so it doesn't clear it every run
    setMessageData($msgdata['id'],'resetstats',0);
  }

  ## check the end date of the campaign
  $stopSending = false;
  if (!empty($msgdata['finishsending'])) {
    $finishSendingBefore = mktime($msgdata['finishsending']['hour'],$msgdata['finishsending']['minute'],0,$msgdata['finishsending']['month'],$msgdata['finishsending']['day'],$msgdata['finishsending']['year']);
    $secondsTogo = $finishSendingBefore - time();
    $stopSending = $secondsTogo < 0;
    if (empty($reload)) {
      ### Hmm, this is probably incredibly confusing. It won't finish then
      if (VERBOSE) {
        output(sprintf($GLOBALS['I18N']->get('sending of this campaign will stop, if it is still going in %s'),secs2time($secondsTogo)));
      }
    }
  }

  $userselection = $msgdata["userselection"]; ## @@ needs more work
  ## load message in cache
  if (!precacheMessage($messageid)) {
    ## precache may fail on eg invalid remote URL
    ## any reporting needed here?

    # mark the message as suspended
    Sql_Query(sprintf('update %s set status = "suspended" where id = %d',$GLOBALS['tables']['message'],$messageid));
    output(s('Error loading message, please check the eventlog for details'));
    if (MANUALLY_PROCESS_QUEUE) {
      # wait a little, otherwise the message won't show
      sleep(10);
    }
    continue; 
  }

  if (!empty($getspeedstats)) output('message data loaded ');  
  if (VERBOSE) {
 //   output($msgdata);
  }
  if (!empty($msgdata['notify_start']) && !isset($msgdata['start_notified'])) {
    $notifications = explode(',',$msgdata['notify_start']);
    foreach ($notifications as $notification) {
      sendMail($notification,$GLOBALS['I18N']->get('Campaign started'),
        sprintf($GLOBALS['I18N']->get('phplist has started sending the campaign with subject %s'),$msgdata['subject']."\n\n".
        sprintf($GLOBALS['I18N']->get('to view the progress of this campaign, go to http://%s'),getConfig('website').$GLOBALS['adminpages'].'/?page=messages&amp;tab=active')));
    }
    Sql_Query(sprintf('insert ignore into %s (name,id,data) values("start_notified",%d,current_timestamp)',
      $GLOBALS['tables']['messagedata'],$messageid));
  }

  if (empty($reload)) {
    output($GLOBALS['I18N']->get('Processing message').' '. $messageid);
  }
  
//  if(isset($GLOBALS['plugins']['rssmanager']) && $GLOBALS['plugins']['rssmanager']->enabled && $message["rsstemplate"]) {
// $processrss = 1;
//    output($GLOBALS['I18N']->get('Message').' '. $messageid.' '.$GLOBALS['I18N']->get('is an rss feed for').' '. $GLOBALS['I18N']->get($rssmessage));
//  } else {
//    $processrss = 0;
//  }

  flush();
  keepLock($send_process_id);
  $query
  = " update ${tables['message']}"
  . " set status = 'inprocess'"
  . " where id = ?";
  $status = Sql_Query_Params($query, array($messageid));
  $query
  = " update ${tables['message']}"
  . " set sendstart = current_timestamp"
  . " where sendstart is null and id = ?";
  $sendstart = Sql_Query_Params($query, array($messageid));
  if (empty($reload)) {
    output($GLOBALS['I18N']->get('Looking for users'));
  }
  if (Sql_Has_Error($database_connection)) {  ProcessError(Sql_Error($database_connection)); }

  # make selection on attribute, users who at least apply to the attributes
  # lots of ppl seem to use it as a normal mailinglist system, and do not use attributes.
  # Check this and take anyone in that case.
  
  ## keep an eye on how long it takes to find users, and warn if it's a long time
  $findUserStart = $processqueue_timer->elapsed(1);
  
  $rs = Sql_Query('select count(*) from ' . $tables['attribute']);
  $numattr = Sql_Fetch_Row($rs);

  $user_attribute_query = ''; #16552
  if ($userselection && $numattr[0]) {
    $res = Sql_Query($userselection);
    $counters['total_users_for_message'] = Sql_Num_Rows($res);
    if (empty($reload)) {
      output($counters['total_users_for_message'].' '.$GLOBALS['I18N']->get('users apply for attributes, now checking lists'),0,'progress');
    }
    $user_list = "";
    while ($row = Sql_Fetch_row($res)) {
      $user_list .= $row[0] . ",";
    }
    $user_list = substr($user_list,0,-1);
    if ($user_list)
      $user_attribute_query = " and listuser.userid in ($user_list)";
    else {
      if (empty($reload)) {
        output($GLOBALS['I18N']->get('No users apply for attributes'));
      }
      $query
      = " update ${tables['message']}"
      . " set status = 'sent', sent = current_timestamp"
      . " where id = ?";
      $status = Sql_Query_Params($query, array($messageid));
      finish("info","Message $messageid: \nNo users apply for attributes, ie nothing to do");
      $script_stage = 6;
      # we should actually continue with the next message
      return;
    }
  }
  if ($script_stage < 3)
    $script_stage = 3; # we know the users by attribute

  # when using commandline we need to exclude users who have already received
  # the email
  # we don't do this otherwise because it slows down the process, possibly
  # causing us to not find anything at all
  $exclusion = "";
  $doneusers = array();
  $skipusers = array();

## 8478, avoid building large array in memory, when sending large amounts of users.

/*
  $req = Sql_Query("select userid from {$tables["usermessage"]} where messageid = $messageid");
  $skipped = Sql_Affected_Rows();
  if ($skipped < 10000) {
    while ($row = Sql_Fetch_Row($req)) {
      $alive = checkLock($send_process_id);
      if ($alive)
        keepLock($send_process_id);
      else
        ProcessError($GLOBALS['I18N']->get('Process Killed by other process'));
      array_push($doneusers,$row[0]);
    }
  } else {
    output($GLOBALS['I18N']->get('Warning, disabling exclusion of done users, too many found'));
    logEvent($GLOBALS['I18N']->get('Warning, disabling exclusion of done users, too many found'));
  }

  # also exclude unconfirmed users, otherwise they'll block the process
  # will give quite different statistics than when used web based
#  $req = Sql_Query("select id from {$tables["user"]} where !confirmed");
#  while ($row = Sql_Fetch_Row($req)) {
#    array_push($doneusers,$row[0]);
#  }
  if (sizeof($doneusers))
    $exclusion = " and listuser.userid not in (".join(",",$doneusers).")";
*/

  if (USE_LIST_EXCLUDE) {
    if (VERBOSE) {
      output($GLOBALS['I18N']->get('looking for users who can be excluded from this mailing'));
    }
    if (count($msgdata['excludelist'])) {

      $query
      = " select userid"
      . " from ". $GLOBALS['tables']['listuser']
      . " where listid in (".join(',',$msgdata['excludelist']).")";
      if (VERBOSE) {
        output('Exclude query '.$query);
      }
      $req = Sql_Query($query);
      while ($row = Sql_Fetch_Row($req)) {
        Sql_Replace($tables['usermessage'], array('entered' => 'current_timestamp', 'userid' => $row[0], 'messageid' => $messageid, 'status' => "excluded"), array('userid', 'messageid'), false);
      }
    }
  }

/*
  ## 8478
  $query = sprintf('select distinct user.id from
    %s as listuser,
    %s as user,
    %s as listmessage
    where
    listmessage.messageid = %d and
    listmessage.listid = listuser.listid and
    user.id = listuser.userid %s %s %s',
    $tables['listuser'],$tables["user"],$tables['listmessage'],
    $messageid,
    $userconfirmed,
    $exclusion,
    $user_attribute_query);*/
  $queued = 0;
  if (defined('MESSAGEQUEUE_PREPARE') && MESSAGEQUEUE_PREPARE) {
    ## we duplicate messageid to match the query_params or the main query
    $query = sprintf('select userid from '.$tables['usermessage'].' where messageid = ? and messageid = ? and status = "todo"');
 #   cl_output($query.' '.$messageid);
    $queued_count = Sql_Query_Params($query, array($messageid, $messageid));
    $queued = Sql_Affected_Rows();
  # if (VERBOSE) {
      cl_output('found pre-queued subscribers '.$queued,0,'progress');
  #  }
  } 

  ## if the above didn't find any, run the normal search (again)
  if (empty($queued)) {
    ## remove pre-queued messages, otherwise they wouldn't go out
    $remove_query = sprintf('delete from '.$tables['usermessage'].' where messageid = ? and status = "todo"');
    Sql_Query_Params($remove_query, array($messageid));
    $removed = Sql_Affected_Rows();
    if ($removed) {
      cl_output('removed pre-queued subscribers '.$removed,0,'progress');
    }

    $query
    = ' select distinct u.id'
    . ' from %s as listuser'
    . '    cross join %s as u'
    . '    cross join %s as listmessage'
    . '    left join %s as um'
    . '       on (um.messageid = ? and um.userid = listuser.userid)'
    . ' where true'
    . '   and listmessage.messageid = ?'
    . '   and listmessage.listid = listuser.listid'
    . '   and u.id = listuser.userid'
    . '   and um.userid IS NULL'
    . '   and u.confirmed and !u.blacklisted and !u.disabled'
    . ' %s %s';
    $query = sprintf($query, $tables['listuser'], 
    $tables['user'], $tables['listmessage'], $tables['usermessage'], 
    $exclusion, $user_attribute_query);
  }

  if (VERBOSE) {
    output('User select query '.$query);
  }

  $userids = Sql_Query_Params($query, array($messageid, $messageid));
  if (Sql_Has_Error($database_connection)) {  ProcessError(Sql_Error($database_connection)); }

  # now we have all our users to send the message to
  $counters['total_users_for_message '.$messageid] = Sql_Num_Rows($userids);
  if ($skipped >= 10000) {
    $counters['total_users_for_message '.$messageid] -= $skipped;
  }
  
  $findUserEnd = $processqueue_timer->elapsed(1);

  if ($findUserEnd - $findUserStart > 300 && !$GLOBALS["commandline"]) {
    output($GLOBALS['I18N']->get('Warning, finding the subscribers to send out to takes a long time, consider changing to commandline sending'));
  }

  if (empty($reload)) {
    output($GLOBALS['I18N']->get('Found them').': '.$counters['total_users_for_message '.$messageid].' '.$GLOBALS['I18N']->get('to process'));
  }
  setMessageData($messageid,'to process',$counters['total_users_for_message '.$messageid]);

  if (defined('MESSAGEQUEUE_PREPARE') && MESSAGEQUEUE_PREPARE && empty($queued)) {
    ## experimental MESSAGEQUEUE_PREPARE will first mark all messages as todo and then work it's way through the todo's
    ## that should save time when running the queue multiple times, which avoids the user search after the first time
    ## only do this first time, ie empty($queued);
    ## the last run will pick up changes
    while ($userdata = Sql_Fetch_Row($userids)) {
      ## mark message/user combination as "todo"
      $userid = $userdata[0];    # id of the user
      Sql_Replace($tables['usermessage'], array('entered' => 'current_timestamp', 'userid' => $userid, 'messageid' => $messageid, 'status' => "todo"), array('userid', 'messageid'), false);
    }
    ## rerun the initial query, in order to continue as normal
    $query = sprintf('select userid from '.$tables['usermessage'].' where messageid = ? and messageid = ? and status = "todo"');
    $userids = Sql_Query_Params($query, array($messageid, $messageid));
    $counters['total_users_for_message '.$messageid] = Sql_Num_Rows($userids);
  }

  if (MAILQUEUE_BATCH_SIZE) {
    ## in case of sending multiple campaigns, reduce batch with "sent"
    $num_per_batch -= $counters['sent'];
    
    # send in batches of $num_per_batch users
    $batch_total = $counters['total_users_for_message '.$messageid];
    if ($num_per_batch > 0) {
      $query .= sprintf(' limit 0,%d',$num_per_batch);
      if (VERBOSE) {
        output($num_per_batch .'  query -> '.$query);
      }
      $userids = Sql_Query_Params($query, array($messageid, $messageid));
      if (Sql_Has_Error($database_connection)) {  ProcessError(Sql_Error($database_connection)); }
    } else {
      output($GLOBALS['I18N']->get('No users to process for this batch'),0,'progress');
      $userids = Sql_Query("select * from ${tables['user']} where id = 0");
    }
    $affrows = Sql_Num_Rows($userids);
    output($GLOBALS['I18N']->get('Processing batch of ').': '.$affrows,0,'progress');
  } 

  while ($userdata = Sql_Fetch_Row($userids)) {
    $counters['processed_users_for_message '.$messageid]++;
    $failure_reason = '';
    if ($num_per_batch && $counters['sent'] >= $num_per_batch) {
      output(s('batch limit reached').": ".$counters['sent']." ($num_per_batch)",1,'progress');
      $GLOBALS["wait"] = $batch_period;
      return;
    }
    
    $userid = $userdata[0];    # id of the user
    if (!empty($getspeedstats)) output('-----------------------------------'."\n".'start process user '.$userid);  
    $some = 1;
    set_time_limit(120);

    $secondsTogo = $finishSendingBefore - time();
    $stopSending = $secondsTogo < 0;

    # check if we have been "killed"
 #   output('Process ID '.$send_process_id);
    $alive = checkLock($send_process_id);

    ## check for max-process-queue-time
    $elapsed = $GLOBALS['processqueue_timer']->elapsed(1);
    if ($maxProcessQueueTime && $elapsed > $maxProcessQueueTime && $counters['sent'] > 0) {
      cl_output($GLOBALS['I18N']->get('queue processing time has exceeded max processing time ').$maxProcessQueueTime);
      break;
    } elseif ($alive && !$stopSending) {
      keepLock($send_process_id);
    } elseif ($stopSending) {
      output($GLOBALS['I18N']->get('Campaign sending timed out, is past date to process until'));
      break;
    } else {
      ProcessError($GLOBALS['I18N']->get('Process Killed by other process'));
    }

    # check if the message we are working on is still there and in process
    $status = Sql_Fetch_Array_query("select id,status from {$tables['message']} where id = $messageid");
    if (!$status['id']) {
      ProcessError($GLOBALS['I18N']->get('Message I was working on has disappeared'));
    } elseif ($status['status'] != 'inprocess') {
      $script_stage = 6;
      ProcessError($GLOBALS['I18N']->get('Sending of this message has been suspended'));
    }
    flush();

    ## 
    #Sql_Query_Params(sprintf('delete from %s where userid = ? and messageid = ? and status = "active"',$tables['usermessage']), array($userid,$messageid));

    # check whether the user has already received the message
    if (!empty($getspeedstats)) output('verify message can go out to '.$userid);  
    
    $um = Sql_query("select entered from {$tables['usermessage']} where userid = $userid and messageid = $messageid and status != 'todo'");
    if (!Sql_Num_Rows($um)) {
      ## mark this message that we're working on it, so that no other process will take it
      ## between two lines ago and here, should hopefully be quick enough
      $userlock = Sql_Replace($tables['usermessage'], array('entered' => 'current_timestamp', 'userid' => $userid, 'messageid' => $messageid, 'status' => "active"), array('userid', 'messageid'), false);

      if ($script_stage < 4)
        $script_stage = 4; # we know a subscriber to send to
      $someusers = 1;
      $users = Sql_query("select id,email,uniqid,htmlemail,confirmed,blacklisted,disabled from {$tables['user']} where id = $userid");

      # pick the first one (rather historical from before email was unique)
      $user = Sql_fetch_Assoc($users); 
      if ($user['confirmed'] && is_email($user['email'])) {
        $userid = $user['id'];    # id of the subscriber
        $useremail = $user['email']; # email of the subscriber
        $userhash = $user['uniqid'];  # unique string of the user
        $htmlpref = $user['htmlemail'];  # preference for HTML emails
        $confirmed = $user['confirmed'] && !$user['disabled']; ## 7 = disabled flag 
        $blacklisted = $user['blacklisted'];

        $cansend = !$blacklisted && $confirmed;
/*
## Ask plugins if they are ok with sending this message to this user
*/
      if (!empty($getspeedstats)) output('start check plugins ');  

      reset($GLOBALS['plugins']);
      while ($cansend && $plugin = current($GLOBALS['plugins']) ) {
        if (VERBOSE) {
          cl_output('Checking plugin '. $plugin->name());
        }
        $cansend = $plugin->canSend($msgdata, $user);
        if (!$cansend) {
          $failure_reason .= 'Sending blocked by plugin '.$plugin->name;
          $counters['send blocked by '.$plugin->name]++;
          if (VERBOSE) {
            cl_output('Sending blocked by plugin '.$plugin->name);
          }
        }

        next($GLOBALS['plugins']);
      } 
      if (!empty($getspeedstats)) output('end check plugins ');  

####################################
# Throttling

        $throttled = 0;
        if ($cansend && USE_DOMAIN_THROTTLE) {
          list($mailbox,$domainname) = explode('@',$useremail);
          $now = time();
          $interval = $now - ($now % DOMAIN_BATCH_PERIOD);
          if (!isset($domainthrottle[$domainname]) || !is_array($domainthrottle[$domainname])) {
            $domainthrottle[$domainname] = array(
              'interval' => '',
              'sent' => 0,
              'attempted' => 0,
            );
          } elseif (isset($domainthrottle[$domainname]['interval']) && $domainthrottle[$domainname]['interval'] == $interval) {
            $throttled = $domainthrottle[$domainname]['sent'] >= DOMAIN_BATCH_SIZE;
            if ($throttled) {
              $counters['send blocked by domain throttle']++;
              $domainthrottle[$domainname]['attempted']++;
              if (DOMAIN_AUTO_THROTTLE
                && $domainthrottle[$domainname]['attempted'] > 25 # skip a few before auto throttling
                && $num_messages <= 1 # only do this when there's only one message to process otherwise the other ones don't get a chance
                && $counters['total_users_for_message '.$messageid] < 1000 # and also when there's not too many left, because then it's likely they're all being throttled
              ) {
                $domainthrottle[$domainname]['attempted'] = 0;
                logEvent(sprintf($GLOBALS['I18N']->get('There have been more than 10 attempts to send to %s that have been blocked for domain throttling.'),$domainname));
                logEvent($GLOBALS['I18N']->get('Introducing extra delay to decrease throttle failures'));
                if (VERBOSE) {
                  output($GLOBALS['I18N']->get('Introducing extra delay to decrease throttle failures'));
                }
                if (!isset($running_throttle_delay)) {
                  $running_throttle_delay = (int)(MAILQUEUE_THROTTLE + (DOMAIN_BATCH_PERIOD / (DOMAIN_BATCH_SIZE * 4)));
                } else {
                  $running_throttle_delay += (int)(DOMAIN_BATCH_PERIOD / (DOMAIN_BATCH_SIZE * 4));
                }
                #output("Running throttle delay: ".$running_throttle_delay);
              } elseif (VERBOSE) {
                output(sprintf($GLOBALS['I18N']->get('%s is currently over throttle limit of %d per %d seconds').' ('.$domainthrottle[$domainname]['sent'].')',$domainname,DOMAIN_BATCH_SIZE,DOMAIN_BATCH_PERIOD));
              }
            }
          }
        }

        if ($cansend) {
          $success = 0;
          if (!TEST) {
            reset($GLOBALS['plugins']);
            while (!$throttled && $plugin = current($GLOBALS['plugins']) ) {
              $throttled = $plugin->throttleSend($msgdata, $user);
              if ($throttled) {
                if (!isset($counters['send throttled by plugin '.$plugin->name])) {
                  $counters['send throttled by plugin '.$plugin->name] = 0;
                }
                $counters['send throttled by plugin '.$plugin->name]++;
                $failure_reason .= 'Sending throttled by plugin '.$plugin->name;
              }
              next($GLOBALS['plugins']);
            } 
            if (!$throttled) {
              if (VERBOSE)
                output($GLOBALS['I18N']->get('Sending').' '. $messageid.' '.$GLOBALS['I18N']->get('to').' '. $useremail);
              $emailSentTimer = new timer();
              $counters['batch_count']++;
              $success = sendEmail($messageid,$useremail,$userhash,$htmlpref); // $rssitems Obsolete by rssmanager plugin
              if (!$success) {
                $counters['sendemail returned false total']++;
                $counters['sendemail returned false']++;
              } else {
                $counters['sendemail returned false'] = 0;
              }
              if ($counters['sendemail returned false'] > 10) {
                foreach ($GLOBALS['plugins'] as $pluginname => $plugin) {
                  $plugin->processError(s('Warning: a lot of errors while sending campaign %d',$messageid));
                }
              }

              if (VERBOSE) {
                output($GLOBALS['I18N']->get('It took').' '.$emailSentTimer->elapsed(1).' '.$GLOBALS['I18N']->get('seconds to send'));
              }
            } else {
              $throttlecount++;
            }
          } else {
            $success = sendEmailTest($messageid,$useremail);
            $counters['sentastest']++;
            $counters['batch_count']++;
            setMessageData($messageid,'sentastest',$counters['sentastest']);
          }

          #############################
          # tried to send email , process succes / failure
          if ($success) {
            if (USE_DOMAIN_THROTTLE) {
              list($mailbox,$domainname) = explode('@',$useremail);
              if ($domainthrottle[$domainname]['interval'] != $interval) {
                $domainthrottle[$domainname]['interval'] = $interval;
                $domainthrottle[$domainname]['sent'] = 1;
              } else {
                $domainthrottle[$domainname]['sent']++;
              }
            }
            $counters['sent']++;
            $counters['sent_users_for_message '.$messageid]++;
            $um = Sql_Replace($tables['usermessage'], array('entered' => 'current_timestamp', 'userid' => $userid, 'messageid' => $messageid, 'status' => "sent"), array('userid', 'messageid'), false);

//obsolete, moved to rssmanager plugin 
//            if (ENABLE_RSS && $pxrocessrss) {
//              foreach ($rssitems as $rssitemid) {
//                $status = Sql_query("update {$tables['rssitem']} set processed = processed +1 where id = $rssitemid");
//                $um = Sql_query("replace into {$tables['rssitem_user']} (userid,itemid) values($userid,$rssitemid)");
//              }
//              Sql_Query("replace into {$tables["user_rss"]} (userid,last) values($userid,date_sub(current_timestamp,interval 15 minute))");
//
//              }
           } else {
             $counters['failed_sent']++;
             $counters['failed_sent_for_message '.$messageid]++;
             ## need to check this, the entry shouldn't be there in the first place, so no need to delete it
             ## might be a cause for duplicated emails
             if (defined('MESSAGEQUEUE_PREPARE') && MESSAGEQUEUE_PREPARE) {
               Sql_Query_Params(sprintf('update %s set status = "todo" where userid = ? and messageid = ? and status = "active"',$tables['usermessage']), array($userid,$messageid));
             } else {
               Sql_Query_Params(sprintf('delete from %s where userid = ? and messageid = ? and status = "active"',$tables['usermessage']), array($userid,$messageid));
             }
             if (VERBOSE) {
               output($GLOBALS['I18N']->get('Failed sending to').' '. $useremail);
               logEvent("Failed sending message $messageid to $useremail");
             }
             # make sure it's not because it's an underdeliverable email
             # unconfirm this user, so they're not included next time
             if (!$throttled && !validateEmail($useremail)) {
               $unconfirmed++;
               $counters['email address invalidated']++;
               logEvent("invalid email address $useremail user marked unconfirmed");
               Sql_Query(sprintf('update %s set confirmed = 0 where email = "%s"',
                 $GLOBALS['tables']['user'],$useremail));
             }
           }
             
           if ($script_stage < 5) {
             $script_stage = 5; # we have actually sent one user
           }
           if (isset($running_throttle_delay)) {
             sleep($running_throttle_delay);
             if ($counters['sent'] % 5 == 0) {
               # retry running faster after some more messages, to see if that helps
               unset($running_throttle_delay);
             }
           } elseif (MAILQUEUE_THROTTLE) {
             usleep(MAILQUEUE_THROTTLE * 1000000);
           } elseif (MAILQUEUE_BATCH_SIZE && MAILQUEUE_AUTOTHROTTLE) {
             $totaltime = $GLOBALS['processqueue_timer']->elapsed(1);
             $msgperhour = (3600/$totaltime) * $counters['sent'];
             $msgpersec = $msgperhour / 3600;
             
             ##11336 - this may cause "division by 0", but 'secpermsg' isn't used at all
           #  $secpermsg = $totaltime / $counters['sent'];
             $target = (MAILQUEUE_BATCH_PERIOD / MAILQUEUE_BATCH_SIZE) * $counters['sent'];
             $delay = $target - $totaltime;

             if ($delay > 0) {
               if (VERBOSE) {
/* output($GLOBALS['I18N']->get('waiting for').' '.$delay.' '.$GLOBALS['I18N']->get('seconds').' '.
                   $GLOBALS['I18N']->get('to make sure we don\'t exceed our limit of ').MAILQUEUE_BATCH_SIZE.' '.
                   $GLOBALS['I18N']->get('messages in ').' '.MAILQUEUE_BATCH_PERIOD.$GLOBALS['I18N']->get('seconds')); */
                output(sprintf($GLOBALS['I18N']->get('waiting for %.1f seconds to meet target of %s seconds per message'),
                        $delay, (MAILQUEUE_BATCH_PERIOD / MAILQUEUE_BATCH_SIZE))
                );
               }
               usleep($delay * 1000000);
             }
           }
        } else {
          $cannotsend++;
          # mark it as sent anyway, because otherwise the process will never finish
          if (VERBOSE) {
            output($GLOBALS['I18N']->get('not sending to ').$useremail);
          }
          $um = Sql_query("replace into {$tables['usermessage']} (entered,userid,messageid,status) values(current_timestamp,$userid,$messageid,\"not sent\")");
        }

        # update possible other users matching this email as well,
        # to avoid duplicate sending when people have subscribed multiple times
        # bit of legacy code after making email unique in the database
#        $emails = Sql_query("select * from {$tables['user']} where email =\"$useremail\"");
#        while ($email = Sql_fetch_row($emails))
#          Sql_query("replace into {$tables['usermessage']} (userid,messageid) values($email[0],$messageid)");
      }  else {
        # some "invalid emails" are entirely empty, ah, that is because they are unconfirmed

        ## this is quite old as well, with the preselection that avoids unconfirmed users
        # it is unlikely this is every processed.

        if (!$user['confirmed'] || $user['disabled']) {
          if (VERBOSE)
            output($GLOBALS['I18N']->get('Unconfirmed user').': '.$userid.' '. $user['email'].' '.  $user['id']);
          $unconfirmed++;
          # when running from commandline we mark it as sent, otherwise we might get
          # stuck when using batch processing
         # if ($GLOBALS["commandline"]) {
            $um = Sql_query("replace into {$tables['usermessage']} (entered,userid,messageid,status) values(current_timestamp,$userid,$messageid,\"unconfirmed user\")");
         # }
        } elseif ($user['email'] || $user['id']) {
          if (VERBOSE) {
            output(s('Invalid email address').': ' . $user['email'].' '.  $user['id']);
          }
          logEvent(s('Invalid email address').': userid  '. $user['id'].'  email '. $user['email']);
          # mark it as sent anyway
          if ($user['id']) {
            $um = Sql_query(sprintf('replace into %s (entered,userid,messageid,status) values(current_timestamp,%d,%d,"invalid email address")',$tables['usermessage'],$userid,$messageid) );
            Sql_Query(sprintf('update %s set confirmed = 0 where id = %d',
              $GLOBALS['tables']['user'],$user['id']));
            addUserHistory(
                $user['email'],
                s('Subscriber marked unconfirmed for invalid email address'),
                s('Marked unconfirmed while sending campaign %d',$messageid)
            );
          }
          $counters['invalid']++;
        }
      }
    } else {

      ## and this is quite historical, and also unlikely to be every called
      # because we now exclude users who have received the message from the
      # query to find users to send to
      
      ## when trying to send the message, it was already marked for this user
      ## June 2010, with the multiple send process extension, that's quite possible to happen again

      $um = Sql_Fetch_Row($um);
      $notsent++;
      if (VERBOSE) {
        output($GLOBALS['I18N']->get('Not sending to').' '. $userdata[0].', '.$GLOBALS['I18N']->get('already sent').' '.$um[0]);
      }
    }
    $status = Sql_query("update {$tables['message']} set processed = processed + 1 where id = $messageid");
    $processed = $notsent + $counters['sent'] + $counters['invalid'] + $unconfirmed + $cannotsend + $counters['failed_sent'];
    #if ($processed % 10 == 0) {
    if (0) {
      output('AR'.$affrows.' N '.$counters['total_users_for_message '.$messageid].' P'.$processed.' S'.$counters['sent'].' N'.$notsent.' I'.$counters['invalid'].' U'.$unconfirmed.' C'.$cannotsend.' F'.$counters['failed_sent']);
      $rn = $reload * $num_per_batch;
      output('P '.$processed .' N'. $counters['total_users_for_message '.$messageid] .' NB'.$num_per_batch .' BT'.$batch_total .' R'.$reload.' RN'.$rn);
    }
    /* 
     * don't calculate this here, but in the "msgstatus" instead, so that
     * the total speed can be calculated, eg when there are multiple send processes
     * 
     * re-added for commandline outputting
     */
     
    
    $totaltime = $GLOBALS['processqueue_timer']->elapsed(1);
    if ($counters['sent'] > 0) {
      $msgperhour = (3600/$totaltime) * $counters['sent'];
      $secpermsg = $totaltime / $counters['sent'];
      $timeleft = ($counters['total_users_for_message '.$messageid] - $counters['sent']) * $secpermsg;
      $eta = date('D j M H:i',time()+$timeleft);
    } else {
      $msgperhour = 0;
      $secpermsg = 0;
      $timeleft = 0;
      $eta = $GLOBALS['I18N']->get('unknown');
    }
    setMessageData($messageid,'ETA',$eta);
    setMessageData($messageid,'msg/hr',"$msgperhour");
    
    cl_progress('sent '.$counters['sent'].' ETA '.$eta.' sending '.sprintf('%d',$msgperhour).' msg/hr');
    
    setMessageData($messageid,'to process',$counters['total_users_for_message '.$messageid] - $counters['sent']);
    setMessageData($messageid,'last msg sent',time());
  #  setMessageData($messageid,'totaltime',$GLOBALS['processqueue_timer']->elapsed(1));
    if (!empty($getspeedstats)) output('end process user '."\n".'-----------------------------------'."\n".$userid);  
  }
  $processed = $notsent + $counters['sent'] + $counters['invalid'] + $unconfirmed + $cannotsend + $counters['failed_sent'];
  output(s('Processed %d out of %d subscribers',$counters['processed_users_for_message '.$messageid],$counters['total_users_for_message '.$messageid]),1,'progress');

  if ($counters['total_users_for_message '.$messageid] - $counters['sent_users_for_message '.$messageid] <= 0 || $stopSending) {
    # this message is done
    if (!$someusers)
      output($GLOBALS['I18N']->get('Hmmm, No users found to send to'),1,'progress');
    if (!$counters['failed_sent']) {
      repeatMessage($messageid);
      $status = Sql_query(sprintf('update %s set status = "sent",sent = current_timestamp where id = %d',$GLOBALS['tables']['message'],$messageid));
            
      if (!empty($msgdata['notify_end']) && !isset($msgdata['end_notified'])) {
        $notifications = explode(',',$msgdata['notify_end']);
        foreach ($notifications as $notification) {
          sendMail($notification,$GLOBALS['I18N']->get('Message campaign finished'),
            sprintf($GLOBALS['I18N']->get('phpList has finished sending the campaign with subject %s'),$msgdata['subject'])."\n\n".
            sprintf($GLOBALS['I18N']->get('to view the results of this campaign, go to http://%s'),getConfig('website').$GLOBALS['adminpages'].'/?page=statsoverview&id='.$messageid)
            );
        }
        Sql_Query(sprintf('insert ignore into %s (name,id,data) values("end_notified",%d,current_timestamp)',
          $GLOBALS['tables']['messagedata'],$messageid));
      }
      $query
      = " select sent, sendstart"
      . " from ${tables['message']}"
      . " where id = ?";
      $rs = Sql_Query_Params($query, array($messageid));
      $timetaken = Sql_Fetch_Row($rs);
      output($GLOBALS['I18N']->get('It took').' '.timeDiff($timetaken[0],$timetaken[1]).' '.$GLOBALS['I18N']->get('to send this message'));
      sendMessageStats($messageid);
    }
    ## flush cached message track stats to the DB
    if (isset($GLOBALS['cached']['linktracksent'])) {
      flushClicktrackCache();
      # we're done with $messageid, so get rid of the cache
      unset($GLOBALS['cached']['linktracksent'][$messageid]);
    }

  } else {
    if ($script_stage < 5)
      $script_stage = 5;
  }
}

if (!$num_messages)
  $script_stage = 6; # we are done
# shutdown will take care of reporting  
