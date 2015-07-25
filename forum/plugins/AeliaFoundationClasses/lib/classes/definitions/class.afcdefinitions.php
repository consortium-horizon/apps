<?php
namespace Aelia\AFC;
if(!defined('APPLICATION')) exit();

/**
 * Constants used by ThankFrank application.
 *
 * @package ThankFrank application
 */
class Definitions {
	// @var string The definitions ID, used to group and identify the definitions.
	protected static $DefinitionsID;

	// @var array Holds a list of the application paths
	protected static $Paths = array();

	// @var array Holds a list of the application URLs
	protected static $URLs = array();

	// TODO Add error codes as constants

	// TODO Add URL arguments as constants

	/**
	 * Returns the full path corresponding to the specified key.
	 *
	 * @param key The path key.
	 * @return string
	 */
	public static function Path($key) {
		return GetValue($key, self::$Paths, '');
	}

	/**
	 * Builds and stores the paths used by the application.
	 */
	protected static function SetPaths() {
		static::$Paths['plugin'] = PATH_PLUGINS . '/' . static::$DefinitionsID;

		static::$Paths['lib'] = static::Path('plugin') . '/lib';
		static::$Paths['views'] = static::Path('plugin') . '/views';
		static::$Paths['admin_views'] = static::Path('views') . '/admin';
		static::$Paths['classes'] = static::Path('lib') . '/classes';
		static::$Paths['vendor'] = static::Path('plugin') . '/vendor';

		static::$Paths['design'] = static::Path('plugin') . '/design';
		static::$Paths['css'] = static::Path('design');
		static::$Paths['images'] = static::Path('design') . '/images';

		static::$Paths['js'] = static::Path('plugin') . '/js';
		static::$Paths['js_admin'] = static::Path('js') . '/admin';
		static::$Paths['js_frontend'] = static::Path('js') . '/frontend';
	}

	/**
	 * Builds and stores the paths specific to this plugin.
	 */
	protected static function SetPluginPaths() {
		static::$Paths['core_overrides'] = static::Path('plugin') . '/core_overrides';
	}

	/**
	 * Builds and stores the URLs used by the application.
	 */
	protected static function SetBaseURLs() {
		static::$URLs['plugin'] =  'plugin/' . strtolower(static::$DefinitionsID);

		static::$URLs['settings'] = static::URL('plugin') . '/settings';
		static::$URLs['design'] = static::URL('plugin') . '/design';
		static::$URLs['css'] = static::URL('design');
		static::$URLs['images'] = static::URL('design') . '/images';
		static::$URLs['js'] = static::URL('plugin') . '/js';
		static::$URLs['js_admin'] = static::URL('js') . '/admin';
		static::$URLs['js_frontend'] = static::URL('js') . '/frontend';
	}

	/**
	 * Adds additional URLs needed by the plugin.
	 */
	protected static function SetPluginURLs() {
		// Placeholder
		static::$URLs['overrides_list'] = static::URL('plugin') . '/overrideslist';
	}

	/**
	 * Returns the URL corresponding to the specified key.
	 *
	 * @param key The URL key.
	 * @return string
	 */
	public function URL($key) {
		return GetValue($key, static::$URLs, '');
	}

	/**
	 * Initialises the definitions class.
	 *
	 * @param string PluginID The ID of the plugin/application for which the
	 * class is instantiated.
	 */
	public static function Initialize($DefinitionsID) {
		static::$DefinitionsID = $DefinitionsID;

		static::SetPaths();
		static::SetPluginPaths();

		static::SetBaseURLs();
		static::SetPluginURLs();
	}

	const ARG_MESSAGES_KEY = 'mkey';
}

Definitions::Initialize('AeliaFoundationClasses');
