<?php if (!defined('APPLICATION')) exit();


abstract class MarketPlaceUtilityDomain extends Gdn_Plugin {
  
    protected $Workers = array();
    private $WorkerName = 'Utility';

    function __construct(){
        parent::__construct();
    }

    public function Utility(){
        $WorkerName = $this->WorkerName;
        if(!GetValue($WorkerName, $this->Workers)){
            $WorkerClass = $this->GetPluginIndex().$WorkerName;
            $this->LinkWorker($WorkerName,$WorkerClass);
        }
        return $this->Workers[$WorkerName];
    }
  
  
  
    public function CalledFrom(){
        return $this->WorkerName;
    }
  
    public function LinkWorker($WorkerName,$WorkerClass){
        $Args = func_get_args(); 
        switch(count($Args)){
            case 2;
                $Worker = new $WorkerClass();
                break;
            case 3:
                $Worker = new $WorkerClass($Args[2]);
                break;
            case 4:
                $Worker = new $WorkerClass($Args[2],$Args[3]);
                break;
            case 5:
                $Worker = new $WorkerClass($Args[2],$Args[3],$Args[4]);
                break;
            default:
                $Ref = new ReflectionClass($WorkerClass);
                $Worker = $Ref->newInstanceArgs($Args);
                break;  
        }
        $Worker->Plgn = $this;
        $this->Workers[$WorkerName] = $Worker;
    }
  
    abstract public function PluginSetup();
  
}


class MarketPlaceUtility {
  
    private static $LoadMaps = array();
  
    public static function RegisterLoadMap($Match,$Folder,$File,$LowercaseMatches=TRUE){
        self::$LoadMaps[] = array(
            'Match' => $Match,
            'Folder' => $Folder,
            'File' =>$File,
            'LowercaseMatches' => $LowercaseMatches
        );
    }
  
    private static function LoadMapParse($Matches,$Str){
        foreach ($Matches As $MatchI => $MatchV){
            $Str = preg_replace('`\{?\$\{?Matches\['.$MatchI.'\]\}?`',$MatchV,$Str);
        }
        return $Str;
    }
  
    public static function Load($Class){
        $Maps = self::$LoadMaps;
        foreach ($Maps As $Map){
            $Matches = array();
      
            if(preg_match($Map['Match'],$Class,$Matches)){
            
                if($Map['LowercaseMatches'])
                    $Matches = array_map('strtolower',$Matches);
                    $Map['Folder'] = self::LoadMapParse($Matches,$Map['Folder']);
                    $Map['File'] = self::LoadMapParse($Matches,$Map['File']);
                    require_once(PATH_PLUGINS.DS.'MarketPlace'.($Map['Folder'] ? DS.$Map['Folder']: '').DS.$Map['File']);
                    break;
            }
        }
    }

    public static function InitLoad(){  
        spl_autoload_register('MarketPlaceUtility::Load');
    }
 
    public function HotLoad($Force =  FALSE) {
        if(C('Plugins.'.$this->Plgn->GetPluginIndex().'.Version')!=$this->Plgn->PluginInfo['Version']){
            $this->Plgn->PluginSetup();     
      SaveToConfig('Plugins.'.$this->Plgn->GetPluginIndex().'.Version', $this->Plgn->PluginInfo['Version']);
        }
    }
    
    /* 
     * Pluggable dispatcher
     * e.g. public function PluginNameController_Test_Create($Sender){}
     */
    
    public function MiniDispatcher($Sender, $PluggablePrefix = NULL, $LocalPrefix = NULL){
        $PluggablePrefix = $PluggablePrefix ? $PluggablePrefix : $this->Plgn->GetPluginIndex().'Controller_';
        $LocalPrefix = $LocalPrefix ? $LocalPrefix : 'Controller_';
        $Sender->Form = new Gdn_Form();
     
        $Plugin = $this;

        $ControllerMethod = '';
        if(count($Sender->RequestArgs)){
            list($MethodName) = $Sender->RequestArgs;
        }else{
            $MethodName = 'Index';     
        }

        $DeclaredClasses = get_declared_classes();

        $TempControllerMethod = $LocalPrefix.$MethodName;
        if (method_exists($Plugin, $TempControllerMethod)){
            $ControllerMethod = $TempControllerMethod;
        }
    
        $WorkerName =  $this->Plgn->CalledFrom();
        if (method_exists($Plugin->Plgn->$WorkerName(), $TempControllerMethod)){
            $Plugin = $Plugin->Plgn->$WorkerName();
            $ControllerMethod = $TempControllerMethod;
        }
    
        if(!$ControllerMethod){
            $TempControllerMethod = $PluggablePrefix.$MethodName.'_Create';

            foreach ($DeclaredClasses as $ClassName) {
                if (Gdn::PluginManager()->GetPluginInfo($ClassName)){
                    $CurrentPlugin = Gdn::PluginManager()->GetPluginInstance($ClassName);
                    if($CurrentPlugin && method_exists($CurrentPlugin, $TempControllerMethod)){
                        $Plugin = $CurrentPlugin;
                        $ControllerMethod = $TempControllerMethod;
                        break;
                    }
                }
            }

        }
        if (method_exists($Plugin, $ControllerMethod)) {
            $Sender->Plugin = $Plugin;
            return call_user_func(array($Plugin,$ControllerMethod),$Sender);
        } else {
            $PluginName = get_class($this);
            throw NotFoundException();
        }
    }
    
    /* 
     * Set view that can be copied over to current theme
     * e.g. view -> current_theme/views/plugins/PluginName/view.php
     */
    
    public function ThemeView($View){
        $ThemeViewLoc = CombinePaths(array(
            PATH_THEMES, Gdn::Controller()->Theme, 'views', $this->Plgn->GetPluginFolder()
        ));
    
        if(file_exists($ThemeViewLoc.DS.$View.'.php')){
            $View=$ThemeViewLoc.DS.$View.'.php';
        }else{
            $View=$this->Plgn->GetView($View.'.php');
        }


        return $View;
        
    }
  
    /**
     *  @@ GDN Plugin Scaffold @@
     *
     *  Add a route on the fly
     *
     *  Typically set in Base_BeforeLoadRoutes_Handler
     *
     *  @param string $Routes loaded
     *  @param string $Route RegExp of route
     *  @param string $Destination to rout to
     *  @param string $Type of redirect (optional), default 'Internal' options Internal,Temporary,Permanent,NotAuthorized,NotFound
     *  @param bool $OneWay if an Internal request prevents direct access to destination  (optional), default FALSE
     *
     *  @return void
     */

    public function DynamicRoute(&$Routes, $Route, $Destination, $Type = 'Internal', $Oneway = FALSE, $Redirect = FALSE){
        $Key = str_replace('_','/',base64_encode($Route));
        $Routes[$Key] = array($Destination, $Type);
        if($Oneway && $Type == 'Internal'){
            if(strtolower(Gdn::Request()->Path()) && strpos(strtolower($Destination), strtolower(Gdn::Request()->Path()))===0){
                if($Redirect){
                    Redirect(Url($Redirect, TRUE), 301);
                }else{
                    Gdn::Dispatcher()->Dispatch('Default404');
                }
                 
                exit;
            }
        }
    }
}
