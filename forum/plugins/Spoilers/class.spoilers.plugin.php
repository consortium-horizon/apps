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
$PluginInfo['Spoilers'] = array(
   'Name' => 'Spoilers',
   'Description' => "This plugin allows users to hide sensitive or revealing information behind clickable barriers to prevent accidental spoilers.",
   'Version' => '0.1.1',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => FALSE,
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "Tim Gunter",
   'AuthorEmail' => 'tim@vanillaforums.com',
   'AuthorUrl' => 'http://www.vanillaforums.com'
);

class SpoilersPlugin extends Gdn_Plugin {

   public function DiscussionController_Render_Before(&$Sender) {
      $this->PrepareController($Sender);
   }
   
   public function PostController_Render_Before(&$Sender) {
      $this->PrepareController($Sender);
   }
   
   protected function PrepareController(&$Sender) {
      $Sender->AddJsFile($this->GetResource('js/spoilers.js', FALSE, FALSE));
      $Sender->AddCssFile($this->GetResource('css/spoilers.css', FALSE, FALSE));
   }
   
   public function DiscussionController_BeforeCommentDisplay_Handler(&$Sender) {
      $this->RenderSpoilers($Sender);
   }
   
   public function PostController_BeforeCommentDisplay_Handler(&$Sender) {
      $this->RenderSpoilers($Sender);
   }
   
   protected function RenderSpoilers(&$Sender) {
      if (isset($Sender->EventArguments['Discussion'])) 
         $Data = $Sender->EventArguments['Discussion'];
         
      if (isset($Sender->EventArguments['Comment'])) 
         $Data = $Sender->EventArguments['Comment'];

      $Data->Body = preg_replace_callback("/(\[spoiler(?:=\"?([\d\w_',.? ]+)\"?)?\])/", array($this, 'SpoilerCallback'), $Data->Body);
      $Data->Body = str_replace('[/spoiler]','</div></div>',$Data->Body);
   }
   
   protected function SpoilerCallback($Matches) {
      $Attribution = T('Spoiler: %s');
      $SpoilerText = (sizeof($Matches) > 2) ? $Matches[2] : NULL;
      if (is_null($SpoilerText)) $SpoilerText = '';
      else
         $SpoilerText = "<span>{$SpoilerText}</span>";
      $Attribution = sprintf($Attribution,$SpoilerText);
      return <<<BLOCKQUOTE
      <div class="UserSpoiler"><div class="SpoilerTitle">{$Attribution}</div><div class="SpoilerReveal"></div><div class="SpoilerText">
BLOCKQUOTE;
   }
   
   public function Setup() {
      // Nothing to do here!
   }
   
   public function Structure() {
      // Nothing to do here!
   }
         
}