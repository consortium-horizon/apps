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
$PluginInfo['Sitemaps'] = array(
   'Name' => 'Sitemaps',
   'Description' => "Creates an XML sitemap based on http://www.sitemaps.org.",
   'Version' => '1.2.1',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "Tim Gunter",
   'AuthorEmail' => 'tim@vanillaforums.com',
   'AuthorUrl' => 'http://www.vanillaforums.com',
   'SettingsUrl' => '/settings/sitemaps',
   'SettingsPermission' => 'Garden.Settings.Manage'
);

class SitemapsPlugin extends Gdn_Plugin {
   
   /// Methods ///
   
   public function BuildCategorySiteMap($UrlCode, &$Urls) {
      $Category = CategoryModel::Categories($UrlCode);
      if (!$Category)
         return;
      
      $CountDiscussions = $Category['CountDiscussions'];
      $PageCount = PageNumber($CountDiscussions, C('Vanilla.Discussions.PerPage', 30));
      $Loc = Url('/categories/'.rawurlencode($Category['UrlCode'] ? $Category['UrlCode'] : $Category['CategoryID']), TRUE).'/{Page}';

      $Url = array(
          'Loc' => $Loc,
          'LastMode' => '',
          'ChangeFreq' => '',
          'PageCount' => $PageCount
      );

      $Urls[] = $Url;
   }
   
   public function Setup() {
      $this->Structure();
   }
   
   public function Structure() {
      Gdn::Router()->SetRoute('sitemapindex.xml', '/utility/sitemapindex.xml', 'Internal');
      Gdn::Router()->SetRoute('sitemap-(.+)', '/utility/sitemap/$1', 'Internal');
      Gdn::Router()->SetRoute('robots.txt', '/utility/robots', 'Internal');
   } 
   
   
   /// Event Handlers ///
   
   public function SettingsController_Sitemaps_Create($Sender) {
      $Sender->Permission('Garden.Settings.Manage');
      $Sender->SetData('Title', T('Sitemap Settings'));
      $Sender->AddSideMenu();
      $Sender->Render('Settings', '', 'plugins/Sitemaps');
   }
   
   /**
    * @param Gdn_Controller $Sender 
    */
   public function UtilityController_Robots_Create($Sender) {
      // Clear the session to mimic a crawler.
      Gdn::Session()->UserID = 0;
      Gdn::Session()->User = FALSE;
      $Sender->DeliveryMethod(DELIVERY_METHOD_XHTML);
      $Sender->DeliveryType(DELIVERY_TYPE_VIEW);
      $Sender->SetHeader('Content-Type', 'text/plain');
      
      $Sender->Render('Robots', '', 'plugins/Sitemaps');
   }
   
   /**
    * @param Gdn_Controller $Sender
    * @param type $Args 
    */
   public function UtilityController_SiteMapIndex_Create($Sender  ) {
      // Clear the session to mimic a crawler.
      Gdn::Session()->Start(0, FALSE, FALSE);
      $Sender->DeliveryMethod(DELIVERY_METHOD_XHTML);
      $Sender->DeliveryType(DELIVERY_TYPE_VIEW);
      $Sender->SetHeader('Content-Type', 'text/xml');
      
      $SiteMaps = array();
      
      if (class_exists('CategoryModel')) {
         $Categories = CategoryModel::Categories();
         foreach ($Categories as $Category) {
            if (!$Category['PermsDiscussionsView'] || $Category['CategoryID'] < 0)
               continue;
            
            $SiteMap = array(
                'Loc' => Url('/sitemap-category-'.rawurlencode($Category['UrlCode'] ? $Category['UrlCode'] : $Category['CategoryID']).'.xml', TRUE),
                'LastMod' => $Category['DateLastComment'],
                'ChangeFreq' => '',
                'Priority' => ''
            );
            $SiteMaps[] = $SiteMap;
         }
      }
      $Sender->SetData('SiteMaps', $SiteMaps);
      $Sender->Render('SiteMapIndex', '', 'plugins/Sitemaps');
   }
   
   public function UtilityController_SiteMap_Create($Sender, $Args = array()) {
      Gdn::Session()->Start(0, FALSE, FALSE);
      $Sender->DeliveryMethod(DELIVERY_METHOD_XHTML);
      $Sender->DeliveryType(DELIVERY_TYPE_VIEW);
      $Sender->SetHeader('Content-Type', 'text/xml');
      
      $Arg = StringEndsWith(GetValue(0, $Args), '.xml', TRUE, TRUE);
      $Parts = explode('-', $Arg, 2);
      $Type = strtolower($Parts[0]);
      $Arg = GetValue(1, $Parts, '');
      
      $Urls = array();
      switch ($Type) {
         case 'category':
            // Build the category site map.
            $this->BuildCategorySiteMap($Arg, $Urls);
            break;
         default:
            // See if a plugin can build the sitemap.
            $this->EventArguments['Type'] = $Type;
            $this->EventArguments['Arg'] = $Arg;
            $this->EventArguments['Urls'] =& $Urls;
            $this->FireEvent('SiteMap'.ucfirst($Type));
            break;
      }
      
      $Sender->SetData('Urls', $Urls);
      $Sender->Render('SiteMap', '', 'plugins/Sitemaps');
   }       
}