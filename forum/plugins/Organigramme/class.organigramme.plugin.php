<?php defined('APPLICATION') or die;

$PluginInfo['Organigramme'] = array(
    'Name' => 'Organigramme',
    'Description' => 'L\'organigramme de la guilde.',
    'Version' => '0.1',
    'RequiredApplications' => array('Vanilla' => '>= 2.1'),
    'RequiredTheme' => false,
    'MobileFriendly' => true,
    'HasLocale' => false,
    'Author' => 'Vlad',
    'License' => 'MIT'
);



/**
 * Example for a custom page in the look and feel of any other Vanilla page.
 *
 * In order to create a custom page that is not too ugly, you would expect it to
 * have the same template like your forum and also some modules in the panel.
 * Furthermore it shouldn't have a clumsy url. So here is an example of how to
 * achieve that.
 *
 * @package Organigramme
 * @author Robin Jurinka
 * @license MIT
 */
class OrganigrammePlugin extends Gdn_Plugin {
    // This is just to make handling easy.
    // If you are toying around with this plugin, you have to keep in mind that
    // before you change one of the values, you have to disable the plugin
    // (to delete the current route), then change the values below and then
    // reenable the plugin to set the new route.
    const SHORT_ROUTE = 'organigramme';
    const LONG_ROUTE = 'vanilla/organigramme';

    // Normally you wouldn't use constants for all that but use the words you
    // need right in the cade. I've chosen to do it that way, because I think it
    // makes code reading easier.
    const PAGE_NAME = 'Organigramme';

    /**
     * Setup is run whenever plugin is enabled.
     *
     * This is the best place to create a custom route for our page. That will
     * make a pretty url for a otherwise clumsy slug.
     *
     * @return void.
     * @package Organigramme
     * @since 0.1
     */
    public function setup () {
        // get a reference to Vanillas routing class
        $router = Gdn::Router();

        // this is the ugly slug we want to change
        $pluginPage = self::LONG_ROUTE.'$1';

        // that's how the nice url should look like
        $newRoute = '^'.self::SHORT_ROUTE.'(/.*)?$';

        // "route 'yourforum.com/vanillacontroller/organigramme' to
        // 'yourforum.com/fancyShortName'"
        if (!$router->matchRoute($newRoute)) {
            $router->setRoute($newRoute, $pluginPage, 'Internal');
        }
    }

    /**
     * OnDisable is run whenever plugin is disabled.
     *
     * We have to delete our internal route because our custom page will not be
     * accessible any more.
     *
     * @return void.
     * @package Organigramme
     * @since 0.1
     */
    public function onDisable () {
        // easy as that:
        Gdn::Router()-> DeleteRoute('^'.self::SHORT_ROUTE.'(/.*)?$');
    }

    /**
     * Create a new page that uses the current theme.
     *
     * By extending the Vanilla controller, we have access to all resources
     * that we need.
     *
     * @param object $sender VanillaController.
     * @param mixed $args Arguments for our function passed in the url.
     * @return void.
     * @package Organigramme
     * @since 0.1
     */
    public function vanillaController_Organigramme_create ($sender, $args, $Limit=5, $Offset=0, $SortOrder='asc', $UserField='Name') {
        // That one is critical! The template of your theme is called
        // default.master.tpl and calling this function sets the master view of
        // this controller to the default theme template.
        $sender->masterView();

        // Set route
        $sender->SelfUrl = self::SHORT_ROUTE;

        // Add custom CSS
        $sender->addCssFile('organigramme.css', 'plugins/Organigramme');
        // $sender->addJsFile('organigramme.js', 'plugins/Organigramme');



        // There is a list of which modules to add to the panel for a standard
        // Vanilla page. We will add all of them, just to be sure our new page
        // looks familiar to the users.
        foreach (c('Modules.Vanilla.Panel') as $module) {
            // We have to exclude the MeModule here, because it is already added
            // by the template and it would appear twice otherwise.
            if ($module != 'MeModule') {
                $sender->addModule($module);
            }
        }

        // We can set a title for the page like that. But this is just a short
        // form for $sender->setData('Title', 'Vanilla Page');
        $sender->title(t(self::PAGE_NAME));

        // This sets the breadcrumb to our current page.
        $sender->setData('Breadcrumbs', array(array('Name' => t(self::PAGE_NAME), 'Url' => self::SHORT_ROUTE)));

        // If you would like to pass some other data to your view, you should do
        // it with setData. Let's do a "Hello World"...
        if ($args[0] != '') {
            // We will use this for a conditional output.
            $sender->setData('hasArguments', true);
            // If we have a parameter use this.
            $name = $args[0];
        } else {
            // We will use this for a conditional output.
            $sender->setData('hasArguments', false);

            $session = Gdn::session();
            if ($session->isValid()) {
                // If user is logged in, get his name.
                $name = $session->User->Name;
            } else {
                // No parameter and no user name? We determine a name by ourselves
                $name = t('Anonymous');
            }
        }

        $MembersListEnhModel = new Gdn_Model('User');
        $SQL = $MembersListEnhModel->SQL
        ->Select('*')
        ->From('User u')
        ->LeftJoin('UserRole ur', 'u.UserID = ur.UserID');
        if (C('EnabledPlugins.KarmaBank') == TRUE)
           $SQL->LeftJoin('KarmaBankBalance kb', 'u.UserID = kb.UserID');
        if (($UserField != "Balance"))
           $SQL->OrderBy("u.$UserField",$SortOrder);
        if ((C('EnabledPlugins.KarmaBank') == TRUE) && ($UserField == "Balance"))
            $SQL->OrderBy("kb.Balance",$SortOrder);
        $SQL->Where('Deleted',false);
        if ($RoleAction == "Exclude")
           $SQL->Where('ur.RoleID<>',$RoleActionID);
        if ($RoleAction == "Include")
            $SQL->Where('ur.RoleID',$RoleActionID);
            // Only show user once if in more than one role.
            $SQL->GroupBy('ur.UserID');

        $sender->UserData = $SQL->Get();
        RoleModel::SetUserRoles($sender->UserData->Result());

        $admins = array();
        $conseillers = array();
        $modos = array();
        $refPS2 = array();
        $refArma = array();
        $refSkyforge = array();
        $refSC = array();
        $refAlbion = array();

        $adminspics = array();
        $conseillerspics = array();
        $modospics = array();
        $refPS2pics = array();
        $refArmapics = array();
        $refSkyforgepics = array();
        $refSCpics = array();
        $refAlbionpics = array();

        foreach ($sender->UserData as $key => $value) {
            if (in_array('Administrateur',$value->Roles)) {
                array_push($admins, $value);
                array_push($adminspics, UserPhoto($value));
            }

            if (in_array('Conseiller',$value->Roles)) {
                array_push($conseillers, $value);
                array_push($conseillerspics, UserPhoto($value));
            }

            if (in_array('Modérateur global',$value->Roles)) {
                array_push($modos, $value);
                array_push($modospics, UserPhoto($value));
            }

            if (in_array('Référent Planetside 2',$value->Roles)) {
                array_push($refPS2, $value);
                array_push($refPS2pics, UserPhoto($value));
            }

            if (in_array('Référent Arma 3',$value->Roles)) {
                array_push($refArma, $value);
                array_push($refArmapics, UserPhoto($value));
            }

            if (in_array('Référent Albion Online',$value->Roles)) {
                array_push($refAlbion, $value);
                array_push($refAlbionpics, UserPhoto($value));
            }

            if (in_array('Référent Star Citizen',$value->Roles)) {
                array_push($refSC, $value);
                array_push($refSCpics, UserPhoto($value));
            }

            if (in_array('Référent Skyforge',$value->Roles)) {
                array_push($refSkyforge, $value);
                array_push($refSkyforgepics, UserPhoto($value));
            }
        }

        // Pass the member data.
        $sender->setData('admins', $admins);
        $sender->setData('conseillers', $conseillers);
        $sender->setData('modos', $modos);
        $sender->setData('name', $name);
        $sender->setData('refPS2', $refPS2);
        $sender->setData('refAlbion', $refAlbion);
        $sender->setData('refArma', $refArma);
        $sender->setData('refSC', $refSC);
        $sender->setData('refSkyforge', $refSkyforge);

        // The the profile pics
        $sender->setData('adminspics', $adminspics);
        $sender->setData('conseillerspics', $conseillerspics);
        $sender->setData('modospics', $modospics);
        $sender->setData('namepics', $namepics);
        $sender->setData('refPS2pics', $refPS2pics);
        $sender->setData('refAlbionpics', $refAlbionpics);
        $sender->setData('refArmapics', $refArmapics);
        $sender->setData('refSCpics', $refSCpics);
        $sender->setData('refSkyforgepics', $refSkyforgepics);

        $sender->Render(parent::getView('organigramme.php'));
    }
}
