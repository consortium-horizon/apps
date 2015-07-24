original Plugin by @peregrine,Redistributed by VrijVlinder with  Peregrine's permission.

Thanks for using the Plugin.

Please go to the settings page in the dashboard for this plugin.
It is always a good idea to disable the plugin and then update to new version, and then re-enable, and check settings.




in conf/config.php


A)  If you want to exclude ONE and only ONE Role, e.g. EITHER the Applicant or Confirm Mail or some Other Role  that show up in viewing page - you can enter the roleid in config.

    You can exclude only ONE ROLE. You can enter the roleid in config and change first value to "Exclude".

    If the member has multiple roles, they will not be excluded from the results in the view page.

    $Configuration['Plugins']['MembersListEnh']['RoleID'] =  array('Exclude', '3');


B)  If you want to include Only One Role e.g. Member  you can enter the roleid in config and change first value to "Include".
     You can include ONE ROLE.

    $Configuration['Plugins']['MembersListEnh']['RoleID'] =  array('Include', '8');

C)  If you want all roles in the roletable to show up in membership view page.  Delete the $Configuration['Plugins']['MembersListEnh']['RoleID']


