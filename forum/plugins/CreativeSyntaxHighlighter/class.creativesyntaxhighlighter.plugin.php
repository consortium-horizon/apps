<?php if (!defined('APPLICATION')) exit();

$PluginInfo['CreativeSyntaxHighlighter'] = array(
	'Name'                 => 'CreativeSyntaxHighlighter',
	'Description'          => 'Adds a <a href="http://alexgorbatchev.com/SyntaxHighlighter/" target="_blank">Code Syntax Highlighter</a> on discussions and comments.',
	'Version'              => '1.0',
    'PluginUrl'            => 'http://www.creativedreams.eu/creative-syntax-highlighter',
	'Author'               => 'Creative Dreams',
	'AuthorEmail'          => 'sandro@creativedreams.eu',
	'AuthorUrl'            => 'http://www.creativedreams.eu',
    'RequiredApplications' => array('Vanilla' => '>=2'),
    'RequiredTheme'        => FALSE,
    'RequiredPlugins'      => FALSE,
    'HasLocale'            => FALSE,
    'RegisterPermissions'  => FALSE,
    'SettingsUrl'          => FALSE,
    'SettingsPermission'   => FALSE
);

class CreativeSyntaxHighlighter implements Gdn_IPlugin
{
	public function Setup(){
		SaveToConfig('Plugins.CreativeSyntaxHighlighter.Style', 'shCoreDefault');
	}

    public function OnDisable(){
        RemoveFromConfig('Plugins.CreativeSyntaxHighlighter.Style');
    }

	public function DiscussionController_Render_Before(&$Sender){
        $this->_AddSyntaxHighlighter($Sender);
	}

	public function PostController_Render_Before(&$Sender){
        $this->_AddSyntaxHighlighter($Sender);
	}

    private function _AddSyntaxHighlighter($Sender) {
        $Sender->AddCssFile(C('Plugins.CreativeSyntaxHighlighter.Style','shCoreDefault').'.css', 'plugins/CreativeSyntaxHighlighter');
		$Sender->AddJsFile('shCore.js', 'plugins/CreativeSyntaxHighlighter');
		$Sender->AddJsFile('shAutoloader.js', 'plugins/CreativeSyntaxHighlighter');
		$Sender->AddJsFile('shInit.js','plugins/CreativeSyntaxHighlighter');
    }

}