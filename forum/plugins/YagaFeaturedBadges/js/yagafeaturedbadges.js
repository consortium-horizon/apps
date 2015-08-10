/* Copyright 2014 Zachary Doll */
jQuery(document).ready(function($) {
  var $SelectedBadges = $('#SelectedBadges'),
      $AvailableBadges = $('#AvailableBadges');
  
  function updateSelection(item) {
    if($SelectedBadges.children('li').length > 3) {
      if(!$(item).hasClass('EmptyBadge') && $SelectedBadges.children('li.EmptyBadge:last').length) {
        // Remove empty badges first, unless we are trying to add an empty badge
        $SelectedBadges.children('li.EmptyBadge:last').fadeOut('fast', function() {
          $(this).remove();
          setDataFields();
        });
      }
      else {
        // Remove the last item
        $SelectedBadges.children('li:last').fadeOut('fast', function() {
          $(this).remove();
          setDataFields();
        });
      }
    }
  }
  
  function setDataFields() {
    // Pass the data-ids into the hidden form fields
    var i = 1;
    $SelectedBadges.children('li:').each( function() {
      var attrNameSelector = 'input[name="Badge'+i+'"]';
      var badgeID = $(this).attr('data-id');
      $(attrNameSelector).val(badgeID);
      i++;
    });
  }
  
  $('#FeaturedBadgeUI').show();
  $('#FeaturedBadgeFallback').hide();
  
  $SelectedBadges.sortable({
    revert: true,
    axis: 'x',
    cursor: 'move',
    containment: '#SelectedBadges',
    receive: function(event, ui) {
      updateSelection(ui.item);
    },
    update: function(event, ui) {
      setDataFields();
    }
  });
  
  $('li', $AvailableBadges).draggable({
    connectToSortable: '#SelectedBadges',
    helper: 'clone',
    revert: 'invalid',
    cursor: 'move'
  });
});
