<?php if (!defined('APPLICATION')) exit();

class ForumDonateModule extends Gdn_Module {
   
 


   public function AssetTarget() {
      return 'Panel';
   }

   public function ToString() {  
     
      echo '<div class="Box DonateBox">'; 
      echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="JUPGKSKZVQX56" />
<input type="image" src="https://www.paypal.com/fr_FR/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate" />
<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
</form>
';
 //      echo Wrap(T('Donation Box'), 'h4');
 //      echo '<ul class="PanelInfo">';
 //      echo '<p></p>';
 //      echo  T("Your Donations will help keep this forum afloat");
 //      echo '<p></p>';
     
 //        echo Anchor(Gdn_Format::Text("My donation link"), Gdn_Format::Url("/donations" ));

 // // edit the links above to the appropriate paypal or whatever donate links and image  
 //    echo Wrap(T('Item 1'), 'li');
 //    echo Wrap(T('Item 2'), 'li');
 //    echo  "</ul>";
    echo "</div>";  
   }
}
