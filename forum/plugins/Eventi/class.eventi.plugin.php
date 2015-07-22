<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['Eventi'] = array(
   'Description' => 'This plugin hooks every possible event and outputs a little chunk of signed HTML in-line.',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.0.10a'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'SettingsUrl' => FALSE,
   'SettingsPermission' => 'Garden.AdminUser.Only',
   'Author' => "Tim Gunter",
   'AuthorEmail' => 'tim@vanillaforums.com',
   'AuthorUrl' => 'http://www.vanillaforums.com'
);

class EventiPlugin extends Gdn_Plugin {

   public function __construct() {
      
   }
   
   public function Base_Render_Before($Sender) {
      $Sender->AddCssFile($this->GetResource('design/eventi.css', FALSE, FALSE));
      $Sender->AddJsFile($this->GetResource('js/eventi.js', FALSE, FALSE));
   }
   
   public function Base_All_Handler($Sender, $Args, $Key) {
      $Caller = $Sender->EventArguments['WildEventStack'];
      
      echo sprintf('<a href="#" class="Eventi">',$Key);
      echo sprintf(' <img src="%s" class="EventiIcon"/>', $this->GetWebResource('img/iconyellow.png', FALSE, TRUE), $Key);
      echo '   <div class="EventiPopup">';
      echo sprintf('      <div class="EventiEventName">%s</div>', $Key);
      
      $ArgList = array();
      foreach ($Caller['args'] as $Arg) {
         if (is_object($Arg))
            $ArgList[] = get_class($Arg);
         elseif (is_array($Arg))
            $ArgList[] = 'array{'.sizeof($Arg).'}';
         elseif (is_string($Arg) || is_numeric($Arg))
            $ArgList[] = "'".$Arg."'";
         elseif (is_bool($Arg))
            $ArgList[] = "b".(string)$Arg;
         else
            $ArgList[] = $Arg;
      }
      
      $Object = GetValue('object', $Caller, '');
      if (is_object($Object)) { $Object = get_class($Object); }
      if (strlen($Object)) $Object .= "::";
      echo sprintf('      <div>%s</div>', $Object.$Caller['function'].' ('.implode(',',$ArgList).')');
      
      $CallerFile = str_replace(Gdn::Request()->GetValue('DOCUMENT_ROOT'),'',$Caller['file']);
      echo sprintf('      <div>%s</div>', $CallerFile.':'.$Caller['line']);
      
      // EventArguments List
      if (sizeof($Sender->EventArguments) > 1) {
      ?>
         <div class="EventiArguments">
            <div class="EventiArgumentsTitle">Arguments:</div>
            <?php
               foreach ($Sender->EventArguments as $ArgKey => $ArgValue) {
                  if ($ArgKey == "WildEventStack") continue;
                  
                  if (is_object($ArgValue))
                     $ArgValue = get_class($ArgValue);
                  elseif (is_array($ArgValue))
                     $ArgValue = 'array{'.sizeof($ArgValue).'}';
                  elseif (is_string($ArgValue) || is_numeric($ArgValue))
                     $ArgValue = "'".$ArgValue."'";
                  elseif (is_bool($ArgValue))
                     $ArgValue = "b".(string)$ArgValue;
                     
                  echo sprintf('<div class="EventiArgument">%s: %s</div>', $ArgKey, $ArgValue);
               }
            ?>
         </div>
      <?php
      }
      
      echo '   </div>';
      echo '</a>';
   }
   
   public function Setup() {
   
   }
   
}
