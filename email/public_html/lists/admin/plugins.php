<?php
require_once dirname(__FILE__).'/accesscheck.php';
#$_POST['pluginurl'] = '';

## handle non-JS ajax
if (isset($_GET['disable']) || isset($_GET['enable'])) {
  include "actions/plugins.php";
}

$pluginDestination = PLUGIN_ROOTDIR;
$pluginInfo = array();

if (!empty($_GET['delete'])) {
  $pluginname = preg_replace('/[^\w_-]/','',$_GET['delete']);
  if (is_file($pluginDestination.'/'.$pluginname.'.info.txt')) {
    $pluginDetails = unserialize(file_get_contents($pluginDestination.'/'.$pluginname.'.info.txt'));
    unlink($pluginDestination.'/'.$pluginname.'.info.txt');
    delFsTree($pluginDestination.'/'.$pluginname);
    unlink($pluginDestination.'/'.$pluginname.'.php');
    $_SESSION['action_result'] = s('The plugin '.$pluginname.' was removed');
  }
  Redirect('plugins');
}

if (!empty($_POST['pluginurl']) && class_exists('ZipArchive')) {
  if (!verifyToken()) {
    print Error(s('Invalid security token, please reload the page and try again'));
    return;
  }
  
  $packageurl = trim($_POST['pluginurl']);
  
  ## verify the url against known locations, and require it to be "zip".
  ## let's hope Github keeps this structure for a while
  if (!preg_match('~^https?://github\.com/([\w-_]+)/([\w-_]+)/archive/([\w]+)\.zip$~i',$packageurl,$regs)) {
    print Error(s('Invalid download URL, please reload the page and try again'));
    return;
  } else {
    $developer = $regs[1];
    $project_name = $regs[2];
    $branch = $regs[3];
  }
  print '<h3>'.s('Fetching plugin').'</h3>';
  
  print '<h2>'.s('Developer').' '.$developer.'</h2>';
  print '<h2>'.s('Project').' '.$project_name.'</h2>';
  
  $filename = '';
  $packagefile = file_get_contents($packageurl);
  if (!$packagefile) {
    print Error(s('Unable to download plugin package, check your connection'));
  } else {
    
    $filename = basename($packageurl);

    file_put_contents($GLOBALS['tmpdir'].'/phpListPlugin-'.$filename,$packagefile);
    print '<h3>'.s('Installing plugin').'</h3>';
  }
  $zip = new ZipArchive;
  if (!empty($filename) && $zip->open($GLOBALS['tmpdir'].'/phpListPlugin-'.$filename) === TRUE) {
    
    /* the zip may have a variety of directory structures, as Github seems to add at least one for the "branch" of 
     * the project and then the developer has some more. 
     * We look for a directory called "plugins" and place it's contents in the plugins folder.
     */
    
   
  #  var_dump($zip);
    //echo "numFiles: " . $zip->numFiles . "\n";
    //echo "status: " . $zip->status  . "\n";
    //echo "statusSys: " . $zip->statusSys . "\n";
    //echo "filename: " . $zip->filename . "\n";
    //echo "comment: " . $zip->comment . "\n";  
    
    $extractList = array();
    $dir_prefix = '';
    for ($i=0; $i<$zip->numFiles;$i++) {
  #      echo "index: $i<br/>\n";
  #    var_dump($zip->statIndex($i));
      $zipItem = $zip->statIndex($i);
      if (preg_match('~^([^/]+)/plugins/~',$zipItem['name'],$regs)) {
        array_push($extractList,$zipItem['name']);
        $dir_prefix = $regs[1];
      }
    }
    //var_dump($extractList);
    //var_dump($dir_prefix);
    @mkdir($GLOBALS['tmpdir'].'/phpListPluginInstall',0755);
  #  $destination = $GLOBALS['tmpdir'].'/phpListPluginDestination';
    @mkdir($pluginDestination,0755);
    if (is_writable($pluginDestination)) {
      if ($zip->extractTo($GLOBALS['tmpdir'].'/phpListPluginInstall',$extractList)) {
        $extractedDir = opendir($GLOBALS['tmpdir'].'/phpListPluginInstall/'.$dir_prefix.'/plugins/');
        $installOk = false;
        $pluginsForUpgrade = array();
        while ($dirEntry = readdir($extractedDir)) {
          if (!preg_match('/^\./',$dirEntry)) {
            print $dirEntry;
            if (preg_match('/^([\w]+)\.php$/',$dirEntry,$regs)) {
              $pluginInfo[$regs[1]] = array(
                'installUrl' => $packageurl,
                'developer' => $developer,
                'projectName' => $project_name,
                'installDate' => time(),
              );
            }
            
            $bu_dir = time();
            if (file_exists($pluginDestination.'/'.$dirEntry)) {
              print ' '.s('updating existing plugin');
              if (preg_match('/(.*)\.php$/',$dirEntry,$regs)) {
                $pluginsForUpgrade[] = $regs[1];
              }
              @rename($pluginDestination.'/'.$dirEntry,
                $pluginDestination.'/'.$dirEntry.'.'.$bu_dir);
            } else {
              print ' '.s('new plugin');
            }
     #       var_dump($pluginInfo);
              
            print '<br/>';
            if (copy_recursive($GLOBALS['tmpdir'].'/phpListPluginInstall/'.$dir_prefix.'/plugins/'.$dirEntry,
              $pluginDestination.'/'.$dirEntry)) {
                delFsTree($pluginDestination.'/'.$dirEntry.'.'.$bu_dir);
                $installOk = true;
            } elseif (is_dir($pluginDestination.'/'.$dirEntry.'.'.$bu_dir)) {
              ## try to place old one back
              @rename($pluginDestination.'/'.$dirEntry.'.'.$bu_dir,$pluginDestination.'/'.$dirEntry);
            }
          }  
        }
        foreach ($pluginInfo as $plugin => $pluginDetails) {
        #  print 'Writing '.$pluginDestination.'/'.$plugin.'.info.txt<br/>';
          file_put_contents($pluginDestination.'/'.$plugin.'.info.txt',serialize($pluginDetails));
        }
        ## clean up
        delFsTree($GLOBALS['tmpdir'].'/phpListPluginInstall');
        
        if ($installOk) {
          upgradePlugins($pluginsForUpgrade);
          
          print s('Plugin installed successfully');
        } else {
          print s('Error installing plugin');
        }
        $zip->close();   
        print '<hr/>'.PageLinkButton('plugins',s('Continue'));
        return;
      }
    } else {
      Error(s('Plugin directory is not writable'));
    }
  } else {
    Error(s('Invalid plugin package'));
  }

  print s('Plugin installation failed');
  print '<hr/>'.PageLinkButton('plugins',s('Continue'));
  return;
}


if (defined('PLUGIN_ROOTDIR') && !is_writable(PLUGIN_ROOTDIR)) {
  Info(s('The plugin root directory is not writable, please install plugins manually'));
} elseif (!class_exists('ZipArchive')) {
  Info(s('PHP has no <a href="http://php.net/zip">Zip capability</a>. This is required to allow installation from a remote URL'));
} else {
  print '<h3>'.s('Install a new plugin').'</h3>';
  print '<p><a class="resourceslink" href="http://resources.phplist.com/plugins/" title="'.s('Find plugins').'" target="_blank">'.s('Find plugins').'</a></p>';
  print formStart();
  print '<fieldset>
      <label for="pluginurl">'.s('Plugin package URL').'</label>
      <div type="field"><input type="text" id="pluginurl" name="pluginurl" /></div>
      <button type="submit" name="download">'.s('Install plugin').'</button>
      </fieldset></form>';
}

$ls = new WebblerListing(s('Installed plugins'));

if (empty($GLOBALS['allplugins'])) return;
ksort($GLOBALS['allplugins'], SORT_FLAG_CASE | SORT_STRING);

foreach ($GLOBALS['allplugins'] as $pluginname => $plugin) {
  $pluginDetails = array();
  $refl = new ReflectionObject($plugin);
  if (is_file(dirname($refl->getFileName()).'/'.$pluginname.'.info.txt')) {
    $pluginDetails = unserialize(file_get_contents($pluginDestination.'/'.$pluginname.'.info.txt'));
  }
  
  $ls->addElement($pluginname);
  $ls->setClass($pluginname, 'row1');
 # $ls->addColumn($pluginname,s('name'),$plugin->name);
 
  
  $details = '<div class="plugindetails">';  
  $details .= '<div class="detail"><span class="label">'.s('name').'</span>';
  $details .= '<span class="value">'.$plugin->name. '</span></div>';
  $details .= '<div class="detail"><span class="label">'.s('version').'</span>';
  $details .= '<span class="value">'.$plugin->version. '</span></div>';
  $details .= '<div class="detail"><span class="label">'.s('description').'</span>';
  $details .= '<span class="value">'.$plugin->description. '</span></div>';
  if (!empty($GLOBALS['developer_email'])) {
    ## show the origin of the plugin, as many may exist
    $details .= '<div class="detail"><span class="label">'.s('origin').'</span>';
    $details .= '<span class="value">'.$plugin->origin. '</span></div>';
  }
  
#  $ls->addRow($pluginname,s('description'),$plugin->description);
 # $ls->addColumn($pluginname,s('version'),$plugin->version);
  if (!empty($pluginDetails['installDate'])) {
  #  $ls->addColumn($pluginname,s('installed'),date('Y-m-d',$pluginDetails['installDate']));
    $details .= '<div class="detail"><span class="label">'.s('installed').'</span>';
    $details .= '<span class="value">'.date('Y-m-d',$pluginDetails['installDate']). '</span></div>';
  }
  if (!empty($pluginDetails['installUrl'])) {
 #   $ls->addRow($pluginname,s('installation Url'),$pluginDetails['installUrl']);
    $details .= '<div class="detail"><span class="label">'.s('installation Url').'</span>';
    $details .= '<span class="value">'.$pluginDetails['installUrl']. '</span></div>';
  }
  if (!empty($pluginDetails['developer'])) {
 #   $ls->addColumn($pluginname,s('developer'),$pluginDetails['developer']);
    $details .= '<div class="detail"><span class="label">'.s('developer').'</span>';
    $details .= '<span class="value">'.$pluginDetails['developer']. '</span></div>';
  }
  if (!empty($plugin->documentationUrl)) {
      $details .= '<div class="detail"><span class="label">'.s('More information').'</span>';
      $details .= '<span class="value"><a href="'.$plugin->documentationUrl. '" target="moreinfoplugin">'.s('Documentation Page'). '</a></span></div>';
  }
  if (pluginCanEnable($pluginname)) {
      $ls->addColumn($pluginname,s('enabled'),$plugin->enabled ? $GLOBALS['img_tick']:$GLOBALS['img_cross']);
      $ls->addColumn($pluginname,s('action'),$plugin->enabled ? 
        PageLinkAjax('plugins&disable='.$pluginname,'<button>Disable</button>') : 
        PageLinkAjax('plugins&enable='.$pluginname,'<button>Enable</button>'));
  } else {
      $ls->addColumn($pluginname,s('enabled'),$GLOBALS['img_cross']);
  }
  if (DEVVERSION) {
    //$ls->addColumn($pluginname,s('initialise'),$plugin->enabled ? 
      //PageLinkAjax('plugins&initialise='.$pluginname,s('Initialise')) : '');
    if ($plugin->enabled) {
      $details .= '<div class="detail"><span class="label">'.s('initialise').'</span>';
      $details .= '<span class="value">';
      $details .= PageLinkAjax('plugins&initialise='.$pluginname,s('Initialise'));
      $details .= '</span></div>';
    }
  }
  if (!empty($pluginDetails['installUrl']) && is_writable($pluginDestination.'/'.$pluginname)) {
    ## we can only delete the ones that were installed from the interface
    $ls->addColumn($pluginname,s('delete'),'<span class="delete"><a href="javascript:deleteRec(\'./?page=plugins&delete='.$pluginname. '\');" class="button" title="'.s('delete this plugin').'">'.s('delete').'</a></span>');
  }
  if (!pluginCanEnable($pluginname)) {
    $details .= '<div class="detail"><span class="label">'.s('Dependency check').'</span>';
    
    if ($plugin->dependencyFailure == 'No other editor enabled') {
        $details .= '<span class="value">'.s('Plugin can not be enabled, because "%s" is enabled.',$GLOBALS['editorplugin']).'</span></div>';
    } else {
        $details .= '<span class="value">'.s('Plugin can not be enabled.'). '<br/>'.s('Failure on system requirement <strong>%s</strong>',$plugin->dependencyFailure). '</span></div>';
    }
  }
  
  if (!empty($pluginDetails['installUrl']) && class_exists('ZipArchive')) {
    $updateForm = formStart();
    $updateForm .= '<input type="hidden" name="pluginurl" value="'.$pluginDetails['installUrl'].'"/>
        <button type="submit" name="update" title="'.s('update this plugin').'">'.s('update').'</button></form>';
    $ls->addColumn($pluginname,s('update'),$updateForm);
  }
  $details .= '</div>';
  $ls->addRow($pluginname,s('details'),$details);
  

}

print $ls->display();

