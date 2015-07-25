<?php if(!defined('APPLICATION')) exit();
/**
 * Overrides used by Foundation Plugin.
 *
 */

\Aelia\OverridesManager::Initialize();

$OriginalFactoryOverwrite = Gdn::FactoryOverwrite(1);
Gdn::FactoryInstall(Gdn::AliasSession, '\Aelia\Session');
Gdn::FactoryInstall('Identity', '\Aelia\CookieIdentity');

Gdn::FactoryOverwrite($OriginalFactoryOverwrite);
