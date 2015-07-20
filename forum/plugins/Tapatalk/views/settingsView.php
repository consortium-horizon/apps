<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<?php
//load roles
$exttMbqRoleModel = new RoleModel();
$exttMbqRoleData = $exttMbqRoleModel->GetByPermission('Garden.SignIn.Allow');
$exttMbqRoles = ConsolidateArrayValuesByKey($exttMbqRoleData->ResultArray(), 'RoleID', 'Name');
array_unshift($exttMbqRoles, "-- None --");
?>
<h1><?php echo T($this->Data['Title']); ?></h1>
      <div class="Info"><?php echo T($this->Data['PluginDescription']); ?></div>
      <table class="AltRows">
         <thead>
            <tr>
               <th style="width:200px;"><?php echo T('Title'); ?></th>
               <th style="width:300px;"><?php echo T('Value'); ?></th>
               <th><?php echo T('Description'); ?></th>
            </tr>
         </thead>
         <tbody>
               <tr>
                  <th><?php echo T('Registration Options'); ?></th>
                  <td>
                      <?php
                      echo $this->Form->DropDown('Plugin.Tapatalk.tapatalk_iar_registration_options',array(
                         1   => 'In App Registration',
                         //2   => 'Native Registration Only',
                         3   => 'Redirect to External Registration URL'
                      )); 
                      ?>
                  </td>
                  <td><?php echo T("In App Registration - Allows Tapatalk users to register your forum easily with in-app registration, Tapatalk supports all custom and required fields such as birthday control and any extra fields you requires new members to enter.<br />Redirect to External Registration URL - All users registering for your forum will be redirected to a web browser outside of the app to continue registration."); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('Registration URL'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.tapatalk_iar_registration_url'); ?></td>
                  <td><?php echo T("This field is required if you select \"Redirect to External Registration URL\" under \"Registration Options\". You do not need to include the forum root URL."); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('User Group Assignment'); ?></th>
                  <td>
                      <?php
                      echo $this->Form->DropDown('Plugin.Tapatalk.tapatalk_iar_usergroup_assignment',$exttMbqRoles); 
                      ?>
                  </td>
                  <td><?php echo T("You can assign users registered with Tapatalk to specific user groups. If you do not assign them to a specific group, they will be assigned a default group."); ?></td>
               </tr>
               <tr style="display:none;">
                  <th><?php echo T('Tapatalk Plugin Directory'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.tapatalk_directory'); ?></td>
                  <td><?php echo T("This is an advanced options. If you wish to install Tapatalk in a different directory other than the default 'mobiquo' folder, you will need to update this settings so the plugin will continue to work. Also you need update the same setting in Tapatalk Forum Owner Area. (http://tapatalk.com/landing.php). ** You still need to manually rename the 'mobiquo' directory to something else, modifying this settings does not automatically change the directory physical location."); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('Tapatalk API Key'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.tapatalk_push_key'); ?></td>
                  <td><?php echo T("Formerly known as Push Key. This key is now required for secure connection between your community and Tapatalk server. Features such as Push Notification and Single Sign-On requires this key to work. You can obtain the key from Tapatalk Forum Owner Area."); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('Mobile Welcome Screen'); ?></th>
                  <td>
                      <?php
                      echo $this->Form->DropDown('Plugin.Tapatalk.tapatalk_full_banner',array(
                         0             => 'No',
                         1   => 'Yes'
                      )); 
                      ?>
                  </td>
                  <td><?php echo T("Tapatalk will show an one time welcoming screen to mobile users to download the free app, the screen will contain your forum logo and branding only, with a button to get the free app."); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('BYO App Banner Message'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.app_banner_message'); ?></td>
                  <td><?php echo T('E.g. "Follow {your_forum_name} with {app_name} for [os_platform]". Do not change the [os_platform] tag as it is displayed dynamically based on user\'s device platform.'); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('BYO iOS App ID'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.app_ios_id'); ?></td>
                  <td><?php echo T('Enter your BYO product ID in Apple App Store, to be used on iOS device.'); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('Android Product ID'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.app_android_id'); ?></td>
                  <td><?php echo T('Enter your BYO App ID from Google Play, to be used on Android device. E.g. "com.quoord.tapatalkpro.activity".'); ?></td>
               </tr>
               <tr>
                  <th><?php echo T('Kindle Fire Product URL'); ?></th>
                  <td><?php echo $this->Form->TextBox('Plugin.Tapatalk.app_kindle_url'); ?></td>
                  <td><?php echo T('Enter your BYO App URL from Amazon App Store, to be used on Kindle Fire device. E.g. "http://www.amazon.com/gp/mas/dl/android?p=com.quoord.tapatalkpro.activity".'); ?></td>
               </tr>
         </tbody>
      </table>
<div>&nbsp;</div>
<?php echo $this->Form->Close('Save');
