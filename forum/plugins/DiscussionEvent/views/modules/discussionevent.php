<?php if (!defined('APPLICATION')) exit();

// Vanilla 2.1 compatibility:
if (!function_exists('PanelHeading')) {
  function PanelHeading($content, $attributes = '') {
    return Wrap($content, 'h4', $attributes);
  }
}

if (!function_exists('WriteDiscussionEvent')) {
  function WriteDiscussionEvent($Discussion, $Prefix = null) {
  ?>
    <span class="Title">
      <span class="MItem"><?php echo Gdn_Format::Date($Discussion->DiscussionEventDate, '%e %b'); ?></span>
      <?php echo Anchor(SliceString(Gdn_Format::Text($Discussion->Name, false), 35), DiscussionUrl($Discussion).($Discussion->CountCommentWatch > 0 ? '#Item_'.$Discussion->CountCommentWatch : ''), 'DiscussionLink'); ?>
    </span>
  <?php
  }
}
?>


<?php
  $RoleModel = new RoleModel();
  $Roles = $RoleModel->getByUserID(Gdn::Session()->UserID)->resultArray();
  foreach ($Roles as $value) {
    if ($value['RoleID'] == 11) { // test si le user a le rôle "membre du consortium"
?>
      <div class="Box BoxDiscussionEvents">
        <?php echo PanelHeading('<i class="fa fa-calendar"></i> '.t('Upcoming Events')); ?>
        <ul class="PanelInfo PanelDiscussionEvents DataList">
          <li class="<?php echo CssClass($Discussion); ?>">
          <?php
            $DiscussionArray = $this->Data('DiscussionEvents')->Result();
            if ($DiscussionArray != null) {
              foreach ($DiscussionArray as $Discussion) {
                WriteDiscussionEvent($Discussion);
              }
            } else {
              echo 'Aucun évènement...';
            }
          ?>
          </li>
        </ul>
      </div>
<?php
    }
  }
?>
