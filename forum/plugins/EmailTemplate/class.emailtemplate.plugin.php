<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2014 Guillermo Fernández
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$PluginInfo['EmailTemplate'] = array(
	'Name' => 'Email Templates',
	'Description' => 'Change email templates.',
	'Version' => '0.3',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => FALSE,
	'RegisterPermissions' => FALSE,
    'SettingsUrl' => '/settings/emailtemplate',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Guillermo Fernández',
	'AuthorEmail' => 'guillermofr@digio.es',
	'AuthorUrl' => 'http://www.digio.es',
	'License' => 'GPLv3'
);

class EmailTemplatePlugin extends Gdn_Plugin {

	public function SettingsController_EmailTemplate_Create($Sender) {
		$Sender->AddSideMenu('settings/emailtemplatename');

		if ($Sender->Form->IsPostBack()) {
			Gdn::SQL()->Insert('EmailTemplate', array(
			   'header' => $Sender->Form->GetFormValue('header'),
			   'footer' => $Sender->Form->GetFormValue('footer')
			));
		} 

		$values = Gdn::SQL()->Select('*')
	        ->From('EmailTemplate')
	        ->OrderBy('TemplateID','desc')
	        ->Limit(1,0)
	        ->Get();

	    $res = $values->Result();

		$Sender->SetData('header',$res[0]->header);
		$Sender->SetData('footer',$res[0]->footer);
		$Sender->Title($this->GetPluginName() . ' ' . T('Settings'));
		$Sender->Render($this->GetView('settings.php'));
	}

	public function Setup() {
      $this->Structure();
	}

	public function Structure(){

		//create table for templates
		Gdn::Database()->Structure()
		->Table('EmailTemplate')
		->PrimaryKey('TemplateID')
		->Column('header', 'text', TRUE)
		->Column('footer', 'text', TRUE)
		->Set();

		//check if empty to create a default one
		$values = Gdn::SQL()->Select('cm.*')
				   ->From('EmailTemplate cm')
				   ->Get();

		if ($values->Result() == false)
		Gdn::SQL()->Insert('EmailTemplate', array(
		   'header' => "<html>
                      <head>
                        <meta charset='utf-8'>
                      </head>
                      <body>
                      <div style='background-color:#eeeeee;padding:40px;font-family:Arial;'>
                      <table width='100%' bgcolor='#eeeeee' style='border:0px'>
                      <tr>
                        <td width='50px'></td>
                        <td>
                          <p>
                     ",
		   'footer' => "
                          </p>
                        </td>
                        <td align='right'><img style='float:right;width: 183px;height: 64px;' src='http://vanillaforums.org/themes/vforg/design/images/logo-vanilla.png'/></td>
                        <td width='50px'></td>
                      </tr>
                      <tr >
                        <td width='50px'></td>
                        <td colspan='2'>
                          <div style='font-weight:light;margin: 20px;'>
                            <small><strong>AVISO:</strong>Este correo electrónico se genera de forma automática por diferentes motivos, por favor, no lo respondas. Si lo has recibido por error, puedes ignorarlo. Puedes ponerte en contacto con nosotros en: xxxx@xxxx.com</small>
                          </div>
                        </td>
                        <td width='50px'></td>
                      </tr>
                      </table>
                      </div>
                      </body>
                      </html>"
		));

	}

}
