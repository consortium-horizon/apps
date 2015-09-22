<?php

$PluginInfo['VanillaSEO'] = array (
 	'Name'					=>	'Vanilla SEO',
	'Description'			=>	'Vanilla SEO is your all in one plugin for optimizing your Vanilla forum for search engines.',
	'Version'				=>	'0.2.1',
	'RequiredApplications'	=>	array('Vanilla' => '2.0.18'),
	'RequiredPlugins'		=>	FALSE,
	'HasLocale'				=>	FALSE,
	'SettingsUrl'			=>	'/dashboard/plugin/seo',
	'SettingsPermission'	=>	'Garden.Settings.Manage',
	'Author'				=>	'Jamie Chung',
	'AuthorEmail'			=>	'me@jamiechung.me',
	'AuthorUrl'				=>	'http://www.jamiechung.me'
);

class VanillaSEOPlugin extends Gdn_Plugin 
{
	// All available %tags%.
	public $tags = array (
		'title'			=>	'Discussion Title',
		'category'		=>	'Category Name',
		'garden'		=>	'Vanilla Banner Title',
		'search'		=>	'Search Query'
	);
	
	// Default titles for each part of the vanilla rewrite scheme.
	public $dynamic_titles = array (
		
		// CATEGORIES
		'categories_all'		=>	array(
					'default' 	=> 'All Categories on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'All Categories',
					'info'		=> 'List page of all the categories available for users to post discussions.',
					'examples'	=> array('/categories/all')
		),
		'category_single'		=>	array(
					'default' 	=> '%category% Discussions on %garden%',
					'fields'	=> array('garden', 'category'),
					'name'		=> 'Single Category Page',
					'info'		=> 'Category view displaying relevent discussions.',
					'examples'	=> array('/categories/general-forum', '/categories/general-forum/p2', '/categories/general-forum/feed.rss')
		),
		'category_discussions'	=>	array(
					'default' 	=> 'View Discussions and Categories on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Sample Categories',
					'info'		=> 'Showing all categories and a few discussions from each category.',
					'examples'	=> array('/categories')
		),		
		
		// ACTIVITY
		'activity'				=> array(
					'default'	=> 'Recent Activity on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Recent Activity',
					'info'		=> 'Page listing recent activity on your vanilla forum.',
					'examples'	=> array('/activity')
		),		
		
		// DISCUSSIONS
		'discussions'			=> array(
					'default'	=> 'Recent Discussions on %garden%',
					'fields'	=> array('garden'),
					'name'		=> 'Discussions Home Page',
					'info'		=> 'Page listing recent discussions on your vanilla forum.',
					'examples'	=>	array('/discussions')
		),		
		'discussion_single'		=> array(
					'default'	=> '%title% - %category% Discussions on %garden%',
					'fields'	=> array('garden', 'title', 'category'),
					'name'		=> 'Single Discussion Page',
					'info'		=> 'Viewing a single discussion thread.',
					'examples'	=> array('/discussions/23/this-is-a-post-title')
		),
			
		// SEARCH
		'search_results'		=> array(
					'default'	=> '%search% - Search Results on %garden%',
					'fields'	=> array('garden', 'search'),
					'name'		=> 'Search Results Page',
					'info'		=> 'Adds the search query in the page title of search results pages.',
					'examples'	=>	array('/search?Search=hello+world')
		),
	);

 	private function GetTitle ( $type )
	{
		if ( C('Plugins.SEO.DynamicTitles.'.$type) )
		{
			return strip_tags(C('Plugins.SEO.DynamicTitles.'.$type));
		}
		else
		{
			return $this->dynamic_titles[$type]['default'];
		}
	}
 	
	public function Base_GetAppSettingsMenuItems_Handler ( $Sender )
	{
		$Menu = $Sender->EventArguments['SideMenu'];
		$Menu->AddItem('Site Settings', T('Settings'));
		$Menu->AddLink('Site Settings', T('Search Engine Optimization'), 'plugin/seo', 'Garden.Settings.Manage');
	}
	
	public function PluginController_SEO_Create ( $Sender )
	{
		$Sender->Permission('Garden.Settings.Manage');
		$Sender->Title(T('Search Engine Optimization'));
		$Sender->AddSideMenu('plugin/seo');
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
	
	public function Controller_Index ( $Sender )
	{
		$Sender->Permission('Garden.Settings.Manage');
		$Sender->DynamicTitles = $this->dynamic_titles;
		$Sender->DynamicTitleTags = $this->tags;
		
		if ( $this->Enabled() )
		{
			if ( $Sender->Form->AuthenticatedPostBack() === TRUE )
			{
				foreach ( $this->dynamic_titles as $field => $info )
				{
					SaveToConfig('Plugins.SEO.DynamicTitles.'.$field, $Sender->Form->GetValue($field));
				}
				
				$Sender->StatusMessage = T('Your settings have been saved.');
			}
			
			foreach ( $this->dynamic_titles as $field => $info )
			{
				$Sender->Form->SetFormValue($field, $this->GetTitle($field));
			}
			
		}
			
		$Sender->Render($this->GetView('seo.php'));
	}
	
	public function Controller_Toggle ( $Sender )
	{
		if ( Gdn::Session()->ValidateTransientKey(GetValue(1, $Sender->RequestArgs)) )
		{
			if ( C('Plugins.SEO.Enabled') )
			{
				RemoveFromConfig('Plugins.SEO.Enabled');
			}
			else
			{
				SaveToConfig('Plugins.SEO.Enabled', TRUE);
			}
		}
		
		redirect('plugin/seo');
	}
	
	/**
	 * We don't have a way to publically get the offset from the PagerModule.
	 * Will go without for now.
	public function PagerModule_GetOffset_Create ( $Sender )
	{
		return $Sender->Offset;
	}
	*/
	
	public function CategoriesController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
		
		$data = array();
		switch ( Gdn::Dispatcher()->ControllerMethod() )
		{
			case 'all':
				$type = 'categories_all';
				break;
			default:
				if ( isset($Sender->Data['Category']) )
				{
					$type = 'category_single';
					$data['category'] = $Sender->Data('Category.Name');
					
					// Add meta description if one is available
               $CategoryDescription = $Sender->Data('Category.Description', NULL);
					if ( !is_null($CategoryDescription) )
					{
						$Sender->Head->AddTag('meta', array('name' => 'description', 'content'=> htmlspecialchars($CategoryDescription)));	
					}
				}
				else
				{
					$type = 'category_discussions';
				}
				break;
		}
		
		$this->ParseTitle($Sender, $data, $type);
	}
	
	public function ActivityController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
		
		$this->ParseTitle($Sender, '', 'activity');
	}
	
	public function SearchController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
		
		
		$Search = Gdn_Format::Text($Sender->Form->GetFormValue('Search'));
		
		// No search term? No page title.
		if ( strlen($Search) == 0 ) return;
		
		$data['search'] = $Search;		
		$this->ParseTitle($Sender, $data, 'search_results');
	}
	
	public function DiscussionsController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
		
		$data = array();
		switch ( Gdn::Dispatcher()->ControllerMethod() )
		{
			// We don't need these personal pages yet because no Search Engines visit them.
			/*
			case 'mine':
				$type = 'my_discussions';
				break;
			case 'bookmarked':
				$type = 'bookmarked_discussions';		
				break;
			*/
			
			case 'index':
			default:
				$type = 'discussions';
				break;
		}
		$this->ParseTitle($Sender, $data, $type );
	}
	
	private function ParseTitle ( &$Sender, $data, $type )
	{
		if ( !isset($this->dynamic_titles[$type]) )
			return;
		
		$dynamic = $this->dynamic_titles[$type];
		$title = $this->GetTitle($type);
		
		if ( !isset($data['garden']) )
		{
			$data['garden'] = C('Garden.Title');
		}
		
		// Only parse the field allowed for this dynamic title.
		foreach ( $dynamic['fields'] as $field )
		{
			$title = str_replace('%'.$field.'%', isset($data[$field]) ? $data[$field] : '', $title);
		}
		
		$Sender->Head->Title($title);
	}
	
	public function Enabled ()
	{
		return ( C('Plugins.SEO.Enabled') == TRUE );
	}
	
	public function DiscussionController_Render_Before ( $Sender )
	{
		if ( !$this->Enabled() )
			return;
			
		$tags = array();
		
		// Check if we have tags from current discussion
      $DiscussionTags = $Sender->Data('Discussion.Tags', NULL);
		if ( C('Plugins.Tagging.Enabled') && !is_null($DiscussionTags) )
		{
			$tags += explode(' ', $DiscussionTags);
		}
		
		// Calculate Page for Single discussion.
		/*
		 * No need for calculating pages since we aren't doing titles for multiple pages.
		$Offset = (int) $Sender->Offset;
		$Limit = (int) C('Vanilla.Comments.PerPage', 50);
		$page = (int) PageNumber($Offset, $Limit);		
		if ( $page <= 0 )
			$page = 1;
		*/
		
		array_walk($tags, 'strip_tags');
		array_walk($tags, 'trim');
		array_walk($tags, 'htmlspecialchars');
		$tags = array_unique($tags);
		if ( count($tags) > 0 )
		{
			$Sender->Head->AddTag('meta', array('name' => 'keywords', 'content' => implode(', ', $tags)));
		}		
		
		$Sender->Head->AddTag('meta', array('name' => 'description', 'content'=> $Sender->Data('Discussion.Name')));
		
		$data = array (
			'title' => $Sender->Data('Discussion.Name'),
			'category' => $Sender->Data('Discussion.Category'),
		);
		
		$type = 'discussion_single';		
		$this->ParseTitle($Sender, $data, $type);
	}
	
	public function Setup ()
	{
		// We don't need to setup because we're awesome.	
	}
}

