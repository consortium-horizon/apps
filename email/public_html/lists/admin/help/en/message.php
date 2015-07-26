<p>
In the message field you can use "variables", which will be replaced by the appropriate value for a subscriber:
<br />The variables need to be in the form <strong>[NAME]</strong> where NAME can be replaced with the name of one of your attributes.
<br />For example if you have an attribute "First Name" put [FIRST NAME] in the message somewhere to identify the location where the "First Name" value of the recipient needs to be inserted.</p>
<p>You can also add some text that will be used, if the subscriber has no value for this attribute. To do this, use the following syntax:
<br/><strong>[PLACEHOLDER%%Fallback words]</strong>
<br/>For example, you can start your newsletter with:
<br/><i>Dear [FIRSTNAME%%Friend],</i>
<br/>and it will insert the FIRSTNAME for those subscribers that have this value, and "Friend" for all others.
</p>

<p>Currently you have the following attributes defined:
<?php

print listPlaceHolders();

if (phplistPlugin::isEnabled('rssmanager')) {
?>
  <p>You can set up templates for messages that go out with RSS items. In order to do that click the "Scheduling" tab and indicate
  the frequency for the message. The message will then be used to send the list of items to users
  on the lists, who have that frequency set. You need to use the placeholder [RSS] in your message
  to identify where the list needs to go.</p>
<?php }
?>

<p>To send the contents of a webpage, add the following to the content of the message:<br/>
<b>[URL:</b>http://www.example.org/path/to/file.html<b>]</b></p>
<p>You can include basic user information in this URL, not attribute information:</br>
<b>[URL:</b>http://www.example.org/userprofile.php?email=<b>[</b>email<b>]]</b><br/>
</p>
