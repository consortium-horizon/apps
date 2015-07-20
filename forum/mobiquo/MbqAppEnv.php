<?php

defined('MBQ_IN_IT') or exit;

/**
 * application environment class
 * 
 * @since  2012-7-2
 * @author Wu ZeTao <578014287@qq.com>
 */
Class MbqAppEnv extends MbqBaseAppEnv {
    
    /* this class fully relys on the application,so you can define the properties what you need come from the application. */
    public $rootUrl;    /* site root url */
    public $oCurStdUser;   /* current user object */
    public $otherParams;    /* other parameters */
    
    public function __construct() {
        parent::__construct();
        $this->otherParams = array(
            'needNativeRegister' => true    //used for sign_in and register method
        );
    }
    
    /**
     * application environment init
     */
    public function init() {
        /* modified from index.php */
        define('APPLICATION', 'Vanilla');
        //define('APPLICATION_VERSION', '2.0.18.4');
        //get APPLICATION_VERSION
        preg_match('/define\(\'APPLICATION_VERSION\', \'(.*?)\'\)\;/i', file_get_contents(MBQ_PARENT_PATH.'index.php'), $exttMatches);
        define('APPLICATION_VERSION', $exttMatches[1]);
        /*
        Copyright 2008, 2009 Vanilla Forums Inc.
        This file is part of Garden.
        Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
        Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
        You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
        Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
        */
        
        // Report and track all errors.
        //error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR);
        //ini_set('display_errors', 'on');
        //ini_set('track_errors', 1);
        
        //ob_start();
        @ ob_start();
        
        // 0. Start profiling if requested in the querystring
        if (isset($_GET['xhprof']) && $_GET['xhprof'] == 'yes')
           define('PROFILER', TRUE);
        
        if (defined('PROFILER') && PROFILER) {
           $ProfileWhat = 0;
           
           if (isset($_GET['memory']) && $_GET['memory'] == 'yes')
              $ProfileWhat += XHPROF_FLAGS_MEMORY;
           
           if (isset($_GET['cpu']) && $_GET['cpu'] == 'yes')
              $ProfileWhat += XHPROF_FLAGS_CPU;
           
           xhprof_enable($ProfileWhat);
        }
        
        // 1. Define the constants we need to get going.
        define('DS', '/');
        //define('PATH_ROOT', dirname(__FILE__));
        define('PATH_ROOT', substr(MBQ_PARENT_PATH, 0, -1));
        
        // 2. Include the bootstrap to configure the framework.
        require_once(PATH_ROOT.'/bootstrap.php');
        
        // 3. Create and configure the dispatcher.
        // TIM: Removed this change temporarily for .com hosting
        // Gdn::Authenticator()->StartAuthenticator();
        $Dispatcher = Gdn::Dispatcher();
        
        $EnabledApplications = Gdn::ApplicationManager()->EnabledApplicationFolders();
        $Dispatcher->EnabledApplicationFolders($EnabledApplications);
        $Dispatcher->PassProperty('EnabledApplications', $EnabledApplications);
        
        // 4. Process the request.
        $Dispatcher->Dispatch();
        //$Dispatcher->Cleanup();
        
        // 5. Finish profiling and save results to disk, if requested
        if (defined('PROFILER') && PROFILER) {
           $xhprof_data = xhprof_disable();
           
           if (is_null($XHPROF_ROOT))
              die("Unable to save XHProf data. \$XHPROF_ROOT not defined in index.php");
        
           if (is_null($XHPROF_SERVER_NAME))
              die("Unable to save XHProf data. \$XHPROF_SERVER_NAME not defined in index.php");
           
           //
           // Saving the XHProf run
           // using the default implementation of iXHProfRuns.
           //
           include_once("{$XHPROF_ROOT}/xhprof_lib/utils/xhprof_lib.php");
           include_once("{$XHPROF_ROOT}/xhprof_lib/utils/xhprof_runs.php");
        
           $xhprof_runs = new XHProfRuns_Default();
           $xhprof_namespace = 'vanilla';
        
           // Save the run under a namespace              
           //
           // **NOTE**:
           // By default save_run() will automatically generate a unique
           // run id for you. [You can override that behavior by passing
           // a run id (optional arg) to the save_run() method instead.]
           //
           $run_id = $xhprof_runs->save_run($xhprof_data, $xhprof_namespace);
        
           echo "http://{$XHPROF_SERVER_NAME}/index.php?run={$run_id}&source={$xhprof_namespace}\n";
        
        }
        
        $exttWebRoot = (strrpos($_SERVER['REQUEST_URI'], '/') === 0) ? '' : substr($_SERVER['REQUEST_URI'], 1, strrpos($_SERVER['REQUEST_URI'], '/') - 1);
        Gdn::Request()->WebRoot($exttWebRoot);    //!!! fixed important site root url parsing issue.
        
        $CurrentUser = Gdn::Session()->User;
        if (is_object($CurrentUser)) {
            $this->oCurStdUser = $CurrentUser;
            $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
            $oMbqRdEtUser->initOCurMbqEtUser();
        }
        if (MbqMain::isJsonProtocol()) {
            $this->rootUrl = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].str_ireplace('tapatalk.php', '', $_SERVER['SCRIPT_NAME']);
        } else {
            $this->rootUrl = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false ? 'http' : 'https').'://'.$_SERVER['SERVER_NAME'].str_ireplace('mobiquo.php', '', $_SERVER['SCRIPT_NAME']);
        }
        
        //make online users
        $oMbqRdEtUser = MbqMain::$oClk->newObj('MbqRdEtUser');
        $oMbqRdEtUser->getObjsMbqEtUser(NULL, array('case' => 'online'));
        
        //calculate the flags for In App Registration
        MbqMain::$oMbqCm->exttMakeFlags();
        
        @ ob_end_clean();   //Clean (erase) the output buffer and turn off output buffering
    }
    
    /**
    * check whether a 3rd plugin is enabled
    * 
    * @param  string  $pluginName  plugins key name  
    * @param  string  $version plugin version
    * @return  Boolean
     */
    public function check3rdPluginEnabled($pluginName, $version = NULL) {
        //print_r(Gdn::PluginManager()->EnabledPlugins(), true)
        //return Gdn::PluginManager()->CheckPlugin($pluginName);
        if ($version) {
            $arr = Gdn::PluginManager()->EnabledPlugins();
            if ($arr[$pluginName] && $arr[$pluginName]['Version'] == $version) {
                return true;
            }
        } else {
            return Gdn::PluginManager()->CheckPlugin($pluginName);
        }
        return false;
    }
    
}

?>