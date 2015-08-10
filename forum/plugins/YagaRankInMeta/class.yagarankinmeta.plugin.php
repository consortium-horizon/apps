<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2014 Zachary Doll
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
$PluginInfo['YagaRankInMeta'] = array(
	'Name' => 'Yaga - Rank In Meta',
	'Description' => 'Adds the User Rank to the author meta on discussions.',
	'Version' => '1.0',
	'RequiredApplications' => array('Yaga' => '0.5'),
	'MobileFriendly' => TRUE,
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class YagaRankInMeta extends Gdn_Plugin {

  public function DiscussionController_AuthorInfo_Handler($Sender) {
    $Author = $Sender->EventArguments['Author'];
    $RankID = $Author->RankID;
    
    // Don't iterate unless rank ID is set
    if(is_null($RankID)) {
      return;
    }
    $RankModel = Yaga::RankModel();
    $Ranks = $RankModel->Get();
    $AuthorRank = new stdClass();
    $AuthorRank->Name = NULL;
    foreach($Ranks as $Rank) {
      if($Rank->RankID == $RankID) {
        $AuthorRank = $Rank;
        break;
      }
    }
    
    echo WrapIf($AuthorRank->Name, 'span', array('class' => 'MItem Rank Rank-' . Gdn_Format::Url($AuthorRank->Name)));
  }

}
