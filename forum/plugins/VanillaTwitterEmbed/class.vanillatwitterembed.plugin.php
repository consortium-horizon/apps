<?php

/**
* Vanilla Twitter Embed: Embed tweets directly into discussion posts by pasting the tweet URL.
* @author Jamie Chung <me@jamiechung.me>
* @homepage http://www.jamiechung.me
* @twitter @jamiechung
* @github https://github.com/JamieChung/VanillaTwitterEmbed
*/

$PluginInfo['VanillaTwitterEmbed'] = array (
	'Name' =>	'Vanilla Twitter Embed',
	'Description' => 'Embed tweets directly into discussion posts by pasting the tweet URL.',
	'Version' => '0.4',
	'RequiredApplications' => array('Vanilla' => '2.0.18'),
	'RequiredPlugins' => FALSE,
	'HasLocale' => FALSE,
	'Author' => 'Jamie Chung',
	'AuthorEmail' => 'me@jamiechung.me',
	'AuthorUrl' => 'http://www.jamiechung.me'
);

class VanillaTwitterEmbedPlugin implements Gdn_IPlugin
{
	// Regex for replace callback. Identifies a url to a individual tweet.
	private $twitterStatusRegex = '/<a(.*?)href="(http|https)\:\/\/twitter.com\/(.*?)\/status\/([0-9]+)"(.*?)>(http|https)\:\/\/twitter.com\/(.*?)\/status\/([0-9]+)<\/a>/';

	/**
	* Setup the database structure required to cache the used tweets.
	*/
	public function Setup()
	{
		Gdn::Database()->Query("CREATE TABLE IF NOT EXISTS `GDN_TwitterEmbed` (
							  `ID` int(11) NOT NULL AUTO_INCREMENT,
							  `TweetID` varchar(255) NOT NULL,
							  `Response` text NOT NULL,
							  PRIMARY KEY (`ID`),
							  KEY `TweetID` (`TweetID`)
							)");
	}

	/**
	* Destructs the database when we disable. Simple act of clearing the cache.
	*/
	public function OnDisable ()
	{
		Gdn::Database()->Query("DROP TABLE IF EXISTS `GDN_TwitterEmbed`");
	}

	/**
	* If a match is found within the body of a post, the
	* proper code is injected in place of it.
	* @param $matches array Matched results from the twitter status regex.
	*/
	protected function CreateEmbed ( $matches )
	{
		// Tweet ID is the fourth matched result.
		$id = $matches[4];
		$tweet = false;

		// We cannot force $id to be an int datatype
		// because the value is too large for PHP.
		if ( ctype_digit($id) )
		{
			// Check if the selected tweet is in the database.
			$tweet = Gdn::Database()->SQL()
						->Select('*')
						->From('TwitterEmbed')
						->Where('TweetID = ', $id)
						->Limit(1)
						->Get()
						->FirstRow();

		}

		// If we don't have the tweet, let's get it from the twitter server.
		if ( !$tweet )
		{
			// Create the twitter embed code based on the Garden Locale
			$locale = explode('-', C('Garden.Locale'));
			$lang = substr($locale[0], 0, 2);

			$api = 'http://api.twitter.com/1/statuses/oembed.json?id='.$id.'&omit_script=true&lang='.$lang;

			$response = file_get_contents($api);

			// If we can't get the proper response from the server
			// simply return the matched result (url link in post)
			if ( !$response )
			{
				return $matches[0];
			}

			// Insert the matched tweet and the cached results into the database.
			Gdn::Database()->SQL()
				->Insert('TwitterEmbed', array(
					'TweetID' => $id,
					'Response' => $response,
				));

			$response = json_decode($response);
		}
		else
		{
			$response = json_decode($tweet->Response);
		}

		return $response->html;
	}

	/**
	* Setup the callback to replace the content of the post with the oembed code.
	*/
	protected function TwitterEmbed ( $content )
	{
		$this->content = $content;
		$content = preg_replace_callback($this->twitterStatusRegex,
		                                 array($this, 'CreateEmbed'), $content);
		return $content;
	}

	/**
	* Setup the callback for every page that handles comments to a post.
	*/
	public function Base_AfterCommentFormat_Handler ( &$Sender )
	{
		$Object = $Sender->EventArguments['Object'];

		$Object->FormatBody = $this->TwitterEmbed($Object->FormatBody);
		$Sender->EventArguments['Object'] = $Object;
	}

	/**
	* Injects the twitter widget into all pages.
	*/
	public function Base_Render_Before ( $Sender )
	{
		$Sender->AddJsFile('https://platform.twitter.com/widgets.js');
	}
}